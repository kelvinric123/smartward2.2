@extends('layouts.app')

@section('title', "Bed {$bed->bed_number} Details")

@section('header')
    Bed {{ $bed->bed_number }} Details - {{ $bed->ward->name }}
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between">
            <a href="{{ route('bed-management.bed-map', ['ward_id' => $bed->ward_id]) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Ward Map
            </a>

            <div class="flex space-x-3">
                <a href="{{ route('beds.edit', $bed) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Edit Bed
                </a>
            </div>
        </div>

        <!-- Bed Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Bed Information</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Bed Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bed->bed_number }}</dd>
                            </div>
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm">
                                    @php
                                        $statusColor = 'text-green-600 bg-green-100';
                                        if ($bed->status === 'occupied') {
                                            $statusColor = 'text-red-600 bg-red-100';
                                        } elseif ($bed->status === 'maintenance') {
                                            $statusColor = 'text-yellow-600 bg-yellow-100';
                                        } elseif ($bed->status === 'cleaning') {
                                            $statusColor = 'text-purple-600 bg-purple-100';
                                        } elseif ($bed->status === 'reserved') {
                                            $statusColor = 'text-blue-600 bg-blue-100';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                        {{ ucfirst($bed->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Ward</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bed->ward->name }}</dd>
                            </div>
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Ward Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $bed->ward->type ?? 'unknown')) }}</dd>
                            </div>
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Bed Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $bed->type ?? 'standard')) }}</dd>
                            </div>
                            <div class="col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bed->updated_at ? $bed->updated_at->format('M d, Y H:i') : 'N/A' }}</dd>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Features</h4>
                        <ul class="space-y-1 text-sm">
                            @if (!empty($bed->features))
                                @foreach(json_decode($bed->features, true) ?? [] as $feature)
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ ucfirst($feature) }}
                                    </li>
                                @endforeach
                            @else
                                <li class="text-gray-500">No special features</li>
                            @endif
                        </ul>

                        @if($bed->notes)
                            <h4 class="text-sm font-medium text-gray-500 mb-2 mt-4">Notes</h4>
                            <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                {{ $bed->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Occupant -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Current Occupant</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($bed->currentAdmission() && $bed->currentAdmission()->patient)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 font-medium">{{ strtoupper(substr($bed->currentAdmission()->patient->first_name, 0, 1) . substr($bed->currentAdmission()->patient->last_name, 0, 1)) }}</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-bold">{{ $bed->currentAdmission()->patient->full_name }}</h4>
                            <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">MRN: {{ $bed->currentAdmission()->patient->mrn }}</p>
                                    <p class="text-sm text-gray-500">Admitted: {{ $bed->currentAdmission()->admission_date->format('M d, Y') }}</p>
                                    <p class="text-sm text-gray-500">Expected Discharge: {{ $bed->currentAdmission()->expected_discharge_date ? $bed->currentAdmission()->expected_discharge_date->format('M d, Y') : 'Not specified' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Days in Hospital: {{ $bed->currentAdmission()->admission_date->diffInDays(now()) }}</p>
                                    <p class="text-sm text-gray-500">Diagnosis: {{ $bed->currentAdmission()->diagnosis ?? 'Not specified' }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex space-x-4">
                                <a href="{{ route('patients.show', ['patient' => $bed->currentAdmission()->patient, 'from' => 'bed-details', 'bed_id' => $bed->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View Patient Record</a>
                                <a href="{{ route('admissions.show', $bed->currentAdmission()) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View Admission Details</a>
                                
                                <form action="{{ route('beds.mark-available', $bed) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-900 bg-transparent border-0 p-0 cursor-pointer">Discharge Patient</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Current Occupant</h3>
                        <p class="mt-1 text-sm text-gray-500">This bed is currently {{ $bed->status }}.</p>
                        <div class="mt-6 flex justify-center space-x-4">
                            @if($bed->status === 'available')
                                <a href="{{ route('beds.admit-form', $bed) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Admit New Patient
                                </a>
                            @elseif($bed->status === 'cleaning')
                                <form action="{{ route('beds.mark-cleaning-complete', $bed) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Mark Cleaning Complete
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('beds.edit', $bed) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Edit Bed Status
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Admission History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Admission History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admitted</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discharged</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Length of Stay</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pastAdmissions as $admission)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $admission->patient->full_name }}</div>
                                    <div class="text-sm text-gray-500">MRN: {{ $admission->patient->mrn }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $admission->admission_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $admission->actual_discharge_date ? $admission->actual_discharge_date->format('M d, Y') : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($admission->actual_discharge_date)
                                            {{ $admission->admission_date->diffInDays($admission->actual_discharge_date) }} days
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No admission history available for this bed.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection 