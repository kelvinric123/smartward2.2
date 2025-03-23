@extends('layouts.app')

@section('title', 'Admission Details')

@section('header')
    Admission Details for {{ $admission->patient->full_name }}
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto">
        <div class="mb-4">
            @if(isset($backUrl))
                <a href="{{ $backUrl }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to Bed Map
                </a>
            @else
                <a href="{{ route('beds.show', $admission->bed) }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to Bed Details
                </a>
            @endif
        </div>

        <!-- Admission Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Admission Information</h2>
                    <div class="flex space-x-3 items-center">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($admission->status === 'active') bg-green-100 text-green-800
                            @elseif($admission->status === 'discharged') bg-red-100 text-red-800
                            @elseif($admission->status === 'transferred') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($admission->status) }}
                        </span>
                        
                        @if($admission->status === 'active')
                            <form action="{{ route('admissions.discharge', $admission) }}" method="POST" onsubmit="return confirm('Are you sure you want to discharge this patient?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Discharge Patient
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Admission Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->admission_date->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Expected Discharge</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->expected_discharge_date ? $admission->expected_discharge_date->format('M d, Y') : 'Not specified' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Actual Discharge</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->actual_discharge_date ? $admission->actual_discharge_date->format('M d, Y H:i') : 'Not discharged' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Length of Stay</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($admission->status === 'discharged' && $admission->actual_discharge_date)
                                        {{ $admission->admission_date->diffInDays($admission->actual_discharge_date) }} days
                                    @else
                                        {{ $admission->admission_date->diffInDays(now()) }} days (ongoing)
                                    @endif
                                </dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Bed</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('beds.show', $admission->bed) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Bed {{ $admission->bed->bed_number }}
                                    </a>
                                </dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Ward</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->bed->ward->name }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Primary Consultant</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($admission->consultant)
                                        {{ $admission->consultant->name }} ({{ $admission->consultant->specialty }})
                                    @else
                                        Not specified
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Diagnosis</h3>
                            <div class="mt-1 text-sm text-gray-900 p-3 bg-gray-50 rounded-md">
                                {{ $admission->diagnosis ?? 'No diagnosis recorded' }}
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                            <div class="mt-1 text-sm text-gray-900 p-3 bg-gray-50 rounded-md">
                                {{ $admission->notes ?? 'No notes recorded' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Patient Information Card -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Patient Information</h2>
                    <a href="{{ route('patients.show', $admission->patient) }}" class="text-indigo-600 hover:text-indigo-900">
                        View Full Patient Record
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->patient->full_name }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">MRN</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->patient->mrn }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->patient->date_of_birth->format('M d, Y') }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Age</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->patient->date_of_birth->age }} years</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($admission->patient->gender) }}</dd>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $admission->patient->contact_number ?? 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Medical Information</h3>
                        <div class="mb-3">
                            <h4 class="text-xs font-medium text-gray-500">Medical History</h4>
                            <p class="text-sm text-gray-900">{{ $admission->patient->medical_history ?? 'No medical history recorded' }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">Allergies</h4>
                            <p class="text-sm text-gray-900">{{ $admission->patient->allergies ?? 'No allergies recorded' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vital Signs Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Vital Signs</h2>
                    <div class="mt-6 flex space-x-3">
                        @if($admission->status === 'active')
                            <a href="{{ route('vital-signs.create-for-admission', $admission) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Record Vital Signs
                            </a>
                        @endif
                    </div>
                </div>
                
                @if($vitalSigns && $vitalSigns->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded At</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heart Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Pressure</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respiratory Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oxygen Saturation</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vitalSigns as $vitalSign)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->temperature ? $vitalSign->temperature . ' °C' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->blood_pressure ? $vitalSign->blood_pressure : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . ' breaths/min' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->measured_by ?? 'Unknown' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-md text-center">
                        <p class="text-gray-500">No vital signs recorded for this admission.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 