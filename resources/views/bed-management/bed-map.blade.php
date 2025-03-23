@extends('layouts.app')

@section('title', 'Bed Map')

@section('header', 'Bed Map Overview')

@section('content')
    <!-- Add bed-management-container class for proper padding -->
    <div class="bed-management-container">
    <!-- Fullscreen Styles -->
    <style>
        /* Styles applied when in fullscreen mode */
        :fullscreen {
            background-color: #f3f4f6;
            padding: 1rem;
            width: 100vw;
            height: 100vh;
            overflow-y: auto;
            position: relative; /* This will help position the footer */
        }
        
        :fullscreen .bed-map-content {
            max-width: 100% !important;
            margin: 0 auto;
            padding-bottom: 40px; /* Add space for the footer */
        }
        
        :fullscreen .grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        }
        
        /* Fullscreen footer styles */
        :fullscreen .fullscreen-footer {
            display: flex !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            color: #4b5563; /* gray-600 */
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            z-index: 50;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e5e7eb; /* gray-200 */
        }
        
        /* For Safari and older browsers */
        :-webkit-full-screen {
            background-color: #f3f4f6;
            padding: 1rem;
            width: 100vw;
            height: 100vh;
            overflow-y: auto;
            position: relative;
        }
        
        :-webkit-full-screen .bed-map-content {
            max-width: 100% !important;
            margin: 0 auto;
            padding-bottom: 40px;
        }
        
        :-webkit-full-screen .grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        }
        
        :-webkit-full-screen .fullscreen-footer {
            display: flex !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            color: #4b5563; /* gray-600 */
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            z-index: 50;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e5e7eb; /* gray-200 */
        }
        
        /* For Firefox */
        :-moz-full-screen {
            background-color: #f3f4f6;
            padding: 1rem;
            width: 100vw;
            height: 100vh;
            overflow-y: auto;
            position: relative;
        }
        
        :-moz-full-screen .bed-map-content {
            max-width: 100% !important;
            margin: 0 auto;
            padding-bottom: 40px;
        }
        
        :-moz-full-screen .grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        }
        
        :-moz-full-screen .fullscreen-footer {
            display: flex !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            color: #4b5563; /* gray-600 */
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            z-index: 50;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #e5e7eb; /* gray-200 */
        }
        
        /* Hide footer by default, only show in fullscreen */
        .fullscreen-footer {
            display: none;
        }
        
        /* Bed box consistent height styles */
        .bed-box {
            display: flex;
            flex-direction: column;
            min-height: 180px;
            height: 100%;
        }
        
        .bed-box-header {
            flex-shrink: 0;
        }
        
        .bed-box-content {
            flex-grow: 1;
        }
        
        .bed-box-footer {
            flex-shrink: 0;
            margin-top: auto;
            padding-top: 8px;
        }
        
        /* Grid container styles to ensure equal height rows */
        .bed-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-auto-rows: 1fr;
            gap: 1rem;
        }
        
        /* Status count badge styles */
        .status-count {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            margin-right: 0.5rem;
            font-weight: 500;
        }
        
        .status-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
    </style>

    <!-- Wrapper div to control fullscreen content -->
    <div id="fullscreenContainer" class="bed-map-content">
        <!-- Full Screen Button -->
        <div class="flex justify-end mb-4">
            <button id="fullscreenBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                </svg>
                Toggle Full Screen
            </button>
        </div>

        <!-- Ward Selector -->
        <div class="mb-6">
            <form action="{{ route('bed-management.bed-map') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="w-64">
                    <label for="ward_id" class="block text-sm font-medium text-gray-700">Select Ward</label>
                    <select id="ward_id" name="ward_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Wards</option>
                        @foreach($wards as $wardOption)
                            <option value="{{ $wardOption->id }}" {{ isset($ward) && $ward->id == $wardOption->id ? 'selected' : '' }}>
                                {{ $wardOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-64">
                    <label for="consultant_id" class="block text-sm font-medium text-gray-700">Filter by Consultant</label>
                    <select id="consultant_id" name="consultant_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Consultants</option>
                        @foreach($consultants ?? [] as $consultant)
                            <option value="{{ $consultant->id }}" {{ isset($consultantId) && $consultantId == $consultant->id ? 'selected' : '' }}>
                                {{ $consultant->name }} - {{ $consultant->specialty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @if(isset($subsections) && count($subsections) > 0)
                <div class="w-64">
                    <label for="subsection" class="block text-sm font-medium text-gray-700">Ward Subsection</label>
                    <select id="subsection" name="subsection" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($subsections as $index => $sectionName)
                            <option value="{{ $index }}" {{ isset($subsection) && $subsection == $index ? 'selected' : '' }}>
                                {{ $sectionName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @elseif(isset($subsection) && $subsection !== null)
                <input type="hidden" name="subsection" value="{{ $subsection }}">
                @endif
                
                <div class="mt-5">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Apply Filters
                    </button>
                    
                    @if(isset($consultantId) && $consultantId)
                        <a href="{{ route('bed-management.bed-map', ['ward_id' => $wardId ?? ($ward->id ?? ''), 'subsection' => $subsection ?? null]) }}" class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Consultant Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if(isset($consultantId) && $consultantId)
            @php
                $filteredConsultant = $consultants->firstWhere('id', $consultantId);
                $totalFilteredBeds = isset($ward) 
                    ? $beds->count() 
                    : $wards->sum(function($ward) { return $ward->beds->count(); });
            @endphp
            @if($filteredConsultant)
                <div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-md mb-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                        <span class="font-medium">Showing {{ $totalFilteredBeds }} patient(s) assigned to: {{ $filteredConsultant->name }} ({{ $filteredConsultant->specialty }})</span>
                    </div>
                </div>
            @endif
        @endif

        @if(isset($ward))
            <!-- Single Ward Bed Map -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <div class="flex flex-col md:flex-row md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $ward->name }} - {{ $ward->code }}
                            </h3>
                            @if(isset($consultantId) && $consultantId)
                                @php
                                    $filteredConsultant = $consultants->firstWhere('id', $consultantId);
                                @endphp
                                @if($filteredConsultant)
                                    <div class="mt-1 bg-blue-50 text-blue-700 px-2 py-1 rounded-md inline-block">
                                        <span class="text-sm font-medium">Filtered by consultant: {{ $filteredConsultant->name }} ({{ $beds->count() }} patients)</span>
                                    </div>
                                @endif
                            @endif
                            @if(isset($subsections) && count($subsections) > 0)
                                <div class="mt-1 bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md inline-block">
                                    <span class="text-sm font-medium">Viewing {{ $subsections[$subsection] ?? 'Unknown Section' }} - {{ $beds->count() }} beds (Total ward beds: {{ $ward->beds->count() }})</span>
                                </div>
                            @endif
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ $ward->description }}
                            </p>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Floor: {{ $ward->floor ?? 'N/A' }} | Capacity: {{ $ward->capacity }} beds | Type: {{ ucfirst(str_replace('_', ' ', $ward->type)) }}
                            </p>
                        </div>
                        <div class="status-pills">
                            <span class="status-count bg-green-50 border border-green-200 text-green-700">
                                Available: <span class="font-bold ml-1">{{ $beds->where('status', 'available')->count() }}</span>
                            </span>
                            <span class="status-count bg-red-50 border border-red-200 text-red-700">
                                Occupied: <span class="font-bold ml-1">{{ $beds->where('status', 'occupied')->count() }}</span>
                            </span>
                            <span class="status-count bg-yellow-50 border border-yellow-200 text-yellow-700">
                                Maintenance: <span class="font-bold ml-1">{{ $beds->where('status', 'maintenance')->count() }}</span>
                            </span>
                            <span class="status-count bg-blue-50 border border-blue-200 text-blue-700">
                                Cleaning: <span class="font-bold ml-1">{{ $beds->where('status', 'cleaning')->count() }}</span>
                            </span>
                            <span class="status-count bg-purple-50 border border-purple-200 text-purple-700">
                                Reserved: <span class="font-bold ml-1">{{ $beds->where('status', 'reserved')->count() }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Nurse Information Section -->
                <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                    <div class="flex flex-col sm:flex-row justify-between">
                        <div>
                            <h4 class="text-md font-medium text-blue-800">Nurses on Duty</h4>
                            <div class="mt-1">
                                @if(count($nursesOnDuty) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($nursesOnDuty as $nurse)
                                            @php
                                                $nurseShift = $nurse->shiftSchedules()
                                                    ->where('ward_id', $ward->id)
                                                    ->where('schedule_date', now()->format('Y-m-d'))
                                                    ->where('status', '!=', 'cancelled')
                                                    ->where('start_time', '<=', now()->format('H:i:s'))
                                                    ->where('end_time', '>=', now()->format('H:i:s'))
                                                    ->first();
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <span class="font-semibold">{{ $nurse->name }}</span>
                                                @if($nurseShift)
                                                    <span class="ml-1 text-blue-600">
                                                        ({{ $nurseShift->shift }} {{ date('g:i A', strtotime($nurseShift->start_time)) }}-{{ date('g:i A', strtotime($nurseShift->end_time)) }})
                                                    </span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No nurses currently on duty</p>
                                @endif
                            </div>
                            <div class="mt-3">
                                <button id="viewShiftScheduleBtn" onclick="window.location.href='{{ route('shift-schedule.ward', $ward) }}'" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none">
                                    View Full Shift Schedule
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-0">
                            <h4 class="text-md font-medium text-blue-800">Patient-to-Nurse Ratio</h4>
                            <div class="mt-1 text-center">
                                @if(count($nursesOnDuty) > 0)
                                    <span class="text-xl font-bold {{ $patientNurseRatio > 5 ? 'text-red-600' : 'text-blue-600' }}">
                                        {{ $patientNurseRatio }}:1
                                    </span>
                                    <p class="text-xs text-gray-500">
                                        {{ $beds->where('status', 'occupied')->count() }} patients / {{ $nursesOnDuty->count() }} nurses
                                    </p>
                                @else
                                    <span class="text-xl font-bold text-red-600">No nurses</span>
                                    <p class="text-xs text-gray-500">
                                        {{ $beds->where('status', 'occupied')->count() }} patients / 0 nurses
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    @if(isset($subsections) && count($subsections) > 0)
                    <div class="mb-6 flex justify-center">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            @foreach($subsections as $index => $sectionName)
                                <a href="{{ route('bed-management.bed-map', ['ward_id' => $ward->id, 'consultant_id' => $consultantId ?? null, 'subsection' => $index]) }}"
                                    class="px-4 py-2 text-sm font-medium 
                                    {{ $subsection == $index 
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700' 
                                        : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300' }}
                                    {{ $index === 0 ? 'rounded-l-md border' : 'border-t border-b border-r' }}
                                    {{ $index === count($subsections) - 1 ? 'rounded-r-md' : '' }}
                                    focus:z-10 focus:ring-2 focus:ring-indigo-500 focus:text-indigo-500">
                                    {{ $sectionName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="bed-grid">
                        @foreach($beds as $bed)
                            <div class="bed-box relative p-4 border rounded-lg 
                                @if($bed->status === 'available') bg-green-50 border-green-300
                                @elseif($bed->status === 'occupied') bg-red-50 border-red-300
                                @elseif($bed->status === 'maintenance') bg-yellow-50 border-yellow-300
                                @elseif($bed->status === 'cleaning') bg-blue-50 border-blue-300
                                @elseif($bed->status === 'reserved') bg-purple-50 border-purple-300
                                @else bg-gray-50 border-gray-300
                                @endif">
                                <div class="bed-box-header flex justify-between items-start">
                                    <span class="font-medium">Bed {{ $bed->bed_number }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        @if($bed->status === 'available') bg-green-100 text-green-800
                                        @elseif($bed->status === 'occupied') bg-red-100 text-red-800
                                        @elseif($bed->status === 'maintenance') bg-yellow-100 text-yellow-800
                                        @elseif($bed->status === 'cleaning') bg-blue-100 text-blue-800
                                        @elseif($bed->status === 'reserved') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($bed->status) }}
                                    </span>
                                </div>
                                
                                <div class="bed-box-content mt-2 text-sm">
                                    <p class="text-gray-600">Type: {{ ucfirst(str_replace('_', ' ', $bed->type)) }}</p>
                                    
                                    @if($bed->status === 'occupied')
                                        @php
                                            $currentAdmission = $bed->currentAdmission;
                                            $currentPatient = $currentAdmission ? $currentAdmission->patient : null;
                                        @endphp
                                        @if($currentPatient)
                                            <div class="patient-details mt-2">
                                                <div class="font-medium text-red-800">{{ $currentPatient->full_name }}</div>
                                                <div class="text-xs text-gray-600">MRN: {{ $currentPatient->mrn }}</div>
                                                <div class="text-xs text-gray-600">Admitted: {{ $currentAdmission->admission_date->format('d/m/Y') }}</div>
                                                @if($currentAdmission->consultant)
                                                    <div class="mt-1 text-xs bg-red-50 p-1 rounded">
                                                        <div class="font-medium text-red-700">Consultant:</div>
                                                        <div class="text-red-800">{{ $currentAdmission->consultant->name }}</div>
                                                        @if($currentAdmission->consultant->specialty)
                                                            <div class="text-xs text-red-600">{{ $currentAdmission->consultant->specialty }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <!-- Assigned Nurses Section -->
                                                @if(isset($nursesPerBed) && isset($nursesPerBed[$bed->id]) && $nursesPerBed[$bed->id]->count() > 0)
                                                    <div class="mt-1">
                                                        <div class="text-xs text-blue-600 font-medium">Assigned Nurse(s):</div>
                                                        <div class="flex flex-wrap gap-1 mt-0.5">
                                                            @foreach($nursesPerBed[$bed->id] as $assignedNurse)
                                                                <span class="px-1.5 py-0.5 bg-blue-50 text-xs text-blue-700 rounded">
                                                                    {{ $assignedNurse->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Nurse Assignment Button -->
                                                <div class="mt-2">
                                                    <button type="button" 
                                                            class="inline-flex items-center px-2 py-1 border border-blue-300 text-xs leading-4 font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                            onclick="openAssignNurseModal({{ $currentPatient->id }}, '{{ $currentPatient->full_name }}', '{{ $bed->bed_number }}', {{ $bed->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                        Assign Nurse
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                
                                <div class="bed-box-footer mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('beds.show', $bed) }}" class="text-xs text-indigo-600 hover:text-indigo-900">Details</a>
                                    
                                    @if($bed->status === 'available')
                                        <a href="{{ route('beds.admit-form', $bed) }}" class="text-xs text-green-600 hover:text-green-900">Admit</a>
                                    @endif
                                    
                                    @if($bed->status === 'occupied')
                                        @php
                                            // We've already updated this section above
                                        @endphp
                                        
                                        @if($currentPatient)
                                            <a href="{{ route('vital-signs.create-for-admission', ['admission' => $currentAdmission, 'from' => 'bed-map', 'ward_id' => $ward->id, 'subsection' => $subsection ?? null, 'consultant_id' => $consultantId ?? null]) }}" class="text-xs text-red-600 hover:text-red-900 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                Record Vitals
                                            </a>
                                        @endif
                                    @endif
                                    
                                    @if($bed->status === 'cleaning')
                                        <form action="{{ route('beds.mark-cleaning-complete', $bed) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-900">Mark Clean</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- All Wards Overview -->
            <div class="space-y-8">
                @if(isset($consultantId) && $consultantId)
                    @php
                        $filteredConsultant = $consultants->firstWhere('id', $consultantId);
                    @endphp
                    @if($filteredConsultant)
                        <div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-md mb-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                <span class="font-medium">Showing {{ $totalFilteredBeds }} patient(s) assigned to: {{ $filteredConsultant->name }} ({{ $filteredConsultant->specialty }})</span>
                            </div>
                        </div>
                    @endif
                @endif
                @foreach($wards as $ward)
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                <div class="mb-4 md:mb-0">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        {{ $ward->name }} - {{ $ward->code }}
                                    </h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                        {{ $ward->beds->count() }} beds | {{ $ward->beds->where('status', 'available')->count() }} available
                                    </p>
                                </div>
                                
                                <div class="flex flex-col md:flex-row">
                                    <div class="status-pills mb-4 md:mb-0 md:mr-4">
                                        <span class="status-count bg-green-50 border border-green-200 text-green-700">
                                            Available: <span class="font-bold ml-1">{{ $ward->beds->where('status', 'available')->count() }}</span>
                                        </span>
                                        <span class="status-count bg-red-50 border border-red-200 text-red-700">
                                            Occupied: <span class="font-bold ml-1">{{ $ward->beds->where('status', 'occupied')->count() }}</span>
                                        </span>
                                        <span class="status-count bg-yellow-50 border border-yellow-200 text-yellow-700">
                                            Maintenance: <span class="font-bold ml-1">{{ $ward->beds->where('status', 'maintenance')->count() }}</span>
                                        </span>
                                        <span class="status-count bg-blue-50 border border-blue-200 text-blue-700">
                                            Cleaning: <span class="font-bold ml-1">{{ $ward->beds->where('status', 'cleaning')->count() }}</span>
                                        </span>
                                        <span class="status-count bg-purple-50 border border-purple-200 text-purple-700">
                                            Reserved: <span class="font-bold ml-1">{{ $ward->beds->where('status', 'reserved')->count() }}</span>
                                        </span>
                                    </div>
                                    
                                    <a href="{{ route('bed-management.bed-map', ['ward_id' => $ward->id]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-fit self-center">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nurse Information Row -->
                        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                            <div class="flex flex-col sm:flex-row justify-between">
                                <div>
                                    <h4 class="text-md font-medium text-blue-800">Nurses on Duty</h4>
                                    <div class="mt-1">
                                        @if($wardStats[$ward->id]['nursesCount'] > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($wardStats[$ward->id]['nursesOnDuty'] as $nurse)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $nurse->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">No nurses currently on duty</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0">
                                    <h4 class="text-md font-medium text-blue-800">Patient-to-Nurse Ratio</h4>
                                    <div class="mt-1 text-center">
                                        @if($wardStats[$ward->id]['nursesCount'] > 0)
                                            <span class="text-xl font-bold {{ $wardStats[$ward->id]['patientNurseRatio'] > 5 ? 'text-red-600' : 'text-blue-600' }}">
                                                {{ $wardStats[$ward->id]['patientNurseRatio'] }}:1
                                            </span>
                                            <p class="text-xs text-gray-500">
                                                {{ $ward->beds->where('status', 'occupied')->count() }} patients / {{ $wardStats[$ward->id]['nursesCount'] }} nurses
                                            </p>
                                        @else
                                            <span class="text-xl font-bold text-red-600">No nurses</span>
                                            <p class="text-xs text-gray-500">
                                                {{ $ward->beds->where('status', 'occupied')->count() }} patients / 0 nurses
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Fullscreen Footer -->
        <div class="fullscreen-footer">
            <div id="datetime" class="text-sm font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="datetime-text"></span>
            </div>
            <div class="text-sm flex items-center">
                <span class="text-indigo-600 font-medium">QMed</span>
                <span class="mx-1">©</span>
                <span>2025. All rights reserved.</span>
            </div>
        </div>
    </div>

    <!-- Inline Script for Fullscreen Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const fullscreenContainer = document.getElementById('fullscreenContainer');
            const datetimeElement = document.getElementById('datetime');
            let datetimeInterval;
            
            // Function to update date and time
            function updateDateTime() {
                const now = new Date();
                
                // Format date: Monday, January 1, 2025
                const dateOptions = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric'
                };
                
                // Format time: 12:34:56 PM
                const timeOptions = {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                };
                
                const dateString = now.toLocaleDateString('en-US', dateOptions);
                const timeString = now.toLocaleTimeString('en-US', timeOptions);
                
                // Combine date and time with a separator
                datetimeElement.querySelector('#datetime-text').textContent = `${dateString} | ${timeString}`;
            }
            
            // Function to hide/show navigation bar and header
            function toggleHeaderVisibility(hide) {
                const headerElements = document.querySelectorAll('header, nav, .navigation-container, #app > nav, .navbar');
                headerElements.forEach(el => {
                    if (el) {
                        el.style.display = hide ? 'none' : '';
                    }
                });
                
                // Also try to find the header by position
                const possibleHeaders = document.querySelectorAll('body > div > nav, body > nav, #app > div > nav');
                possibleHeaders.forEach(el => {
                    if (el) {
                        el.style.display = hide ? 'none' : '';
                    }
                });
            }
            
            fullscreenBtn.addEventListener('click', function() {
                try {
                    if (!document.fullscreenElement && 
                        !document.webkitFullscreenElement && 
                        !document.mozFullScreenElement && 
                        !document.msFullscreenElement) {
                        
                        // Use our container for fullscreen
                        const element = fullscreenContainer;
                        
                        console.log('Attempting to enter fullscreen mode');
                        
                        if (element.requestFullscreen) {
                            element.requestFullscreen();
                        } else if (element.mozRequestFullScreen) { /* Firefox */
                            element.mozRequestFullScreen();
                        } else if (element.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                            element.webkitRequestFullscreen();
                        } else if (element.msRequestFullscreen) { /* IE/Edge */
                            element.msRequestFullscreen();
                        } else {
                            alert('Fullscreen is not supported by your browser.');
                            return;
                        }
                        
                        // Hide header and navigation after entering fullscreen
                        setTimeout(() => toggleHeaderVisibility(true), 100);
                        
                        // Start updating date and time
                        updateDateTime();
                        datetimeInterval = setInterval(updateDateTime, 1000);
                        
                        fullscreenBtn.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Exit Full Screen
                        `;
                    } else {
                        console.log('Attempting to exit fullscreen mode');
                        
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.mozCancelFullScreen) { /* Firefox */
                            document.mozCancelFullScreen();
                        } else if (document.webkitExitFullscreen) { /* Chrome, Safari & Opera */
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) { /* IE/Edge */
                            document.msExitFullscreen();
                        }
                        
                        // Show header and navigation after exiting fullscreen
                        setTimeout(() => toggleHeaderVisibility(false), 100);
                        
                        // Stop updating date and time
                        clearInterval(datetimeInterval);
                        
                        fullscreenBtn.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                            Toggle Full Screen
                        `;
                    }
                } catch (error) {
                    console.error('Fullscreen error:', error);
                    alert('There was an error entering fullscreen mode: ' + error.message);
                }
            });
            
            // Listen for fullscreen change events to update button text and header visibility
            document.addEventListener('fullscreenchange', handleFullscreenChange);
            document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
            document.addEventListener('mozfullscreenchange', handleFullscreenChange);
            document.addEventListener('MSFullscreenChange', handleFullscreenChange);
            
            function handleFullscreenChange() {
                if (!document.fullscreenElement && 
                    !document.webkitFullscreenElement && 
                    !document.mozFullScreenElement && 
                    !document.msFullscreenElement) {
                    // Exited fullscreen
                    toggleHeaderVisibility(false);
                    clearInterval(datetimeInterval);
                    
                    fullscreenBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                        </svg>
                        Toggle Full Screen
                    `;
                } else {
                    // Entered fullscreen
                    toggleHeaderVisibility(true);
                    updateDateTime();
                    datetimeInterval = setInterval(updateDateTime, 1000);
                    
                    fullscreenBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Exit Full Screen
                    `;
                }
            }
        });
    </script>
    </div>
    
    <!-- Fullscreen footer -->
    <div class="fullscreen-footer">
        <div>
            <span class="text-sm">SmartWard Bed Management</span>
        </div>
        <div>
            <button id="exitFullscreenBtn" class="text-sm text-indigo-600 hover:text-indigo-800">Exit Fullscreen</button>
        </div>
    </div>
    
    <!-- Assign Nurse Modal -->
    <div id="assignNurseModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div id="assignNurseModalOverlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            
            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="assignNurseForm" action="{{ route('nurses.assign-to-patient') }}" method="POST">
                    @csrf
                    <input type="hidden" id="patientIdInput" name="patient_id" value="">
                    <input type="hidden" id="redirectToInput" name="redirect_to" value="{{ url()->full() }}">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Assign Nurse to Patient
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="patientName"></p>
                                    <p class="text-sm text-gray-500">Bed: <span id="bedNumber"></span></p>
                                    
                                    <div class="mt-4">
                                        <label for="nurseSelect" class="block text-sm font-medium text-gray-700">Select Nurse</label>
                                        <select id="nurseSelect" name="nurse_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            @if(isset($nursesOnDuty) && $nursesOnDuty->count() > 0)
                                                @foreach($nursesOnDuty as $nurse)
                                                    <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No nurses on duty</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Assign
                        </button>
                        <button type="button" id="cancelAssignNurse" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Modal functionality
        function openAssignNurseModal(patientId, patientFullName, bedNum, bedId) {
            document.getElementById('patientIdInput').value = patientId;
            document.getElementById('patientName').textContent = 'Patient: ' + patientFullName;
            document.getElementById('bedNumber').textContent = bedNum;
            document.getElementById('assignNurseModal').classList.remove('hidden');
        }
        
        document.getElementById('assignNurseModalOverlay').addEventListener('click', function() {
            document.getElementById('assignNurseModal').classList.add('hidden');
        });
        
        document.getElementById('cancelAssignNurse').addEventListener('click', function() {
            document.getElementById('assignNurseModal').classList.add('hidden');
        });
        
        // Full screen functionality
        const container = document.getElementById('fullscreenContainer');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
        
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    container.requestFullscreen().catch(err => {
                        console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });
        }
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.addEventListener('click', function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
            });
        }
    </script>
</div>
@endsection