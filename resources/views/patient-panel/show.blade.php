@extends('layouts.app')

@section('title', "Patient Panel - Bed {$bed->bed_number}")

@section('header')
    Patient Panel - Bed {{ $bed->bed_number }}, {{ $bed->ward->name }}
@endsection

@section('content')
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('patient-panel.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Patient Panels
        </a>
    </div>

    <!-- Main Panel Container -->
    <div id="patientPanelContainer" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <!-- Header Bar - Similar to the screenshot with teal/dark background -->
        <div class="bg-teal-600 text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <div class="text-sm">QM Smart Ward Patient System</div>
                    <div class="text-xs">{{ now()->format('H:i:s') }} {{ now()->format('D, m/d/Y') }}</div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="mr-4">{{ $bed->ward->temperature ?? '29°C' }}</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <!-- Add fullscreen button -->
                <div class="ml-4 cursor-pointer flex items-center bg-teal-700 rounded px-2 py-1 hover:bg-teal-800 transition-colors" id="fullscreenButton">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                    <span class="text-xs whitespace-nowrap" id="fullscreenText">Fullscreen</span>
                </div>
            </div>
        </div>

        @php
            $currentAdmission = $bed->admissions->first();
            $patientName = $currentAdmission && $currentAdmission->patient ? $currentAdmission->patient->full_name : 'No Patient';
            $patientMRN = $currentAdmission && $currentAdmission->patient ? $currentAdmission->patient->mrn : 'N/A';
        @endphp

        <!-- Patient Info Bar -->
        <div class="bg-teal-500 text-white p-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-white text-teal-600 flex items-center justify-center text-xl font-bold">
                        @if($currentAdmission && $currentAdmission->patient)
                            {{ strtoupper(substr($currentAdmission->patient->first_name, 0, 1) . substr($currentAdmission->patient->last_name, 0, 1)) }}
                        @else
                            NA
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">{{ $patientName }}</h3>
                        <p class="text-white text-opacity-80">MRN: {{ $patientMRN }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold">{{ $bed->bed_number }}</div>
                    <p class="text-white text-opacity-80">{{ $bed->ward->name }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area - Interactive Buttons Grid -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- First Row -->
                <div class="bg-teal-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-teal-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h4 class="text-teal-800 font-semibold mb-1">Patient Information</h4>
                    <p class="text-sm text-teal-600">View detailed patient information</p>
                    @if($currentAdmission && $currentAdmission->patient)
                        <button id="viewPatientDetailsBtn" class="mt-4 bg-teal-600 text-white px-4 py-2 rounded-md text-sm hover:bg-teal-700 transition-colors">View Details</button>
                    @else
                        <button disabled class="mt-4 bg-gray-400 text-white px-4 py-2 rounded-md text-sm cursor-not-allowed">Not Available</button>
                    @endif
                </div>

                <div class="bg-blue-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h4 class="text-blue-800 font-semibold mb-1">Medication Information</h4>
                    <p class="text-sm text-blue-600">View prescribed medications</p>
                    <button id="viewMedicationsBtn" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">View Medications</button>
                </div>

                <div class="bg-indigo-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h4 class="text-indigo-800 font-semibold mb-1">Hospital Video</h4>
                    <p class="text-sm text-indigo-600">Watch hospital information</p>
                    <button id="viewVideoBtn" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition-colors">Watch Video</button>
                </div>

                <!-- Second Row -->
                <div class="bg-green-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <h4 class="text-green-800 font-semibold mb-1">Vital Signs</h4>
                    <p class="text-sm text-green-600">Monitor patient vitals</p>
                    @if($currentAdmission)
                        <button id="viewVitalsBtn" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition-colors">View Vitals</button>
                    @else
                        <button disabled class="mt-4 bg-gray-400 text-white px-4 py-2 rounded-md text-sm cursor-not-allowed">Not Available</button>
                    @endif
                </div>

                <div class="bg-purple-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="text-purple-800 font-semibold mb-1">Alert Nurse</h4>
                    <p class="text-sm text-purple-600">Call for assistance</p>
                    <button id="alertNurseBtn" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 transition-colors">Alert Nurse</button>
                </div>

                <div class="bg-yellow-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h4 class="text-yellow-800 font-semibold mb-1">Medical Team</h4>
                    <p class="text-sm text-yellow-600">View medical staff information</p>
                    <button id="viewMedicalTeamBtn" class="mt-4 bg-yellow-600 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-700 transition-colors">View Team</button>
                </div>

                <!-- Third Row -->
                <div class="bg-red-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h4 class="text-red-800 font-semibold mb-1">Environmental Control</h4>
                    <p class="text-sm text-red-600">Adjust room conditions</p>
                    <button id="environmentControlBtn" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition-colors">Control Environment</button>
                </div>

                <div class="bg-gray-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h4 class="text-gray-800 font-semibold mb-1">Satisfaction Survey</h4>
                    <p class="text-sm text-gray-600">Rate your experience</p>
                    <button id="takeSurveyBtn" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700 transition-colors">Take Survey</button>
                </div>

                <div class="bg-pink-100 rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-pink-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h4 class="text-pink-800 font-semibold mb-1">Health Education</h4>
                    <p class="text-sm text-pink-600">Access educational resources</p>
                    <a href="{{ route('patient-panel.health-education', ['bed' => $bed->id]) }}" class="mt-4 bg-pink-600 text-white px-4 py-2 rounded-md text-sm hover:bg-pink-700 transition-colors inline-block">View Resources</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Details Modal -->
    <div id="patientDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-teal-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Patient Details</h3>
                <button id="closePatientDetailsBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            @if($currentAdmission && $currentAdmission->patient)
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Information Section -->
                        <div class="bg-gray-50 rounded-lg p-4 shadow-inner">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Patient Information</h4>
                            
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-teal-100 flex items-center justify-center">
                                    <span class="text-teal-600 font-medium">
                                        {{ strtoupper(substr($currentAdmission->patient->first_name, 0, 1) . substr($currentAdmission->patient->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-semibold">{{ $currentAdmission->patient->full_name }}</h5>
                                    <p class="text-sm text-gray-600">MRN: {{ $currentAdmission->patient->mrn }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Gender</p>
                                        <p class="text-sm font-medium">{{ ucfirst($currentAdmission->patient->gender) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Age</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->patient->date_of_birth->age }} years</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date of Birth</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->patient->date_of_birth->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Blood Type</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->patient->blood_type ?? 'Not recorded' }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t">
                                    <h6 class="text-sm font-semibold mb-2">Contact Information</h6>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Phone:</span> 
                                        {{ $currentAdmission->patient->phone ?? 'Not recorded' }}
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Email:</span> 
                                        {{ $currentAdmission->patient->email ?? 'Not recorded' }}
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Address:</span> 
                                        {{ $currentAdmission->patient->address ?? 'Not recorded' }}
                                    </p>
                                </div>

                                <div class="mt-4 pt-4 border-t">
                                    <h6 class="text-sm font-semibold mb-2">Emergency Contact</h6>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Name:</span> 
                                        {{ $currentAdmission->patient->emergency_contact_name ?? 'Not recorded' }}
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Relationship:</span> 
                                        {{ $currentAdmission->patient->emergency_contact_relationship ?? 'Not recorded' }}
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-500">Phone:</span> 
                                        {{ $currentAdmission->patient->emergency_contact_phone ?? 'Not recorded' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Admission Information Section -->
                        <div class="bg-gray-50 rounded-lg p-4 shadow-inner">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Admission Information</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <h6 class="text-sm font-semibold mb-2">Admission Details</h6>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Admission Date</p>
                                            <p class="text-sm font-medium">{{ $currentAdmission->admission_date->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Expected Discharge</p>
                                            <p class="text-sm font-medium">{{ $currentAdmission->expected_discharge_date ? $currentAdmission->expected_discharge_date->format('M d, Y') : 'Not specified' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Days in Hospital</p>
                                            <p class="text-sm font-medium">{{ $currentAdmission->admission_date->diffInDays(now()) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Admission Type</p>
                                            <p class="text-sm font-medium">{{ ucfirst($currentAdmission->admission_type ?? 'Regular') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t">
                                    <h6 class="text-sm font-semibold mb-2">Clinical Information</h6>
                                    <div>
                                        <p class="text-sm text-gray-500">Primary Diagnosis</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->diagnosis ?? 'Not specified' }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500">Secondary Diagnoses</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->secondary_diagnoses ?? 'None' }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500">Allergies</p>
                                        <p class="text-sm font-medium">{{ $currentAdmission->patient->allergies ?? 'None recorded' }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t">
                                    <h6 class="text-sm font-semibold mb-2">Ward Information</h6>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Ward Name</p>
                                            <p class="text-sm font-medium">{{ $bed->ward->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Ward Type</p>
                                            <p class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $bed->ward->type ?? 'standard')) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Bed Number</p>
                                            <p class="text-sm font-medium">{{ $bed->bed_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Bed Type</p>
                                            <p class="text-sm font-medium">{{ ucfirst($bed->type ?? 'Standard') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Medications Modal -->
    <div id="medicationsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Patient Medications</h3>
                <button id="closeMedicationsBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Current Medications Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Current Medications</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Medication</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Dosage</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Frequency</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Route</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Start Date</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">
                                        <div class="font-medium">Lisinopril</div>
                                        <div class="text-xs text-gray-500">ACE Inhibitor</div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">10mg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Once daily</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Oral</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subDays(5)->format('M d, Y') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">
                                        <div class="font-medium">Metformin</div>
                                        <div class="text-xs text-gray-500">Antidiabetic</div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">500mg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Twice daily</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Oral</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subDays(5)->format('M d, Y') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">
                                        <div class="font-medium">Atorvastatin</div>
                                        <div class="text-xs text-gray-500">Statin</div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">20mg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Once daily at bedtime</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Oral</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subDays(5)->format('M d, Y') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">
                                        <div class="font-medium">Ceftriaxone</div>
                                        <div class="text-xs text-gray-500">Antibiotic</div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">2g</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Every 12 hours</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">IV</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subDays(2)->format('M d, Y') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Medication Schedule Today -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Today's Medication Schedule</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Morning Medications -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h5 class="font-medium text-blue-800 mb-2">Morning (6:00 AM - 12:00 PM)</h5>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Lisinopril 10mg</p>
                                        <p class="text-xs text-gray-500">8:00 AM - Administered</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Metformin 500mg</p>
                                        <p class="text-xs text-gray-500">8:00 AM - Administered</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Ceftriaxone 2g</p>
                                        <p class="text-xs text-gray-500">10:00 AM - Administered</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Afternoon Medications -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h5 class="font-medium text-blue-800 mb-2">Afternoon (12:00 PM - 6:00 PM)</h5>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Metformin 500mg</p>
                                        <p class="text-xs text-gray-500">2:00 PM - Scheduled</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Evening Medications -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h5 class="font-medium text-blue-800 mb-2">Evening (6:00 PM - Midnight)</h5>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Ceftriaxone 2g</p>
                                        <p class="text-xs text-gray-500">10:00 PM - Scheduled</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-5 w-5 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">Atorvastatin 20mg</p>
                                        <p class="text-xs text-gray-500">10:00 PM - Scheduled</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Medication Information and Notes -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Medication Notes</h4>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <span class="font-bold">Allergy Alert:</span> Patient has reported allergic reactions to penicillin.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <span class="font-bold">Administration Note:</span> Metformin should be taken with food to minimize gastrointestinal side effects.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 border-l-4 border-purple-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-purple-700">
                                    <span class="font-bold">Monitoring Required:</span> Check blood pressure before administering Lisinopril. Report if systolic BP is below 100 mmHg.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vital Signs Modal -->
    <div id="vitalsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Patient Vital Signs</h3>
                <button id="closeVitalsBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Latest Vitals Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Latest Vital Signs</h4>
                    
                    <div class="bg-green-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500">Time</p>
                                    <p class="text-lg font-medium text-gray-900">{{ now()->subHours(2)->format('H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500">Temp</p>
                                    <p class="text-lg font-medium text-gray-900">37.2°C</p>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500">Heart Rate</p>
                                    <p class="text-lg font-medium text-gray-900">82 bpm</p>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500">Blood Pressure</p>
                                    <p class="text-lg font-medium text-gray-900">128/82</p>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500">O₂ Saturation</p>
                                    <p class="text-lg font-medium text-gray-900">98%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vital Signs History -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Vital Signs History</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date & Time</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Temperature</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Heart Rate</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Blood Pressure</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Resp. Rate</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">O₂ Saturation</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subHours(2)->format('M d, H:i') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">37.2°C</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">82 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">128/82 mmHg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">18 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">98%</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Nurse Johnson</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subHours(8)->format('M d, H:i') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">37.4°C</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">84 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">130/84 mmHg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">18 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">97%</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Nurse Williams</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subHours(14)->format('M d, H:i') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">37.8°C</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">88 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">134/86 mmHg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">20 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">96%</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Nurse Davis</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ now()->subDay()->format('M d, H:i') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">38.1°C</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">90 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">136/88 mmHg</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">22 bpm</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">95%</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">Nurse Smith</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Vital Signs Charts -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Vital Sign Trends</h4>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 text-center">Charts would be displayed here showing trends in vital signs over time.</p>
                        <div class="h-48 flex items-center justify-center border border-dashed border-gray-300 rounded-lg bg-white">
                            <div class="text-center px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm text-gray-500">Vital sign charts visualization placeholder</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Nurse Modal -->
    <div id="alertNurseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="bg-purple-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-bold">Alert Nurse</h3>
                <button id="closeAlertNurseBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-4">Please select a reason for calling the nurse:</p>
                
                <form id="alertNurseForm">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="reason-pain" name="alert_reason" type="radio" value="pain" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-pain" class="ml-3 block text-gray-700">
                                Pain Management
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="reason-medication" name="alert_reason" type="radio" value="medication" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-medication" class="ml-3 block text-gray-700">
                                Medication Assistance
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="reason-bathroom" name="alert_reason" type="radio" value="bathroom" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-bathroom" class="ml-3 block text-gray-700">
                                Bathroom Assistance
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="reason-mobility" name="alert_reason" type="radio" value="mobility" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-mobility" class="ml-3 block text-gray-700">
                                Mobility Assistance
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="reason-emergency" name="alert_reason" type="radio" value="emergency" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-emergency" class="ml-3 block text-gray-700">
                                Emergency Assistance
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="reason-other" name="alert_reason" type="radio" value="other" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <label for="reason-other" class="ml-3 block text-gray-700">
                                Other
                            </label>
                        </div>
                        
                        <div id="other-reason-container" class="hidden mt-3">
                            <label for="other-reason" class="block text-sm font-medium text-gray-700">Please specify:</label>
                            <textarea id="other-reason" name="other_reason" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Submit Alert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Success Notification -->
    <div id="successNotification" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-50 p-4 rounded-md shadow-lg border border-green-200 z-50 hidden">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800" id="notificationMessage">
                    Nurse has been alerted successfully!
                </p>
            </div>
        </div>
    </div>

    <!-- Add some spacing at the bottom to account for the fixed footer -->
    <div class="pb-20"></div>

    <!-- Medical Team Modal -->
    <div id="medicalTeamModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-yellow-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Medical Team</h3>
                <button id="closeMedicalTeamBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Primary Consultant Section -->
                @if($currentAdmission && $currentAdmission->consultant)
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <h4 class="text-lg font-semibold text-gray-900">Primary Consultant</h4>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-yellow-100 flex items-center justify-center">
                                <span class="text-yellow-700 font-semibold">
                                    {{ strtoupper(substr($currentAdmission->consultant->name ?? '', 0, 2)) }}
                                </span>
                            </div>
                            <div class="ml-4 flex-grow">
                                <div class="flex justify-between items-start">
                                    <h5 class="text-md font-semibold text-gray-900">{{ $currentAdmission->consultant->name ?? 'Unknown' }}</h5>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Attending Consultant
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600">{{ $currentAdmission->consultant->specialty ?? 'Specialty not specified' }}</p>
                                
                                <div class="mt-2 text-sm space-y-1">
                                    @if($currentAdmission->consultant && $currentAdmission->consultant->email)
                                        <div class="flex items-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ $currentAdmission->consultant->email }}
                                        </div>
                                    @endif
                                    @if($currentAdmission->consultant && $currentAdmission->consultant->contact_number)
                                        <div class="flex items-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ $currentAdmission->consultant->contact_number }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-3">
                                    <a href="#" 
                                       class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        View Schedule
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Consultants Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <h4 class="text-lg font-semibold text-gray-900">Consulting Team</h4>
                        
                        @can('assign consultants')
                        <a href="{{ route('patients.consultants.edit', $currentAdmission->patient->id ?? 0) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Manage Consultants
                        </a>
                        @endcan
                    </div>
                    
                    @if($currentAdmission && $currentAdmission->patient && $currentAdmission->patient->consultants->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($currentAdmission->patient->consultants as $consultant)
                                @if(!$currentAdmission->consultant || $consultant->id !== $currentAdmission->consultant->id)
                                <div class="bg-yellow-50 rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-14 w-14 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <span class="text-yellow-700 font-semibold">
                                                {{ strtoupper(substr($consultant->first_name, 0, 1) . substr($consultant->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-grow">
                                            <div class="flex justify-between items-start">
                                                <h5 class="text-md font-semibold text-gray-900">Dr. {{ $consultant->full_name }}</h5>
                                                @if($consultant->pivot && $consultant->pivot->is_primary)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Primary
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-600">{{ $consultant->specialty ?? 'Specialty not specified' }}</p>
                                            
                                            <div class="mt-2 text-sm space-y-1">
                                                @if($consultant->email)
                                                    <div class="flex items-center text-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $consultant->email }}
                                                    </div>
                                                @endif
                                                @if($consultant->phone)
                                                    <div class="flex items-center text-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $consultant->phone }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-3">
                                                <a href="#" 
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    View Schedule
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        
                        @if($currentAdmission->patient->consultants->count() == 1 && $currentAdmission->consultant && $currentAdmission->patient->consultants->first()->id == $currentAdmission->consultant->id)
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <p class="text-gray-600">No additional consulting physicians assigned.</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-600">No additional consultants assigned yet.</p>
                            
                            @can('assign consultants')
                            <a href="{{ route('patients.consultants.edit', $currentAdmission->patient->id ?? 0) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Assign Consultant
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
                
                <!-- Nurses Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <h4 class="text-lg font-semibold text-gray-900">Nursing Team</h4>
                        
                        @can('assign nurses')
                        <a href="{{ route('patients.nurses.edit', $currentAdmission->patient->id ?? 0) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Manage Nurses
                        </a>
                        @endcan
                    </div>
                    
                    @if($currentAdmission && $currentAdmission->patient && $currentAdmission->patient->nurses->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($currentAdmission->patient->nurses as $nurse)
                                <div class="bg-blue-50 rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-14 w-14 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-700 font-semibold">
                                                {{ strtoupper(substr($nurse->first_name, 0, 1) . substr($nurse->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-grow">
                                            <div class="flex justify-between items-start">
                                                <h5 class="text-md font-semibold text-gray-900">{{ $nurse->full_name }}</h5>
                                                @if($nurse->pivot && $nurse->pivot->is_primary)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Primary
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-600">{{ $nurse->position ?? 'Position not specified' }}</p>
                                            
                                            <div class="mt-2 text-sm space-y-1">
                                                @if($nurse->email)
                                                    <div class="flex items-center text-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $nurse->email }}
                                                    </div>
                                                @endif
                                                @if($nurse->phone)
                                                    <div class="flex items-center text-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $nurse->phone }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-3">
                                                <a href="#" 
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    View Schedule
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-600">No nurses assigned yet.</p>
                            
                            @can('assign nurses')
                            <a href="{{ route('patients.nurses.edit', $currentAdmission->patient->id ?? 0) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Assign Nurse
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
                
                <!-- Recent Activity Section -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Recent Team Activity</h4>
                    
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-green-600 font-semibold text-xs">NT</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium text-gray-900">Nurse Thompson</span> updated vital signs
                                </p>
                                <p class="text-xs text-gray-500">Today at {{ now()->subHours(2)->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <span class="text-yellow-600 font-semibold text-xs">DS</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium text-gray-900">Dr. Smith</span> ordered new medication
                                </p>
                                <p class="text-xs text-gray-500">Yesterday at {{ now()->subDays(1)->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-xs">NJ</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium text-gray-900">Nurse Johnson</span> administered medication
                                </p>
                                <p class="text-xs text-gray-500">Yesterday at {{ now()->subDays(1)->subHours(3)->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>

    <!-- Environment Control Modal -->
    <div id="environmentControlModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="bg-red-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-bold">Environment Control</h3>
                <button id="closeEnvironmentControlBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Current Room Conditions -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Current Room Conditions</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-gray-900 mb-1">
                                <span id="currentTemp">23</span>°C
                            </div>
                            <p class="text-sm text-gray-600">Temperature</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-gray-900 mb-1">
                                <span id="currentHumidity">55</span>%
                            </div>
                            <p class="text-sm text-gray-600">Humidity</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="acStatus">ON</div>
                            <p class="text-sm text-gray-600">AC Status</p>
                        </div>
                    </div>
                </div>

                <!-- Temperature Control -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Temperature Control</h4>
                    <div class="flex items-center justify-center space-x-6">
                        <button id="decreaseTemp" class="bg-red-100 text-red-600 rounded-full p-3 hover:bg-red-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <div class="text-4xl font-bold text-gray-900">
                            <span id="setTemp">22</span>°C
                        </div>
                        <button id="increaseTemp" class="bg-red-100 text-red-600 rounded-full p-3 hover:bg-red-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mode Selection -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Mode</h4>
                    <div class="grid grid-cols-4 gap-4">
                        <button class="mode-btn bg-gray-100 p-4 rounded-lg text-center hover:bg-red-100 transition-colors" data-mode="cool">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-sm">Cool</span>
                        </button>
                        <button class="mode-btn bg-gray-100 p-4 rounded-lg text-center hover:bg-red-100 transition-colors" data-mode="heat">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            </svg>
                            <span class="text-sm">Heat</span>
                        </button>
                        <button class="mode-btn bg-gray-100 p-4 rounded-lg text-center hover:bg-red-100 transition-colors" data-mode="fan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span class="text-sm">Fan</span>
                        </button>
                        <button class="mode-btn bg-gray-100 p-4 rounded-lg text-center hover:bg-red-100 transition-colors" data-mode="auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="text-sm">Auto</span>
                        </button>
                    </div>
                </div>

                <!-- Fan Speed -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Fan Speed</h4>
                    <div class="flex items-center space-x-4">
                        <input type="range" id="fanSpeed" min="1" max="5" value="3" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <span class="text-lg font-semibold text-gray-900" id="fanSpeedValue">3</span>
                    </div>
                </div>

                <!-- Power Button -->
                <div class="flex justify-center">
                    <button id="powerButton" class="bg-red-600 text-white px-6 py-3 rounded-full text-lg font-semibold hover:bg-red-700 transition-colors flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Power</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Modal -->
    <div id="surveyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="bg-gray-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-bold">Patient Satisfaction Survey</h3>
                <button id="closeSurveyBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <form id="surveyForm">
                    <!-- Overall Experience -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Overall Experience</h4>
                        <div class="flex justify-center space-x-4">
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="overall_rating" value="1" class="hidden">
                                <div class="text-4xl mb-2 opacity-50 hover:opacity-100 transition-opacity">😞</div>
                                <span class="text-sm text-gray-600">Poor</span>
                            </label>
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="overall_rating" value="2" class="hidden">
                                <div class="text-4xl mb-2 opacity-50 hover:opacity-100 transition-opacity">😐</div>
                                <span class="text-sm text-gray-600">Fair</span>
                            </label>
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="overall_rating" value="3" class="hidden">
                                <div class="text-4xl mb-2 opacity-50 hover:opacity-100 transition-opacity">🙂</div>
                                <span class="text-sm text-gray-600">Good</span>
                            </label>
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="overall_rating" value="4" class="hidden">
                                <div class="text-4xl mb-2 opacity-50 hover:opacity-100 transition-opacity">😊</div>
                                <span class="text-sm text-gray-600">Very Good</span>
                            </label>
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="overall_rating" value="5" class="hidden">
                                <div class="text-4xl mb-2 opacity-50 hover:opacity-100 transition-opacity">😄</div>
                                <span class="text-sm text-gray-600">Excellent</span>
                            </label>
                        </div>
                    </div>

                    <!-- Specific Ratings -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Please Rate the Following</h4>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Responsiveness</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" name="staff_rating" min="1" max="5" value="3" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-gray-600 w-8" id="staffRatingValue">3</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Room Cleanliness</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" name="cleanliness_rating" min="1" max="5" value="3" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-gray-600 w-8" id="cleanlinessRatingValue">3</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Food Quality</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" name="food_rating" min="1" max="5" value="3" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-gray-600 w-8" id="foodRatingValue">3</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Noise Level</label>
                                <div class="flex items-center space-x-2">
                                    <input type="range" name="noise_rating" min="1" max="5" value="3" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-gray-600 w-8" id="noiseRatingValue">3</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Additional Comments</h4>
                        <textarea name="comments" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500" placeholder="Please share any additional feedback or suggestions..."></textarea>
                    </div>

                    <!-- Areas for Improvement -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Areas for Improvement</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="improvements[]" value="communication" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Communication</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="improvements[]" value="waiting_time" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Waiting Time</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="improvements[]" value="facilities" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Facilities</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="improvements[]" value="food_service" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Food Service</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-md text-base font-semibold hover:bg-gray-700 transition-colors flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Submit Survey</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl mx-4">
            <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-bold">Hospital Information Video</h3>
                <button id="closeVideoBtn" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Video Player Section - Made Larger -->
                <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden">
                    <iframe 
                        class="w-full h-full" 
                        src="https://www.youtube.com/embed/N4ibwJdziwA" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                </div>
                
                <!-- Video Selection Section - Optimized Layout -->
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Available Videos</h4>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <button class="video-selector flex flex-col h-full p-4 border rounded-lg hover:bg-gray-50 transition-colors text-left active bg-indigo-50 border-indigo-500" data-video="https://www.youtube.com/embed/N4ibwJdziwA">
                            <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-lg mb-3 w-12 h-12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 mb-1">Welcome to Our Hospital</h5>
                                <p class="text-sm text-gray-500">A warm welcome and tour of our facilities</p>
                            </div>
                        </button>
                        
                        <button class="video-selector flex flex-col h-full p-4 border rounded-lg hover:bg-gray-50 transition-colors text-left" data-video="https://www.youtube.com/embed/KgzHJY9HVWc">
                            <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-lg mb-3 w-12 h-12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 mb-1">Patient Safety Guidelines</h5>
                                <p class="text-sm text-gray-500">Essential safety protocols during your stay</p>
                            </div>
                        </button>
                        
                        <button class="video-selector flex flex-col h-full p-4 border rounded-lg hover:bg-gray-50 transition-colors text-left" data-video="https://www.youtube.com/embed/mH81Q9Dtodc">
                            <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-lg mb-3 w-12 h-12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 mb-1">Understanding Your Medications</h5>
                                <p class="text-sm text-gray-500">Complete guide to medication management</p>
                            </div>
                        </button>
                        
                        <button class="video-selector flex flex-col h-full p-4 border rounded-lg hover:bg-gray-50 transition-colors text-left" data-video="https://www.youtube.com/embed/45Jy5g7oxfM">
                            <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-lg mb-3 w-12 h-12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 mb-1">Discharge Planning</h5>
                                <p class="text-sm text-gray-500">Important steps for a smooth transition home</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic time and fullscreen functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto enter fullscreen mode when page loads
            const patientPanelContainer = document.getElementById('patientPanelContainer');
            const fullscreenText = document.getElementById('fullscreenText');
            
            // Patient Details Modal functionality
            const viewPatientDetailsBtn = document.getElementById('viewPatientDetailsBtn');
            const closePatientDetailsBtn = document.getElementById('closePatientDetailsBtn');
            const patientDetailsModal = document.getElementById('patientDetailsModal');
            
            // Medications Modal functionality
            const viewMedicationsBtn = document.getElementById('viewMedicationsBtn');
            const closeMedicationsBtn = document.getElementById('closeMedicationsBtn');
            const medicationsModal = document.getElementById('medicationsModal');
            
            // Vital Signs Modal functionality
            const viewVitalsBtn = document.getElementById('viewVitalsBtn');
            const closeVitalsBtn = document.getElementById('closeVitalsBtn');
            const vitalsModal = document.getElementById('vitalsModal');
            
            // Alert Nurse Modal functionality
            const alertNurseBtn = document.getElementById('alertNurseBtn');
            const closeAlertNurseBtn = document.getElementById('closeAlertNurseBtn');
            const alertNurseModal = document.getElementById('alertNurseModal');
            const alertNurseForm = document.getElementById('alertNurseForm');
            const reasonOther = document.getElementById('reason-other');
            const otherReasonContainer = document.getElementById('other-reason-container');
            const successNotification = document.getElementById('successNotification');
            
            // Medical Team Modal functionality
            const viewMedicalTeamBtn = document.getElementById('viewMedicalTeamBtn');
            const closeMedicalTeamBtn = document.getElementById('closeMedicalTeamBtn');
            const medicalTeamModal = document.getElementById('medicalTeamModal');
            
            // Environment Control Modal functionality
            const environmentControlBtn = document.getElementById('environmentControlBtn');
            const closeEnvironmentControlBtn = document.getElementById('closeEnvironmentControlBtn');
            const environmentControlModal = document.getElementById('environmentControlModal');
            
            // Show alert nurse modal
            if (alertNurseBtn) {
                alertNurseBtn.addEventListener('click', function() {
                    alertNurseModal.classList.remove('hidden');
                });
            }
            
            // Close alert nurse modal
            if (closeAlertNurseBtn) {
                closeAlertNurseBtn.addEventListener('click', function() {
                    alertNurseModal.classList.add('hidden');
                });
            }
            
            // Toggle other reason text area
            if (reasonOther) {
                reasonOther.addEventListener('change', function() {
                    if (this.checked) {
                        otherReasonContainer.classList.remove('hidden');
                    } else {
                        otherReasonContainer.classList.add('hidden');
                    }
                });
            }
            
            // Listen for changes on all radio buttons
            document.querySelectorAll('input[name="alert_reason"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (this.value === 'other') {
                        otherReasonContainer.classList.remove('hidden');
                    } else {
                        otherReasonContainer.classList.add('hidden');
                    }
                });
            });
            
            // Handle form submission
            if (alertNurseForm) {
                alertNurseForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get selected reason
                    const selectedReason = document.querySelector('input[name="alert_reason"]:checked');
                    
                    if (!selectedReason) {
                        alert('Please select a reason');
                        return;
                    }
                    
                    // Here you would typically make an AJAX call to your backend
                    // For demo purposes, we'll just show the success notification
                    
                    // Close the modal
                    alertNurseModal.classList.add('hidden');
                    
                    // Reset form
                    alertNurseForm.reset();
                    otherReasonContainer.classList.add('hidden');
                    
                    // Show success notification
                    successNotification.classList.remove('hidden');
                    
                    // Auto hide notification after 3 seconds
                    setTimeout(function() {
                        successNotification.classList.add('hidden');
                    }, 3000);
                });
            }
            
            // Show patient details modal
            if (viewPatientDetailsBtn) {
                viewPatientDetailsBtn.addEventListener('click', function() {
                    patientDetailsModal.classList.remove('hidden');
                });
            }
            
            // Close patient details modal
            if (closePatientDetailsBtn) {
                closePatientDetailsBtn.addEventListener('click', function() {
                    patientDetailsModal.classList.add('hidden');
                });
            }
            
            // Show medications modal
            if (viewMedicationsBtn) {
                viewMedicationsBtn.addEventListener('click', function() {
                    medicationsModal.classList.remove('hidden');
                });
            }
            
            // Close medications modal
            if (closeMedicationsBtn) {
                closeMedicationsBtn.addEventListener('click', function() {
                    medicationsModal.classList.add('hidden');
                });
            }
            
            // Show vitals modal
            if (viewVitalsBtn) {
                viewVitalsBtn.addEventListener('click', function() {
                    vitalsModal.classList.remove('hidden');
                });
            }
            
            // Close vitals modal
            if (closeVitalsBtn) {
                closeVitalsBtn.addEventListener('click', function() {
                    vitalsModal.classList.add('hidden');
                });
            }
            
            // Show medical team modal
            if (viewMedicalTeamBtn) {
                viewMedicalTeamBtn.addEventListener('click', function() {
                    medicalTeamModal.classList.remove('hidden');
                });
            }
            
            // Close medical team modal
            if (closeMedicalTeamBtn) {
                closeMedicalTeamBtn.addEventListener('click', function() {
                    medicalTeamModal.classList.add('hidden');
                });
            }
            
            // Show environment control modal
            if (environmentControlBtn) {
                environmentControlBtn.addEventListener('click', function() {
                    environmentControlModal.classList.remove('hidden');
                });
            }
            
            // Close environment control modal
            if (closeEnvironmentControlBtn) {
                closeEnvironmentControlBtn.addEventListener('click', function() {
                    environmentControlModal.classList.add('hidden');
                });
            }
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === patientDetailsModal) {
                    patientDetailsModal.classList.add('hidden');
                }
                if (event.target === medicationsModal) {
                    medicationsModal.classList.add('hidden');
                }
                if (event.target === vitalsModal) {
                    vitalsModal.classList.add('hidden');
                }
                if (event.target === alertNurseModal) {
                    alertNurseModal.classList.add('hidden');
                }
                if (event.target === medicalTeamModal) {
                    medicalTeamModal.classList.add('hidden');
                }
                if (event.target === environmentControlModal) {
                    environmentControlModal.classList.add('hidden');
                }
            });
            
            setTimeout(function() {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                    if (fullscreenText) fullscreenText.textContent = 'Exit Fullscreen';
                }
            }, 1000);
            
            // Fullscreen button functionality
            const fullscreenButton = document.getElementById('fullscreenButton');
            const navigationBar = document.querySelector('nav');
            const pageHeader = document.querySelector('header.bg-white.shadow');
            
            fullscreenButton.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                    if (fullscreenText) fullscreenText.textContent = 'Exit Fullscreen';
                    
                    // Hide navigation and header when entering fullscreen
                    if (navigationBar) navigationBar.style.display = 'none';
                    if (pageHeader) pageHeader.style.display = 'none';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        if (fullscreenText) fullscreenText.textContent = 'Fullscreen';
                        
                        // Show navigation and header when exiting fullscreen
                        if (navigationBar) navigationBar.style.display = '';
                        if (pageHeader) pageHeader.style.display = '';
                    }
                }
            });
            
            // Update fullscreen text and UI when fullscreen state changes
            document.addEventListener('fullscreenchange', function() {
                if (document.fullscreenElement) {
                    if (fullscreenText) fullscreenText.textContent = 'Exit Fullscreen';
                    
                    // Hide navigation and header when entering fullscreen
                    if (navigationBar) navigationBar.style.display = 'none';
                    if (pageHeader) pageHeader.style.display = 'none';
                } else {
                    if (fullscreenText) fullscreenText.textContent = 'Fullscreen';
                    
                    // Show navigation and header when exiting fullscreen
                    if (navigationBar) navigationBar.style.display = '';
                    if (pageHeader) pageHeader.style.display = '';
                }
            });
            // Update time every second
            setInterval(function() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const timeElement = document.querySelector('.bg-teal-600 .text-xs');
                if (timeElement) {
                    timeElement.textContent = `${hours}:${minutes}:${seconds} ${now.toLocaleDateString('en-US', { weekday: 'short', month: 'numeric', day: 'numeric', year: 'numeric' })}`;
                }
            }, 1000);

            // Temperature Control
            const decreaseTemp = document.getElementById('decreaseTemp');
            const increaseTemp = document.getElementById('increaseTemp');
            const setTemp = document.getElementById('setTemp');
            const currentTemp = document.getElementById('currentTemp');
            
            let temperature = 22;
            
            decreaseTemp.addEventListener('click', function() {
                if (temperature > 16) {
                    temperature--;
                    setTemp.textContent = temperature;
                    simulateTemperatureChange();
                }
            });
            
            increaseTemp.addEventListener('click', function() {
                if (temperature < 30) {
                    temperature++;
                    setTemp.textContent = temperature;
                    simulateTemperatureChange();
                }
            });

            // Fan Speed Control
            const fanSpeed = document.getElementById('fanSpeed');
            const fanSpeedValue = document.getElementById('fanSpeedValue');
            
            fanSpeed.addEventListener('input', function() {
                fanSpeedValue.textContent = this.value;
            });

            // Mode Selection
            const modeButtons = document.querySelectorAll('.mode-btn');
            
            modeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modeButtons.forEach(btn => btn.classList.remove('bg-red-100'));
                    this.classList.add('bg-red-100');
                });
            });

            // Power Button
            const powerButton = document.getElementById('powerButton');
            const acStatus = document.getElementById('acStatus');
            let isPowered = true;
            
            powerButton.addEventListener('click', function() {
                isPowered = !isPowered;
                acStatus.textContent = isPowered ? 'ON' : 'OFF';
                powerButton.classList.toggle('bg-gray-600');
                powerButton.classList.toggle('bg-red-600');
            });

            // Simulate temperature change
            function simulateTemperatureChange() {
                let current = parseInt(currentTemp.textContent);
                const target = parseInt(setTemp.textContent);
                
                if (current !== target) {
                    const interval = setInterval(() => {
                        if (current < target) {
                            current++;
                        } else if (current > target) {
                            current--;
                        }
                        
                        currentTemp.textContent = current;
                        
                        if (current === target) {
                            clearInterval(interval);
                        }
                    }, 2000);
                }
            }

            // Survey Modal functionality
            const takeSurveyBtn = document.getElementById('takeSurveyBtn');
            const closeSurveyBtn = document.getElementById('closeSurveyBtn');
            const surveyModal = document.getElementById('surveyModal');
            const surveyForm = document.getElementById('surveyForm');
            
            // Show survey modal
            if (takeSurveyBtn) {
                takeSurveyBtn.addEventListener('click', function() {
                    surveyModal.classList.remove('hidden');
                });
            }
            
            // Close survey modal
            if (closeSurveyBtn) {
                closeSurveyBtn.addEventListener('click', function() {
                    surveyModal.classList.add('hidden');
                });
            }

            // Handle rating emoji selection
            const ratingInputs = document.querySelectorAll('input[name="overall_rating"]');
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Reset all emojis to 50% opacity
                    ratingInputs.forEach(inp => {
                        inp.parentElement.querySelector('div').style.opacity = '0.5';
                    });
                    // Set selected emoji to 100% opacity
                    this.parentElement.querySelector('div').style.opacity = '1';
                });
            });

            // Handle range input values
            const rangeInputs = {
                'staff_rating': 'staffRatingValue',
                'cleanliness_rating': 'cleanlinessRatingValue',
                'food_rating': 'foodRatingValue',
                'noise_rating': 'noiseRatingValue'
            };

            Object.entries(rangeInputs).forEach(([inputName, valueId]) => {
                const input = document.querySelector(`input[name="${inputName}"]`);
                const value = document.getElementById(valueId);
                
                if (input && value) {
                    input.addEventListener('input', function() {
                        value.textContent = this.value;
                    });
                }
            });

            // Handle form submission
            if (surveyForm) {
                surveyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show success notification
                    const successNotification = document.getElementById('successNotification');
                    const notificationMessage = document.getElementById('notificationMessage');
                    if (successNotification && notificationMessage) {
                        notificationMessage.textContent = 'Thank you for your feedback!';
                        successNotification.classList.remove('hidden');
                        
                        // Hide the notification after 3 seconds
                        setTimeout(() => {
                            successNotification.classList.add('hidden');
                        }, 3000);
                    }
                    
                    // Close the modal
                    surveyModal.classList.add('hidden');
                    
                    // Reset form
                    this.reset();
                    
                    // Reset emoji opacities
                    ratingInputs.forEach(input => {
                        input.parentElement.querySelector('div').style.opacity = '0.5';
                    });
                    
                    // Reset range input values
                    Object.entries(rangeInputs).forEach(([inputName, valueId]) => {
                        const input = document.querySelector(`input[name="${inputName}"]`);
                        const value = document.getElementById(valueId);
                        if (input && value) {
                            input.value = 3;
                            value.textContent = '3';
                        }
                    });
                });
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === surveyModal) {
                    surveyModal.classList.add('hidden');
                }
            });

            // Video Modal functionality
            const viewVideoBtn = document.getElementById('viewVideoBtn');
            const closeVideoBtn = document.getElementById('closeVideoBtn');
            const videoModal = document.getElementById('videoModal');
            const videoFrame = videoModal.querySelector('iframe');
            const videoSelectors = document.querySelectorAll('.video-selector');
            
            // Show video modal
            if (viewVideoBtn) {
                viewVideoBtn.addEventListener('click', function() {
                    videoModal.classList.remove('hidden');
                });
            }
            
            // Close video modal
            if (closeVideoBtn) {
                closeVideoBtn.addEventListener('click', function() {
                    videoModal.classList.add('hidden');
                    // Stop video playback when closing modal
                    videoFrame.src = videoFrame.src;
                });
            }

            // Handle video selection
            videoSelectors.forEach(selector => {
                selector.addEventListener('click', function() {
                    const videoUrl = this.dataset.video;
                    videoFrame.src = videoUrl;
                    
                    // Update active state
                    videoSelectors.forEach(sel => sel.classList.remove('bg-indigo-50', 'border-indigo-500'));
                    this.classList.add('bg-indigo-50', 'border-indigo-500');
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === videoModal) {
                    videoModal.classList.add('hidden');
                    // Stop video playback when closing modal
                    videoFrame.src = videoFrame.src;
                }
            });
        });
    </script>
@endsection 