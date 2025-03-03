@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Record Vital Signs
                        @if($patient)
                            for {{ $patient->full_name }}
                        @endif
                    </h2>
                    
                    @if($patient)
                        <a href="{{ route('patients.show', $patient) }}" class="text-indigo-600 hover:text-indigo-900">
                            Back to Patient
                        </a>
                    @elseif($admission)
                        <a href="{{ route('admissions.show', $admission) }}" class="text-indigo-600 hover:text-indigo-900">
                            Back to Admission
                        </a>
                    @else
                        <a href="{{ route('patients.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            Back to Patients
                        </a>
                    @endif
                </div>
                
                <form action="{{ route('vital-signs.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if($patient)
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    @else
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                            <select id="patient_id" name="patient_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select a patient</option>
                                <!-- This would normally be populated with a list of patients -->
                            </select>
                            @error('patient_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    @if($admission)
                        <input type="hidden" name="admission_id" value="{{ $admission->id }}">
                    @elseif($patient && $patient->activeAdmission())
                        <div>
                            <label for="admission_id" class="block text-sm font-medium text-gray-700">Admission</label>
                            <select id="admission_id" name="admission_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">No admission (outpatient)</option>
                                @foreach($patient->admissions()->where('status', 'active')->get() as $admission)
                                    <option value="{{ $admission->id }}">
                                        Admitted on {{ $admission->admission_date->format('M d, Y') }} - {{ $admission->ward->name ?? 'Unknown ward' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('admission_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
                            <input type="number" step="0.1" name="temperature" id="temperature" placeholder="36.8" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('temperature')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="heart_rate" class="block text-sm font-medium text-gray-700">Heart Rate (bpm)</label>
                            <input type="number" name="heart_rate" id="heart_rate" placeholder="72" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('heart_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Respiratory Rate (breaths/min)</label>
                            <input type="number" name="respiratory_rate" id="respiratory_rate" placeholder="16" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('respiratory_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="systolic_bp" class="block text-sm font-medium text-gray-700">Systolic BP (mmHg)</label>
                                <input type="number" name="systolic_bp" id="systolic_bp" placeholder="120" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('systolic_bp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="diastolic_bp" class="block text-sm font-medium text-gray-700">Diastolic BP (mmHg)</label>
                                <input type="number" name="diastolic_bp" id="diastolic_bp" placeholder="80" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('diastolic_bp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700">Oxygen Saturation (%)</label>
                            <input type="number" min="0" max="100" name="oxygen_saturation" id="oxygen_saturation" placeholder="98" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('oxygen_saturation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="blood_glucose" class="block text-sm font-medium text-gray-700">Blood Glucose (mmol/L)</label>
                            <input type="number" step="0.1" name="blood_glucose" id="blood_glucose" placeholder="5.5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('blood_glucose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="pain_level" class="block text-sm font-medium text-gray-700">Pain Level (0-10)</label>
                            <input type="number" step="1" min="0" max="10" name="pain_level" id="pain_level" placeholder="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('pain_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Any additional observations or notes"></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="pt-5">
                        <div class="flex justify-end">
                            @if($patient)
                                <a href="{{ route('patients.show', $patient) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                    Cancel
                                </a>
                            @elseif($admission)
                                <a href="{{ route('admissions.show', $admission) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                    Cancel
                                </a>
                            @else
                                <a href="{{ route('patients.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                    Cancel
                                </a>
                            @endif
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Record Vital Signs
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 