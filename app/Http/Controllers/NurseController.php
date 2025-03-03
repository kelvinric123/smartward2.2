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
            'shiftOptions' => Nurse::getShiftOptions(),
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
            'shift' => 'required|string|in:' . implode(',', Nurse::getShiftOptions()),
            'status' => 'required|string|in:' . implode(',', Nurse::getStatusOptions()),
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'employment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

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
            'shiftOptions' => Nurse::getShiftOptions(),
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
            'shift' => 'required|string|in:' . implode(',', Nurse::getShiftOptions()),
            'status' => 'required|string|in:' . implode(',', Nurse::getStatusOptions()),
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'employment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

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
            ->with('success', 'Nurse deactivated successfully.');
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
} 