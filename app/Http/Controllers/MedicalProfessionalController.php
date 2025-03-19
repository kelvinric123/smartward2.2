<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Nurse;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MedicalProfessionalController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        // Debug log when the controller is constructed
        Log::info('MedicalProfessionalController constructed');
        $this->middleware('auth');
    }
    
    /**
     * Display the consultants page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function consultants()
    {
        return redirect()->route('consultants.index');
    }

    /**
     * Display the nurses page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nurses()
    {
        // Add debug log
        Log::info('Nurses method reached');
        
        return redirect()->route('nurses.index');
    }
    
    /**
     * Display the nurse roster management page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function roster()
    {
        // Add debug log
        Log::info('Nurse roster method reached');
        
        return redirect()->route('roster.index');
    }
} 