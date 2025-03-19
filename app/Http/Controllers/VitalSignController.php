<?php

namespace App\Http\Controllers;

use App\Models\VitalSign;
use App\Models\Patient;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VitalSignController extends Controller
{
    /**
     * Display a listing of the vital signs for a patient.
     */
    public function index(Patient $patient)
    {
        $vitalSigns = $patient->vitalSigns()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get chart data from the most recent 20 vital signs entries (ordered by date ascending for proper timeline)
        $chartData = $patient->vitalSigns()
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();
        
        // Format the data for Chart.js
        $labels = $chartData->map(function($item) {
            return $item->created_at->format('M d, H:i');
        })->toArray();
        
        $datasets = [
            [
                'label' => 'Temperature (°C)',
                'data' => $chartData->pluck('temperature')->toArray(),
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Heart Rate (bpm)',
                'data' => $chartData->pluck('heart_rate')->toArray(),
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Respiratory Rate (breaths/min)',
                'data' => $chartData->pluck('respiratory_rate')->toArray(),
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Systolic BP (mmHg)',
                'data' => $chartData->pluck('systolic_bp')->toArray(),
                'borderColor' => 'rgba(153, 102, 255, 1)',
                'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Diastolic BP (mmHg)',
                'data' => $chartData->pluck('diastolic_bp')->toArray(),
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Oxygen Saturation (%)',
                'data' => $chartData->pluck('oxygen_saturation')->toArray(),
                'borderColor' => 'rgba(255, 206, 86, 1)',
                'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Blood Glucose (mmol/L)',
                'data' => $chartData->pluck('blood_glucose')->toArray(),
                'borderColor' => 'rgba(75, 192, 128, 1)',
                'backgroundColor' => 'rgba(75, 192, 128, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'Pain Level (0-10)',
                'data' => $chartData->pluck('pain_level')->toArray(),
                'borderColor' => 'rgba(192, 75, 75, 1)',
                'backgroundColor' => 'rgba(192, 75, 75, 0.2)',
                'fill' => false,
                'tension' => 0.1
            ],
            [
                'label' => 'EWS Score',
                'data' => $chartData->map(function($item) {
                    // If ews_score exists, use it, otherwise calculate it
                    return $item->ews_score ?? $item->calculateEWS();
                })->toArray(),
                'borderColor' => 'rgba(0, 0, 0, 1)',
                'backgroundColor' => 'rgba(0, 0, 0, 0.2)',
                'fill' => false,
                'tension' => 0.1,
                'borderWidth' => 2,
                'pointRadius' => 5,
                'pointBackgroundColor' => $chartData->map(function($item) {
                    $riskLevel = $item->getEwsRiskLevel();
                    switch ($riskLevel) {
                        case 'Low':
                            return 'rgba(75, 192, 75, 1)';
                        case 'Medium':
                            return 'rgba(255, 159, 0, 1)';
                        case 'High':
                            return 'rgba(255, 0, 0, 1)';
                        default:
                            return 'rgba(100, 100, 100, 1)';
                    }
                })->toArray()
            ]
        ];
        
        // Calculate or update EWS for all vital signs without score
        VitalSign::whereNull('ews_score')->orWhere('ews_score', 0)->get()->each(function($vitalSign) {
            $vitalSign->ews_score = $vitalSign->calculateEWS();
            $vitalSign->save();
        });
        
        return view('vital-signs.index', compact('patient', 'vitalSigns', 'labels', 'datasets'));
    }

    /**
     * Show the form for creating a new vital sign record.
     */
    public function create(Request $request)
    {
        $patient = null;
        $admission = null;
        
        if ($request->has('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }
        
        if ($request->has('admission_id')) {
            $admission = Admission::findOrFail($request->admission_id);
            $patient = $admission->patient;
        }
        
        return view('vital-signs.create', compact('patient', 'admission'));
    }

    /**
     * Store a newly created vital sign record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'admission_id' => 'nullable|exists:admissions,id',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|integer|between:0,300',
            'respiratory_rate' => 'nullable|integer|between:0,100',
            'systolic_bp' => 'nullable|integer|between:0,300',
            'diastolic_bp' => 'nullable|integer|between:0,200',
            'oxygen_saturation' => 'nullable|integer|between:0,100',
            'blood_glucose' => 'nullable|numeric|between:0,50',
            'pain_level' => 'nullable|numeric|between:0,10',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Add the user who recorded this vital sign
        $validated['measured_by'] = Auth::id();
        
        $vitalSign = VitalSign::create($validated);
        
        // Get navigation parameters for preserving context
        $from = $request->input('from');
        $wardId = $request->input('ward_id');
        $subsection = $request->input('subsection');
        $consultantId = $request->input('consultant_id');
        
        // Prepare query parameters for redirect
        $redirectParams = [];
        if ($from) $redirectParams['from'] = $from;
        if ($wardId) $redirectParams['ward_id'] = $wardId;
        if ($subsection !== null) $redirectParams['subsection'] = $subsection;
        if ($consultantId) $redirectParams['consultant_id'] = $consultantId;
        
        // Redirect back to bed map directly if that's the origin
        if ($from === 'bed-map' && $wardId) {
            return redirect()
                ->route('bed-management.bed-map', $redirectParams)
                ->with('success', 'Vital signs recorded successfully.');
        }
        
        if ($request->has('admission_id')) {
            // Serious debugging - get actual details about what's happening
            try {
                // Check if admission_id is actually stored in the vital sign
                $admId = $vitalSign->admission_id;
                
                // Check if the Admission model exists with this ID
                $admissionExists = \App\Models\Admission::find($admId) !== null;
                
                if (!$admissionExists) {
                    // If the admission doesn't exist, don't try to redirect to it
                    return redirect()
                        ->route('patients.show', array_merge(['patient' => $vitalSign->patient_id], $redirectParams))
                        ->with('warning', 'Vital signs recorded, but referenced admission not found.');
                }
                
                // Try the direct URL approach to bypass route generation issues
                return redirect("/admissions/{$admId}?" . http_build_query($redirectParams))
                    ->with('success', 'Vital signs recorded successfully.');
                    
            } catch (\Exception $e) {
                // Log the error and use a fallback
                \Log::error('Error redirecting to admission: ' . $e->getMessage());
                
                return redirect()
                    ->route('patients.show', array_merge(['patient' => $vitalSign->patient_id], $redirectParams))
                    ->with('warning', 'Vital signs recorded, but could not view admission details.');
            }
        }
        
        return redirect()
            ->route('patients.show', array_merge(['patient' => $vitalSign->patient_id], $redirectParams))
            ->with('success', 'Vital signs recorded successfully.');
    }

    /**
     * Display the specified vital sign record.
     */
    public function show(VitalSign $vitalSign)
    {
        return view('vital-signs.show', compact('vitalSign'));
    }

    /**
     * Show the form for editing the specified vital sign record.
     */
    public function edit(VitalSign $vitalSign)
    {
        return view('vital-signs.edit', compact('vitalSign'));
    }

    /**
     * Update the specified vital sign record in storage.
     */
    public function update(Request $request, VitalSign $vitalSign)
    {
        $validated = $request->validate([
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|integer|between:0,300',
            'respiratory_rate' => 'nullable|integer|between:0,100',
            'systolic_bp' => 'nullable|integer|between:0,300',
            'diastolic_bp' => 'nullable|integer|between:0,200',
            'oxygen_saturation' => 'nullable|integer|between:0,100',
            'blood_glucose' => 'nullable|numeric|between:0,50',
            'pain_level' => 'nullable|numeric|between:0,10',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $vitalSign->update($validated);
        
        if ($vitalSign->admission_id) {
            try {
                $admId = $vitalSign->admission_id;
                
                // Check if the Admission model exists with this ID
                $admissionExists = \App\Models\Admission::find($admId) !== null;
                
                if (!$admissionExists) {
                    // If the admission doesn't exist, don't try to redirect to it
                    return redirect()
                        ->route('patients.show', ['patient' => $vitalSign->patient_id])
                        ->with('warning', 'Vital signs updated, but referenced admission not found.');
                }
                
                // Try the direct URL approach to bypass route generation issues
                return redirect("/admissions/{$admId}")
                    ->with('success', 'Vital signs updated successfully.');
                    
            } catch (\Exception $e) {
                // Log the error and use a fallback
                \Log::error('Error redirecting to admission after update: ' . $e->getMessage());
                
                return redirect()
                    ->route('patients.show', ['patient' => $vitalSign->patient_id])
                    ->with('warning', 'Vital signs updated, but could not view admission details.');
            }
        }
        
        return redirect()
            ->route('patients.show', ['patient' => $vitalSign->patient_id])
            ->with('success', 'Vital signs updated successfully.');
    }

    /**
     * Remove the specified vital sign record from storage.
     */
    public function destroy(VitalSign $vitalSign)
    {
        $patientId = $vitalSign->patient_id;
        $admissionId = $vitalSign->admission_id;
        
        $vitalSign->delete();
        
        if ($admissionId) {
            try {
                // Check if the Admission model exists with this ID
                $admissionExists = \App\Models\Admission::find($admissionId) !== null;
                
                if (!$admissionExists) {
                    // If the admission doesn't exist, don't try to redirect to it
                    return redirect()
                        ->route('patients.show', ['patient' => $patientId])
                        ->with('warning', 'Vital sign record deleted, but referenced admission not found.');
                }
                
                // Try the direct URL approach to bypass route generation issues
                return redirect("/admissions/{$admissionId}")
                    ->with('success', 'Vital sign record deleted successfully.');
                    
            } catch (\Exception $e) {
                // Log the error and use a fallback
                \Log::error('Error redirecting to admission after delete: ' . $e->getMessage());
                
                return redirect()
                    ->route('patients.show', ['patient' => $patientId])
                    ->with('warning', 'Vital sign record deleted, but could not view admission details.');
            }
        }
        
        return redirect()
            ->route('patients.show', ['patient' => $patientId])
            ->with('success', 'Vital sign record deleted successfully.');
    }
    
    /**
     * Show form to create vital signs for a specific admission.
     */
    public function createForAdmission(Request $request, Admission $admission)
    {
        // Safety check - ensure the admission exists
        if (!$admission || !$admission->exists) {
            \Log::error("Attempted to create vital signs for non-existent admission");
            return redirect()
                ->route('patients.index')
                ->with('warning', 'The referenced admission could not be found.');
        }
        
        $patient = $admission->patient;
        
        // Safety check - ensure the patient exists
        if (!$patient || !$patient->exists) {
            \Log::error("Admission {$admission->id} has no valid patient reference");
            return redirect()
                ->route('patients.index')
                ->with('warning', 'The patient record for this admission could not be found.');
        }
        
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
        
        return view('vital-signs.create', compact('patient', 'admission', 'backUrl', 'from', 'wardId', 'subsection', 'consultantId'));
    }
} 