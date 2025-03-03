<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BedController extends Controller
{
    /**
     * Display a listing of the beds.
     */
    public function index(Request $request)
    {
        $query = Bed::with('ward');
        
        // Filter by ward if specified
        if ($request->has('ward_id')) {
            $query->where('ward_id', $request->ward_id);
        }
        
        // Filter by status if specified
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $beds = $query->get();
        $wards = Ward::all();
        
        return view('beds.index', compact('beds', 'wards'));
    }

    /**
     * Show the form for creating a new bed.
     */
    public function create(Request $request)
    {
        $wards = Ward::all();
        $selectedWard = $request->has('ward_id') ? Ward::findOrFail($request->ward_id) : null;
        
        return view('beds.create', compact('wards', 'selectedWard'));
    }

    /**
     * Store a newly created bed in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bed_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('beds')->where(function ($query) use ($request) {
                    return $query->where('ward_id', $request->ward_id);
                }),
            ],
            'ward_id' => 'required|exists:wards,id',
            'status' => [
                'required',
                Rule::in(['available', 'occupied', 'reserved', 'maintenance', 'cleaning']),
            ],
            'type' => [
                'required',
                Rule::in(['standard', 'electric', 'bariatric', 'pediatric', 'intensive_care', 'other']),
            ],
            'notes' => 'nullable|string',
        ]);

        $bed = Bed::create($validated);

        return redirect()->route('beds.show', $bed)
            ->with('success', 'Bed created successfully.');
    }

    /**
     * Display the specified bed.
     */
    public function show(Bed $bed)
    {
        $bed->load(['ward', 'admissions' => function ($query) {
            $query->with('patient')->latest();
        }]);
        
        $currentAdmission = $bed->currentAdmission();
        $currentPatient = $currentAdmission ? $currentAdmission->patient : null;
        
        return view('beds.show', compact('bed', 'currentAdmission', 'currentPatient'));
    }

    /**
     * Show the form for editing the specified bed.
     */
    public function edit(Bed $bed)
    {
        $wards = Ward::all();
        return view('beds.edit', compact('bed', 'wards'));
    }

    /**
     * Update the specified bed in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'bed_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('beds')->where(function ($query) use ($request) {
                    return $query->where('ward_id', $request->ward_id);
                })->ignore($bed->id),
            ],
            'ward_id' => 'required|exists:wards,id',
            'status' => [
                'required',
                Rule::in(['available', 'occupied', 'reserved', 'maintenance', 'cleaning']),
            ],
            'type' => [
                'required',
                Rule::in(['standard', 'electric', 'bariatric', 'pediatric', 'intensive_care', 'other']),
            ],
            'notes' => 'nullable|string',
        ]);

        // Prevent updating status to available if bed is occupied with an active admission
        if ($validated['status'] === 'available' && $bed->status === 'occupied' && $bed->currentAdmission()) {
            return back()->withErrors(['status' => 'Cannot change status to available when bed has an active admission.'])
                        ->withInput();
        }

        $bed->update($validated);

        return redirect()->route('beds.show', $bed)
            ->with('success', 'Bed updated successfully.');
    }

    /**
     * Remove the specified bed from storage.
     */
    public function destroy(Bed $bed)
    {
        if ($bed->currentAdmission()) {
            return back()->with('error', 'Cannot delete bed with active patient admission.');
        }
        
        $bed->delete();

        return redirect()->route('beds.index')
            ->with('success', 'Bed deleted successfully.');
    }

    /**
     * Mark a bed as clean and available
     */
    public function markAvailable(Bed $bed)
    {
        if ($bed->status !== 'cleaning') {
            return back()->with('error', 'Only beds in cleaning status can be marked as available.');
        }
        
        $bed->update(['status' => 'available']);
        
        return back()->with('success', 'Bed marked as clean and available.');
    }

    /**
     * Mark a bed for maintenance
     */
    public function markMaintenance(Bed $bed)
    {
        if ($bed->currentAdmission()) {
            return back()->with('error', 'Cannot mark an occupied bed for maintenance.');
        }
        
        $bed->update(['status' => 'maintenance']);
        
        return back()->with('success', 'Bed marked for maintenance.');
    }
} 