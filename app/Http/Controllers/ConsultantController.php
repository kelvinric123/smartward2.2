<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultantController extends Controller
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
     * Display a listing of the consultants.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $consultants = Consultant::orderBy('name')->get();
        
        return view('consultants.index', [
            'consultants' => $consultants,
            'statusOptions' => Consultant::getStatusOptions(),
        ]);
    }

    /**
     * Show the form for creating a new consultant.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('consultants.create', [
            'statusOptions' => Consultant::getStatusOptions(),
        ]);
    }

    /**
     * Store a newly created consultant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|string|in:' . implode(',', Consultant::getStatusOptions()),
            'email' => 'nullable|email|max:255',
            'office_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Consultant::create($validated);

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant created successfully.');
    }

    /**
     * Display the specified consultant.
     *
     * @param  \App\Models\Consultant  $consultant
     * @return \Illuminate\View\View
     */
    public function show(Consultant $consultant)
    {
        return view('consultants.show', compact('consultant'));
    }

    /**
     * Show the form for editing the specified consultant.
     *
     * @param  \App\Models\Consultant  $consultant
     * @return \Illuminate\View\View
     */
    public function edit(Consultant $consultant)
    {
        return view('consultants.edit', [
            'consultant' => $consultant,
            'statusOptions' => Consultant::getStatusOptions(),
        ]);
    }

    /**
     * Update the specified consultant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultant  $consultant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|string|in:' . implode(',', Consultant::getStatusOptions()),
            'email' => 'nullable|email|max:255',
            'office_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $consultant->update($validated);

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant updated successfully.');
    }

    /**
     * Deactivate the specified consultant.
     *
     * @param  \App\Models\Consultant  $consultant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Consultant $consultant)
    {
        $consultant->update(['status' => 'Unavailable']);

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant deactivated successfully.');
    }

    /**
     * Remove the specified consultant from storage.
     *
     * @param  \App\Models\Consultant  $consultant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Consultant $consultant)
    {
        $consultant->delete();

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant deleted successfully.');
    }
} 