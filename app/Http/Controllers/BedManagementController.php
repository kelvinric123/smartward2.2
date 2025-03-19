<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BedManagementController extends Controller
{
    /**
     * Display the bed management dashboard.
     */
    public function dashboard()
    {
        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $availableBeds = Bed::where('status', 'available')->count();
        $maintenanceBeds = Bed::where('status', 'maintenance')->count();
        $cleaningBeds = Bed::where('status', 'cleaning')->count();
        $reservedBeds = Bed::where('status', 'reserved')->count();
        
        $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;
        
        $wards = Ward::withCount(['beds', 'admissions' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        $activeAdmissions = Admission::with(['patient', 'bed', 'ward'])
            ->where('status', 'active')
            ->orderBy('admission_date', 'desc')
            ->limit(10)
            ->get();

        $wardsData = $wards->map(function ($ward) {
            return [
                'name' => $ward->name,
                'total' => $ward->beds_count,
                'occupied' => $ward->admissions_count,
                'available' => $ward->beds_count - $ward->admissions_count,
            ];
        });

        return view('bed-management.dashboard', compact(
            'totalBeds',
            'occupiedBeds',
            'availableBeds',
            'maintenanceBeds',
            'cleaningBeds',
            'reservedBeds',
            'occupancyRate',
            'wards',
            'wardsData',
            'activeAdmissions'
        ));
    }

    /**
     * Show the bed map overview.
     */
    public function bedMap(Request $request)
    {
        $wardId = $request->input('ward_id');
        $consultantId = $request->input('consultant_id');
        $subsection = $request->input('subsection');
        
        // Get all consultants for the filter dropdown
        $consultants = \App\Models\Consultant::orderBy('name')->get();
        
        if ($wardId) {
            $ward = Ward::with(['beds' => function ($query) {
                $query->orderBy('bed_number');
            }])->findOrFail($wardId);
            
            // Eager load active admissions with patient and consultant
            foreach ($ward->beds as $bed) {
                $bed->setRelation('currentAdmission', 
                    $bed->admissions()->with(['patient', 'consultant'])->where('status', 'active')->first()
                );
            }
            
            $beds = $ward->beds;
            
            // Calculate subsections if the ward has more than 12 beds
            $subsections = [];
            if ($beds->count() > 12) {
                $bedsPerSubsection = 12;
                $totalSubsections = ceil($beds->count() / $bedsPerSubsection);
                
                for ($i = 0; $i < $totalSubsections; $i++) {
                    $subsectionName = 'Section ' . ($i + 1);
                    $subsections[$i] = $subsectionName;
                }
                
                // If a subsection is selected, filter the beds accordingly
                if ($subsection !== null && $subsection < $totalSubsections) {
                    $start = $subsection * $bedsPerSubsection;
                    $end = min(($subsection + 1) * $bedsPerSubsection - 1, $beds->count() - 1);
                    $beds = $beds->slice($start, $end - $start + 1);
                } else {
                    // Default to first subsection if not specified and there are subsections
                    $subsection = 0;
                    $beds = $beds->slice(0, $bedsPerSubsection);
                }
            }
            
            // Filter beds by consultant if specified
            if ($consultantId) {
                $beds = $beds->filter(function ($bed) use ($consultantId) {
                    return $bed->currentAdmission && 
                           $bed->currentAdmission->consultant_id == $consultantId;
                });
            }
            
            $wards = Ward::all();
            
            // Get nurses on duty for this ward
            $nursesOnDuty = \App\Models\Nurse::where('ward_assignment', $ward->name)
                ->where('status', 'On Duty')
                ->get();
            
            // Calculate patient-to-nurse ratio
            $occupiedBedsCount = $beds->where('status', 'occupied')->count();
            $nursesCount = $nursesOnDuty->count();
            $patientNurseRatio = $nursesCount > 0 ? round($occupiedBedsCount / $nursesCount, 1) : 0;
            
            return view('bed-management.bed-map', compact('ward', 'beds', 'wards', 'nursesOnDuty', 
                'patientNurseRatio', 'consultants', 'consultantId', 'subsections', 'subsection', 'wardId'));
        } else {
            $wards = Ward::with(['beds' => function ($query) {
                $query->orderBy('bed_number');
            }])->get();
            
            // Eager load active admissions with patient and consultant for each bed
            foreach ($wards as $ward) {
                foreach ($ward->beds as $bed) {
                    $bed->setRelation('currentAdmission', 
                        $bed->admissions()->with(['patient', 'consultant'])->where('status', 'active')->first()
                    );
                }
                
                // Filter beds by consultant if specified
                if ($consultantId) {
                    $filteredBeds = $ward->beds->filter(function ($bed) use ($consultantId) {
                        return $bed->currentAdmission && 
                               $bed->currentAdmission->consultant_id == $consultantId;
                    });
                    $ward->setRelation('beds', $filteredBeds);
                }
            }
            
            // Get ward stats including nurses on duty and patient-to-nurse ratios
            $wardStats = collect();
            foreach ($wards as $ward) {
                $nursesOnDuty = \App\Models\Nurse::where('ward_assignment', $ward->name)
                    ->where('status', 'On Duty')
                    ->get();
                
                $occupiedBedsCount = $ward->beds->where('status', 'occupied')->count();
                $nursesCount = $nursesOnDuty->count();
                
                $wardStats->put($ward->id, [
                    'nursesOnDuty' => $nursesOnDuty,
                    'nursesCount' => $nursesCount,
                    'patientNurseRatio' => $nursesCount > 0 ? round($occupiedBedsCount / $nursesCount, 1) : 0
                ]);
            }
            
            return view('bed-management.bed-map', compact('wards', 'wardStats', 'consultants', 'consultantId'));
        }
    }

    /**
     * Find a bed for a patient based on criteria.
     */
    public function findBed(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'nullable|in:male,female,other',
            'ward_type' => 'nullable|in:general,intensive_care,emergency,maternity,pediatric,psychiatric,surgery,other',
            'bed_type' => 'nullable|in:standard,electric,bariatric,pediatric,intensive_care,other',
        ]);
        
        $query = Bed::with('ward')
            ->where('status', 'available');
            
        if (isset($validated['bed_type'])) {
            $query->where('type', $validated['bed_type']);
        }
        
        if (isset($validated['ward_type'])) {
            $query->whereHas('ward', function ($q) use ($validated) {
                $q->where('type', $validated['ward_type']);
            });
        }
        
        $availableBeds = $query->get();
        $wards = Ward::all();
        $bedTypes = ['standard', 'electric', 'bariatric', 'pediatric', 'intensive_care', 'other'];
        $wardTypes = ['general', 'intensive_care', 'emergency', 'maternity', 'pediatric', 'psychiatric', 'surgery', 'other'];
        
        return view('bed-management.find-bed', compact(
            'availableBeds',
            'wards',
            'bedTypes',
            'wardTypes',
            'validated'
        ));
    }

    /**
     * Admit a patient to a specific bed.
     */
    public function admitToBed(Request $request, Bed $bed)
    {
        // Only allow admission to beds with 'available' status
        if ($bed->status !== 'available') {
            return back()->with('error', 'Bed is not available for admission. Only beds with "available" status can be used for admission.');
        }
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'admission_date' => 'required|date',
            'expected_discharge_date' => 'nullable|date|after_or_equal:admission_date',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'consultant_id' => 'nullable|exists:consultants,id',
        ]);
        
        $patient = Patient::findOrFail($validated['patient_id']);
        
        // Check if the patient already has an active admission
        if (Admission::patientHasActiveAdmission($patient->id)) {
            return back()->with('error', 'This patient already has an active admission. A patient cannot have multiple active admissions simultaneously. Please discharge the patient from their current location before admitting them to a new bed.');
        }
        
        $admission = new Admission([
            'patient_id' => $patient->id,
            'bed_id' => $bed->id,
            'ward_id' => $bed->ward_id,
            'admission_date' => $validated['admission_date'],
            'expected_discharge_date' => $validated['expected_discharge_date'] ?? null,
            'status' => 'active',
            'consultant_id' => $validated['consultant_id'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        $admission->save();
        
        // Update bed status to 'occupied'
        $bed->update(['status' => 'occupied']);
        
        return redirect()->route('beds.show', $bed)
            ->with('success', 'Patient successfully admitted to bed.');
    }

    /**
     * Get bed statistics and analytics.
     */
    public function statistics(Request $request)
    {
        $timeRange = $request->input('timeRange', '90days'); // Default to 90 days if not specified
        
        $totalAdmissions = Admission::count();
        $currentAdmissions = Admission::where('status', 'active')->count();
        
        // Average length of stay
        $avgLosResult = DB::select("
            SELECT AVG(DATEDIFF(
                COALESCE(actual_discharge_date, NOW()),
                admission_date
            )) as avg_los
            FROM admissions
            WHERE actual_discharge_date IS NOT NULL
            OR status = 'active'
        ");
        $avgLos = $avgLosResult[0]->avg_los ?? 0;
        
        // Set time period based on selected range
        switch ($timeRange) {
            case 'month':
                $startDate = now()->subMonth();
                $timeRangeTitle = 'Last Month';
                $groupBy = 'day'; // Group by day for monthly view
                break;
            case 'year':
                $startDate = now()->subYear();
                $timeRangeTitle = 'Last Year';
                $groupBy = 'month'; // Group by month for yearly view
                break;
            case '90days':
            default:
                $startDate = now()->subDays(90);
                $timeRangeTitle = 'Last 90 Days';
                $groupBy = 'day'; // Group by day for 90 days view
                break;
        }

        // Build the select based on grouping
        if ($groupBy === 'day') {
            $dateFormat = '%Y-%m-%d';
            $formattedDateFormat = '%d/%m/%Y';
        } else {
            $dateFormat = '%Y-%m';
            $formattedDateFormat = '%m/%Y';
        }
        
        // Get admission data grouped by the selected time period
        $occupancyRateData = DB::table('admissions')
            ->select(
                DB::raw("DATE_FORMAT(admission_date, '$dateFormat') as date"),
                DB::raw("DATE_FORMAT(admission_date, '$formattedDateFormat') as formatted_date"),
                DB::raw('COUNT(*) as admissions'),
                DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active'),
                DB::raw('SUM(CASE WHEN actual_discharge_date IS NOT NULL THEN 1 ELSE 0 END) as discharges')
            )
            ->where('admission_date', '>=', $startDate)
            ->groupBy('date', 'formatted_date')
            ->orderBy('date')
            ->get();
        
        // Ward occupancy rates
        $wardOccupancy = Ward::withCount(['beds', 'admissions' => function ($query) {
            $query->where('status', 'active');
        }])->get()->map(function ($ward) {
            $rate = $ward->beds_count > 0 ? ($ward->admissions_count / $ward->beds_count) * 100 : 0;
            return [
                'ward' => $ward->name,
                'rate' => round($rate, 2),
                'occupied' => $ward->admissions_count,
                'total' => $ward->beds_count,
            ];
        })->sortByDesc('rate')->values();
        
        return view('bed-management.statistics', compact(
            'totalAdmissions',
            'currentAdmissions',
            'avgLos',
            'occupancyRateData',
            'wardOccupancy',
            'timeRange',
            'timeRangeTitle'
        ));
    }

    /**
     * Display the details of a specific bed.
     */
    public function showBed(Bed $bed)
    {
        // Load related data
        $bed->load(['ward', 'currentAdmissionRelation.patient']);
        
        // Get bed history - past admissions
        $pastAdmissions = Admission::where('bed_id', $bed->id)
            ->where(function($query) {
                $query->where('status', '!=', 'active')
                      ->orWhereNull('status');
            })
            ->with('patient')
            ->orderBy('admission_date', 'desc')
            ->limit(10)
            ->get();
        
        return view('bed-management.bed-details', compact('bed', 'pastAdmissions'));
    }

    /**
     * Mark a bed as available.
     */
    public function markAvailable(Bed $bed)
    {
        // Check if there's an active admission
        $activeAdmission = $bed->currentAdmission();
        
        if ($activeAdmission) {
            // If there's an active admission, discharge the patient first
            $activeAdmission->update([
                'status' => 'discharged',
                'actual_discharge_date' => now(),
            ]);
            
            // Update the bed status to cleaning after discharge
            $bed->update(['status' => 'cleaning']);
            
            return redirect()->back()->with('success', 'Patient discharged and bed marked for cleaning.');
        }
        
        // If the bed is in cleaning status, don't allow direct change to available through this method
        if ($bed->status === 'cleaning') {
            return redirect()->back()->with('error', 'Please mark cleaning as complete to make bed available.');
        }
        
        // Only allow changing to available if the bed is not in maintenance or reserved status
        if ($bed->status === 'maintenance' || $bed->status === 'reserved') {
            return redirect()->back()->with('error', 'Cannot automatically change bed from ' . $bed->status . ' status. Please use Edit Bed to update status.');
        }
        
        // Update the bed status to available
        $bed->update(['status' => 'available']);
        
        return redirect()->back()->with('success', 'Bed marked as available successfully.');
    }
    
    /**
     * Mark bed cleaning as complete.
     */
    public function markCleaningComplete(Bed $bed)
    {
        // Only allow this action if the bed is in cleaning status
        if ($bed->status !== 'cleaning') {
            return redirect()->back()->with('error', 'This action is only valid for beds in cleaning status.');
        }
        
        // Update the bed status to available
        $bed->update(['status' => 'available']);
        
        return redirect()->back()->with('success', 'Bed cleaning completed and marked as available.');
    }

    /**
     * Show form to admit a patient to a bed.
     */
    public function admitForm(Bed $bed)
    {
        // Check if bed is available
        if ($bed->status !== 'available') {
            return redirect()->route('beds.show', $bed)
                ->with('error', 'This bed is not available for admission.');
        }
        
        // Get all patients who are not currently admitted
        $patients = Patient::whereDoesntHave('admissions', function($query) {
            $query->where('status', 'active');
        })->orderBy('last_name')->get();
        
        // Get consultants instead of doctors
        $consultants = \App\Models\Consultant::orderBy('name')->get();
        
        return view('bed-management.admit-form', compact('bed', 'patients', 'consultants'));
    }

    /**
     * Show details of a specific admission.
     */
    public function showAdmission(Request $request, Admission $admission)
    {
        $admission->load(['patient', 'bed.ward', 'consultant']);
        
        // Get vital signs
        $vitalSigns = $admission->vitalSigns()->orderBy('created_at', 'desc')->get();
        
        // Get navigation parameters for the back button
        $from = $request->input('from');
        $wardId = $request->input('ward_id');
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
        }
        
        return view('admissions.show', compact('admission', 'vitalSigns', 'backUrl', 'from', 'wardId', 'subsection', 'consultantId'));
    }

    /**
     * Show form to edit a bed.
     */
    public function editBed(Bed $bed)
    {
        $bed->load('ward');
        $wards = Ward::all();
        $bedTypes = ['standard', 'electric', 'bariatric', 'pediatric', 'intensive_care', 'other'];
        $statuses = ['available', 'occupied', 'maintenance', 'cleaning', 'reserved'];
        
        return view('bed-management.edit-bed', compact('bed', 'wards', 'bedTypes', 'statuses'));
    }
    
    /**
     * Update a bed's details.
     */
    public function updateBed(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:10',
            'type' => 'required|in:standard,electric,bariatric,pediatric,intensive_care,other',
            'status' => 'required|in:available,occupied,maintenance,cleaning,reserved',
            'notes' => 'nullable|string',
        ]);
        
        // Always use the existing ward_id
        $validated['ward_id'] = $bed->ward_id;
        
        // Status transition rules
        $newStatus = $validated['status'];
        $currentStatus = $bed->status;
        
        // Cannot manually set a bed as "occupied" - this happens only through admission
        if ($newStatus === 'occupied' && $currentStatus !== 'occupied') {
            return back()->with('error', 'Cannot manually set bed as occupied. Use the admit patient function instead.')->withInput();
        }
        
        // If the bed is occupied, only allow transitioning to cleaning (during discharge)
        if ($currentStatus === 'occupied' && $newStatus !== 'occupied') {
            // Check if there's an active admission
            $activeAdmission = $bed->currentAdmission();
            if ($activeAdmission) {
                if ($newStatus !== 'cleaning') {
                    return back()->with('error', 'An occupied bed can only transition to cleaning status during discharge.')->withInput();
                }
                
                // Discharge the patient if moving to cleaning
                $activeAdmission->update([
                    'status' => 'discharged',
                    'actual_discharge_date' => now(),
                ]);
            }
        }
        
        // Check for unique bed number within the ward
        if ($request->bed_number != $bed->bed_number) {
            $exists = Bed::where('ward_id', $bed->ward_id)
                ->where('bed_number', $request->bed_number)
                ->where('id', '!=', $bed->id)
                ->exists();
                
            if ($exists) {
                return back()->withErrors(['bed_number' => 'A bed with this number already exists in the selected ward.'])->withInput();
            }
        }
        
        $bed->update($validated);
        
        return redirect()->route('beds.show', $bed)->with('success', 'Bed updated successfully.');
    }
} 