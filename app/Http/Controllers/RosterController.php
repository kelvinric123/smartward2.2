<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RosterController extends Controller
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
     * Display the nurse roster management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $nurses = Nurse::orderBy('name')->get();
        $wards = Ward::orderBy('name')->get();
        
        // Group nurses by ward for easier management
        $nursesByWard = $nurses->groupBy('ward_assignment');
        
        // Get shifts for filtering
        $shifts = Nurse::getShiftOptions();
        
        return view('roster.index', compact('nurses', 'wards', 'nursesByWard', 'shifts'));
    }

    /**
     * Show the form for editing a nurse's roster.
     *
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\View\View
     */
    public function edit(Nurse $nurse)
    {
        $wards = Ward::orderBy('name')->get();
        $shifts = Nurse::getShiftOptions();
        
        // Default roster structure for 7 days if none exists
        $roster = $nurse->roster ?? $this->generateDefaultRoster();
        
        return view('roster.edit', compact('nurse', 'wards', 'shifts', 'roster'));
    }

    /**
     * Update the nurse's roster.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nurse  $nurse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Nurse $nurse)
    {
        $validated = $request->validate([
            'roster' => 'required|array',
            'shift_preferences' => 'nullable|array',
            'roster_notes' => 'nullable|string|max:255',
        ]);
        
        // Update the nurse's roster information
        $nurse->update([
            'roster' => $validated['roster'],
            'shift_preferences' => $validated['shift_preferences'] ?? null,
            'roster_notes' => $validated['roster_notes'] ?? null,
            'last_roster_update' => now(),
        ]);
        
        // If the ward assignment was changed, update that as well
        if ($request->has('ward_assignment') && $request->ward_assignment != $nurse->ward_assignment) {
            $nurse->update(['ward_assignment' => $request->ward_assignment]);
        }
        
        return redirect()->route('roster.index')
            ->with('success', 'Nurse roster updated successfully.');
    }

    /**
     * Get roster for a specific ward.
     *
     * @param  string  $wardName
     * @return \Illuminate\View\View
     */
    public function wardRoster($wardName)
    {
        $ward = Ward::where('name', $wardName)->firstOrFail();
        $nurses = Nurse::where('ward_assignment', $wardName)->orderBy('name')->get();
        
        return view('roster.ward', compact('ward', 'nurses'));
    }

    /**
     * Generate a default roster structure for 7 days.
     *
     * @return array
     */
    private function generateDefaultRoster()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $roster = [];
        
        foreach ($days as $day) {
            $roster[$day] = [
                'shift' => null,
                'start_time' => null,
                'end_time' => null,
                'assigned' => false
            ];
        }
        
        return $roster;
    }
}
