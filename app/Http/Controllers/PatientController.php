<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $patients = Patient::search($search)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();
        
        return view('patients.index', compact('patients', 'search'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female,other',
            'mrn' => 'required|string|max:50|unique:patients,mrn',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient = Patient::create($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Request $request, Patient $patient)
    {
        $patient->load(['admissions' => function ($query) {
            $query->with(['bed.ward', 'transferHistory' => function($query) {
                $query->with(['fromWard', 'fromBed', 'toWard', 'toBed', 'transferredBy'])
                      ->orderBy('transfer_date', 'desc');
            }]);
        }]);
        
        // Get navigation parameters for the back button
        $from = $request->input('from');
        $wardId = $request->input('ward_id');
        $bedId = $request->input('bed_id');
        $subsection = $request->input('subsection');
        $consultantId = $request->input('consultant_id');
        
        // Construct back URL if coming from bed-map
        $backUrl = null;
        if ($from === 'bed-map' && $wardId) {
            $params = [
                'ward_id' => $wardId
            ];
            
            if ($subsection !== null) {
                $params['subsection'] = $subsection;
            }
            
            if ($consultantId) {
                $params['consultant_id'] = $consultantId;
            }
            
            $backUrl = route('bed-management.bed-map', $params);
        } elseif ($from === 'bed-details' && $bedId) {
            // If coming from a specific bed details page
            $backUrl = route('beds.show', $bedId);
        }
        
        return view('patients.show', compact('patient', 'backUrl', 'from', 'wardId', 'bedId', 'subsection', 'consultantId'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female,other',
            'mrn' => [
                'required',
                'string',
                'max:50',
                Rule::unique('patients')->ignore($patient->id),
            ],
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Patient $patient)
    {
        // Check if the patient has any active admissions
        if ($patient->admissions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a patient with active admissions.');
        }

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Transfer a patient to a different ward.
     */
    public function transferWard(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'admission_id' => 'required|exists:admissions,id',
            'ward_id' => 'required|exists:wards,id',
            'bed_id' => 'required|exists:beds,id',
            'transfer_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            // Get the admission and verify it belongs to this patient
            $admission = Admission::findOrFail($validated['admission_id']);
            
            if ($admission->patient_id !== $patient->id) {
                return back()->with('error', 'The admission does not belong to this patient.');
            }
            
            if ($admission->status !== 'active') {
                return back()->with('error', 'Only active admissions can be transferred.');
            }
            
            // Check if there are other active admissions for this patient (there shouldn't be)
            $otherActiveAdmissions = Admission::where('patient_id', $patient->id)
                ->where('status', 'active')
                ->where('id', '!=', $admission->id)
                ->get();
                
            if ($otherActiveAdmissions->isNotEmpty()) {
                // Log this unusual situation
                \Illuminate\Support\Facades\Log::warning("Patient {$patient->id} has multiple active admissions during transfer attempt", [
                    'patient_id' => $patient->id,
                    'transferring_admission_id' => $admission->id,
                    'other_admission_ids' => $otherActiveAdmissions->pluck('id')->toArray()
                ]);
                
                // Auto-fix by discharging other admissions
                foreach ($otherActiveAdmissions as $otherAdmission) {
                    $otherAdmission->update([
                        'status' => 'discharged',
                        'actual_discharge_date' => now(),
                        'notes' => ($otherAdmission->notes ? $otherAdmission->notes . "\n\n" : '') . 
                                  "Auto-discharged by system due to duplicate active admissions during transfer of admission ID {$admission->id}."
                    ]);
                    
                    // Update the bed status if applicable
                    if ($otherAdmission->bed) {
                        $otherAdmission->bed->update(['status' => 'cleaning']);
                    }
                }
            }
            
            // Get the new bed and verify it's available
            $newBed = Bed::findOrFail($validated['bed_id']);
            
            if ($newBed->status !== 'available') {
                return back()->with('error', 'The selected bed is not available.');
            }
            
            // Store original bed info for reference
            $originalBed = $admission->bed;
            $originalWard = $admission->ward;
            
            // Perform the transfer
            $admission->transfer($newBed, $validated['transfer_notes'] ?? 'Patient transferred to another ward');
            
            DB::commit();
            
            // Redirect back with a success message
            $queryParams = [];
            if ($request->has('from')) {
                $queryParams['from'] = $request->from;
            }
            if ($request->has('ward_id')) {
                $queryParams['ward_id'] = $request->ward_id;
            }
            
            $redirectUrl = route('patients.show', $patient);
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            
            return redirect($redirectUrl)
                ->with('success', "Patient successfully transferred from {$originalWard->name} to {$newBed->ward->name}");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while transferring the patient: ' . $e->getMessage());
        }
    }

    /**
     * Get a list of wards with available beds.
     */
    public function getAvailableWards()
    {
        $wards = Ward::select('id', 'name', 'code', 'type')
            ->withCount(['beds as occupied_beds' => function ($query) {
                $query->where('status', 'occupied');
            }])
            ->withCount(['beds as available_beds' => function ($query) {
                $query->where('status', 'available');
            }])
            ->withCount('beds as total_beds')
            ->having('available_beds', '>', 0)
            ->orderBy('name')
            ->get();
        
        return response()->json($wards);
    }

    /**
     * Get available beds for a specific ward.
     */
    public function getAvailableBeds(Ward $ward)
    {
        $beds = $ward->beds()
            ->where('status', 'available')
            ->select('id', 'bed_number', 'type', 'status')
            ->orderBy('bed_number')
            ->get();
        
        return response()->json($beds);
    }
} 