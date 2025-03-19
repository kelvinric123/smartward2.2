<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\DischargeChecklist;
use Illuminate\Http\Request;

class DischargeChecklistController extends Controller
{
    /**
     * Start the discharge process for an admission.
     */
    public function startDischargeProcess(Request $request, Admission $admission)
    {
        // Check if admission is active
        if (!$admission->isActive()) {
            return back()->with('error', 'Only active admissions can start the discharge process.');
        }
        
        // Start the discharge process
        $checklist = $admission->startDischargeProcess();
        
        // Set default planned discharge date (7 days from admission date)
        $checklist->update([
            'planned_discharge_date' => $admission->admission_date->copy()->addDays(7)
        ]);
        
        // Get source parameters
        $from = $request->input('from');
        $bedId = $request->input('bed_id');
        
        return redirect()->route('discharge-checklist.edit', [
            'dischargeChecklist' => $checklist,
            'from' => $from,
            'bed_id' => $bedId
        ])->with('success', 'Discharge process started successfully.');
    }
    
    /**
     * Show the discharge checklist form.
     */
    public function edit(Request $request, DischargeChecklist $dischargeChecklist)
    {
        $dischargeChecklist->load('admission.patient', 'admission.bed.ward');
        
        // Get source parameters
        $from = $request->input('from');
        $bedId = $request->input('bed_id');
        
        return view('discharge-checklist.edit', compact('dischargeChecklist', 'from', 'bedId'));
    }
    
    /**
     * Update the discharge checklist.
     */
    public function update(Request $request, DischargeChecklist $dischargeChecklist)
    {
        $validated = $request->validate([
            'blood_test_results' => 'boolean',
            'blood_test_results_notes' => 'nullable|string',
            'iv_medication' => 'boolean',
            'iv_medication_notes' => 'nullable|string',
            'imaging' => 'boolean',
            'imaging_notes' => 'nullable|string',
            'procedures' => 'boolean',
            'procedures_notes' => 'nullable|string',
            'referral' => 'boolean',
            'referral_notes' => 'nullable|string',
            'documentation' => 'boolean',
            'documentation_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'planned_discharge_date' => 'nullable|date',
        ]);
        
        $dischargeChecklist->update($validated);
        
        // Preserve source parameters for proper back navigation
        $from = $request->input('from');
        $bedId = $request->input('bed_id');
        
        return redirect()->route('discharge-checklist.edit', [
            'dischargeChecklist' => $dischargeChecklist,
            'from' => $from,
            'bed_id' => $bedId
        ])->with('success', 'Discharge checklist updated successfully.');
    }
    
    /**
     * Complete the discharge process.
     */
    public function completeDischarge(Request $request, DischargeChecklist $dischargeChecklist)
    {
        $validated = $request->validate([
            'blood_test_results' => 'required|boolean',
            'blood_test_results_notes' => 'nullable|string',
            'iv_medication' => 'required|boolean',
            'iv_medication_notes' => 'nullable|string',
            'imaging' => 'required|boolean',
            'imaging_notes' => 'nullable|string',
            'procedures' => 'required|boolean',
            'procedures_notes' => 'nullable|string',
            'referral' => 'required|boolean',
            'referral_notes' => 'nullable|string',
            'documentation' => 'required|boolean',
            'documentation_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'discharge_notes' => 'nullable|string',
            'planned_discharge_date' => 'nullable|date',
        ]);
        
        $dischargeNotes = $validated['discharge_notes'] ?? null;
        unset($validated['discharge_notes']);
        
        $admission = $dischargeChecklist->admission;
        
        // Check if all items are completed
        if (!$dischargeChecklist->isComplete()) {
            return back()->with('error', 'All checklist items must be completed before discharge.');
        }
        
        // Complete the discharge process
        $admission->completeDischargeProcess($validated, $dischargeNotes);
        
        // Get source parameters
        $from = $request->input('from');
        $bedId = $request->input('bed_id');
        
        // If coming from bed details, go back to bed details
        if ($from === 'bed-details' && $bedId) {
            return redirect()->route('beds.show', $bedId)
                ->with('success', 'Patient discharged successfully.');
        }
        
        // Otherwise go to patient details
        return redirect()->route('patients.show', $admission->patient)
            ->with('success', 'Patient discharged successfully.');
    }
    
    /**
     * List all discharge checklists.
     */
    public function index()
    {
        $checklists = DischargeChecklist::with('admission.patient')
            ->latest()
            ->paginate(10);
            
        return view('discharge-checklist.index', compact('checklists'));
    }
    
    /**
     * Show a discharge checklist.
     */
    public function show(DischargeChecklist $dischargeChecklist)
    {
        $dischargeChecklist->load('admission.patient', 'admission.bed.ward', 'completedBy');
        
        return view('discharge-checklist.show', compact('dischargeChecklist'));
    }
    
    /**
     * Mark a specific checklist item as completed.
     */
    public function completeItem(Request $request, DischargeChecklist $dischargeChecklist, $item)
    {
        $allowedItems = [
            'blood_test_results',
            'iv_medication',
            'imaging',
            'procedures',
            'referral',
            'documentation'
        ];
        
        if (!in_array($item, $allowedItems)) {
            return back()->with('error', 'Invalid checklist item specified.');
        }
        
        $validated = $request->validate([
            $item => 'required|boolean',
            $item . '_notes' => 'nullable|string',
        ]);
        
        $dischargeChecklist->update($validated);
        
        // Preserve source parameters for proper back navigation
        $from = $request->input('from');
        $bedId = $request->input('bed_id');
        
        return redirect()->route('discharge-checklist.edit', [
            'dischargeChecklist' => $dischargeChecklist,
            'from' => $from,
            'bed_id' => $bedId
        ])->with('success', ucfirst(str_replace('_', ' ', $item)) . ' marked as completed.');
    }
}
