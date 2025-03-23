@extends('layouts.app')

@section('title', 'Patient Panel Overview')

@section('header', 'Patient Panel Overview')

@section('content')
    <div class="space-y-6">
        <!-- Introduction and Controls -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Patient Panels Overview</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            View all patient panels organized by ward. Each bed displays key patient information.
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-sm text-gray-600">Available</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                <span class="text-sm text-gray-600">Occupied</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                                <span class="text-sm text-gray-600">Maintenance</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                <span class="text-sm text-gray-600">Reserved</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                                <span class="text-sm text-gray-600">Cleaning</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Panels by Ward -->
        @foreach($wards as $ward)
            @if(isset($bedsByWard[$ward->id]) && $bedsByWard[$ward->id]->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $ward->name }} ({{ $ward->type }})</h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($bedsByWard[$ward->id] as $bed)
                                @php
                                    $statusColor = 'bg-green-500';
                                    $statusText = 'Available';
                                    
                                    if($bed->status === 'occupied') {
                                        $statusColor = 'bg-red-500';
                                        $statusText = 'Occupied';
                                    } elseif($bed->status === 'maintenance') {
                                        $statusColor = 'bg-yellow-500';
                                        $statusText = 'Maintenance';
                                    } elseif($bed->status === 'reserved') {
                                        $statusColor = 'bg-blue-500';
                                        $statusText = 'Reserved';
                                    } elseif($bed->status === 'cleaning') {
                                        $statusColor = 'bg-purple-500';
                                        $statusText = 'Cleaning';
                                    }
                                    
                                    $currentAdmission = $bed->admissions->first();
                                @endphp
                                
                                <div class="border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                                    <!-- Bed Header -->
                                    <div class="flex items-center justify-between px-4 py-3 border-b">
                                        <div class="flex items-center">
                                            <span class="inline-block w-3 h-3 rounded-full {{ $statusColor }} mr-2"></span>
                                            <h4 class="text-base font-medium text-gray-900">Bed {{ $bed->bed_number }}</h4>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $statusText }}</span>
                                    </div>
                                    
                                    <!-- Patient Info -->
                                    <div class="p-4">
                                        @if($bed->status === 'occupied' && $currentAdmission && $currentAdmission->patient)
                                            <div class="mb-3">
                                                <div class="flex items-center mb-2">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-gray-500 font-medium text-xs">
                                                            {{ strtoupper(substr($currentAdmission->patient->first_name, 0, 1) . substr($currentAdmission->patient->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900">{{ $currentAdmission->patient->full_name }}</p>
                                                        <p class="text-xs text-gray-500">MRN: {{ $currentAdmission->patient->mrn }}</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                                    <div>
                                                        <span class="font-medium">Admitted:</span>
                                                        <p>{{ $currentAdmission->admission_date->format('M d, Y') }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Expected Discharge:</span>
                                                        <p>{{ $currentAdmission->expected_discharge_date ? $currentAdmission->expected_discharge_date->format('M d, Y') : 'Not set' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Diagnosis:</span>
                                                        <p>{{ $currentAdmission->diagnosis ?? 'Not specified' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Days in Hospital:</span>
                                                        <p>{{ $currentAdmission->admission_date->diffInDays(now()) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Medical Team Preview -->
                                            <div class="text-xs mt-3">
                                                <p class="font-medium text-gray-700 mb-1">Medical Team:</p>
                                                @if($currentAdmission->patient->consultants->isNotEmpty())
                                                    <p>
                                                        <span class="font-medium">Primary Doctor:</span>
                                                        {{ $currentAdmission->patient->consultants->first()->full_name }}
                                                    </p>
                                                @endif
                                                @if($currentAdmission->patient->nurses->isNotEmpty())
                                                    <p>
                                                        <span class="font-medium">Primary Nurse:</span>
                                                        {{ $currentAdmission->patient->nurses->first()->full_name }}
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center h-32">
                                                <p class="text-sm text-gray-500">No patient information</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Footer with Action Link -->
                                    <div class="px-4 py-3 bg-gray-50 text-right">
                                        <a href="{{ route('patient-panel.show', $bed->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            View Full Panel
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        
        @if($wards->isEmpty() || $bedsByWard->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-gray-500">No patient panels available.</p>
            </div>
        @endif
    </div>
@endsection 