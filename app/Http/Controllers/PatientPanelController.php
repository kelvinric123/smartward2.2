<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\Admission;
use App\Models\Ward;

class PatientPanelController extends Controller
{
    /**
     * Display a listing of the patient panels.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all beds with their current admission and patient information
        $beds = Bed::with(['ward', 'admissions' => function($query) {
            $query->where('status', 'active')
                  ->with(['patient' => function($query) {
                      $query->with(['consultants', 'nurses']);
                  }]);
        }])->get();

        // Group beds by ward
        $bedsByWard = $beds->groupBy('ward_id');
        
        // Get all wards
        $wards = Ward::all();
        
        return view('patient-panel.index', compact('bedsByWard', 'wards'));
    }

    /**
     * Display the details of a specific bed's patient panel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get specific bed with its current admission and patient information
        $bed = Bed::with(['ward', 'admissions' => function($query) {
            $query->where('status', 'active')
                  ->with(['patient' => function($query) {
                      $query->with(['consultants', 'nurses']);
                  }, 'vitalSigns' => function($query) {
                      $query->latest()->take(5);
                  }]);
        }])->findOrFail($id);
        
        return view('patient-panel.show', compact('bed'));
    }
} 