<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WardController extends Controller
{
    /**
     * Display a listing of the wards.
     */
    public function index()
    {
        $wards = Ward::withCount(['beds', 'admissions' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        return view('wards.index', compact('wards'));
    }

    /**
     * Show the form for creating a new ward.
     */
    public function create()
    {
        return view('wards.create');
    }

    /**
     * Store a newly created ward in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:wards',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer',
            'capacity' => 'required|integer|min:0',
            'type' => [
                'required',
                Rule::in(['general', 'intensive_care', 'emergency', 'maternity', 'pediatric', 'psychiatric', 'surgery', 'other']),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'under_maintenance']),
            ],
        ]);

        $ward = Ward::create($validated);

        return redirect()->route('wards.show', $ward)
            ->with('success', 'Ward created successfully.');
    }

    /**
     * Display the specified ward.
     */
    public function show(Ward $ward)
    {
        $ward->load(['beds' => function ($query) {
            $query->withCount(['admissions' => function ($q) {
                $q->where('status', 'active');
            }]);
        }]);

        $occupancyRate = $ward->getOccupancyRate();
        $availableBeds = $ward->getAvailableBedsCount();
        $occupiedBeds = $ward->getOccupiedBedsCount();

        $medicalDevices = $ward->medicalDevices()->get();

        return view('wards.show', compact('ward', 'occupancyRate', 'availableBeds', 'occupiedBeds', 'medicalDevices'));
    }

    /**
     * Show the form for editing the specified ward.
     */
    public function edit(Ward $ward)
    {
        return view('wards.edit', compact('ward'));
    }

    /**
     * Update the specified ward in storage.
     */
    public function update(Request $request, Ward $ward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('wards')->ignore($ward->id),
            ],
            'description' => 'nullable|string',
            'floor' => 'nullable|integer',
            'capacity' => 'required|integer|min:0',
            'type' => [
                'required',
                Rule::in(['general', 'intensive_care', 'emergency', 'maternity', 'pediatric', 'psychiatric', 'surgery', 'other']),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'under_maintenance']),
            ],
        ]);

        $ward->update($validated);

        return redirect()->route('wards.show', $ward)
            ->with('success', 'Ward updated successfully.');
    }

    /**
     * Remove the specified ward from storage.
     */
    public function destroy(Ward $ward)
    {
        // Check if ward has active admissions
        $hasActiveAdmissions = $ward->admissions()->where('status', 'active')->exists();
        
        if ($hasActiveAdmissions) {
            return back()->with('error', 'Cannot delete ward with active patient admissions.');
        }
        
        $ward->delete();

        return redirect()->route('wards.index')
            ->with('success', 'Ward deleted successfully.');
    }
} 