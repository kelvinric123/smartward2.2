<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NurseController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the nurses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $nurses = Nurse::orderBy('name')->get();
        $morningShiftNurses = Nurse::where('shift', 'Morning')->get();
        $eveningShiftNurses = Nurse::where('shift', 'Evening')->get();
        $nightShiftNurses = Nurse::where('shift', 'Night')->get();
        
        return view('nurses.index', [
            'nurses' => $nurses,
            'morningShiftNurses' => $morningShiftNurses,
            'eveningShiftNurses' => $eveningShiftNurses,
            'nightShiftNurses' => $nightShiftNurses,
            'shiftOptions' => Nurse::getShiftOptions(),
            'statusOptions' => Nurse::getStatusOptions(),
        ]);
    }

    /**
     * Show the form for creating a new nurse.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('nurses.create', [
            'statusOptions' => Nurse::getStatusOptions(),
            'wards' => Ward::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created nurse in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'ward_assignment' => 'required|string|max:255',
            'status' => 'required|string|in:' . implode(',', Nurse::getStatusOptions()),
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'employment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Add default value for shift field
        $validated['shift'] = 'Custom';

        Nurse::create($validated);

        return redirect()->route('nurses.index')
            ->with('success', 'Nurse created successfully.');
    }

    /**
     * Display the specified nurse.
     *
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\View\View
     */
    public function show(Nurse $nurse)
    {
        return view('nurses.show', compact('nurse'));
    }

    /**
     * Show the form for editing the specified nurse.
     *
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\View\View
     */
    public function edit(Nurse $nurse)
    {
        return view('nurses.edit', [
            'nurse' => $nurse,
            'statusOptions' => Nurse::getStatusOptions(),
            'wards' => Ward::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified nurse in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Nurse $nurse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'ward_assignment' => 'required|string|max:255',
            'status' => 'required|string|in:' . implode(',', Nurse::getStatusOptions()),
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'employment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Keep the existing shift value when updating
        $validated['shift'] = $nurse->shift;

        $nurse->update($validated);

        return redirect()->route('nurses.index')
            ->with('success', 'Nurse updated successfully.');
    }

    /**
     * Deactivate the specified nurse.
     *
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Nurse $nurse)
    {
        $nurse->update(['status' => 'Off Duty']);
        
        return redirect()->route('nurses.index')
            ->with('success', 'Nurse has been deactivated successfully');
    }

    /**
     * Remove the specified nurse from storage.
     *
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Nurse $nurse)
    {
        $nurse->delete();

        return redirect()->route('nurses.index')
            ->with('success', 'Nurse deleted successfully.');
    }

    /**
     * Assign a nurse to a patient.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignToPatient(Request $request)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'patient_id' => 'required|exists:patients,id',
            'redirect_to' => 'nullable|string'
        ]);
        
        $nurse = Nurse::findOrFail($validated['nurse_id']);
        $patient = \App\Models\Patient::findOrFail($validated['patient_id']);
        
        // Check if the nurse is already assigned to this patient
        if (!$nurse->patients()->where('patient_id', $patient->id)->exists()) {
            $nurse->patients()->attach($patient->id);
            $message = "Nurse {$nurse->name} has been assigned to patient {$patient->full_name}";
        } else {
            $message = "Nurse {$nurse->name} is already assigned to patient {$patient->full_name}";
        }
        
        // Redirect back to referring page or to a default page
        $redirectTo = $validated['redirect_to'] ?? route('patients.show', $patient);
        
        return redirect($redirectTo)
            ->with('success', $message);
    }
    
    /**
     * Unassign a nurse from a patient.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unassignFromPatient(Request $request)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'patient_id' => 'required|exists:patients,id',
            'redirect_to' => 'nullable|string'
        ]);
        
        $nurse = Nurse::findOrFail($validated['nurse_id']);
        $patient = \App\Models\Patient::findOrFail($validated['patient_id']);
        
        $nurse->patients()->detach($patient->id);
        
        // Redirect back to referring page or to a default page
        $redirectTo = $validated['redirect_to'] ?? route('patients.show', $patient);
        
        return redirect($redirectTo)
            ->with('success', "Nurse {$nurse->name} has been unassigned from patient {$patient->full_name}");
    }
} 