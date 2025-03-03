@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Vital Signs for {{ $vitalSign->patient->full_name }}
                    </h2>
                    
                    <div class="flex space-x-2">
                        @if($vitalSign->admission_id)
                            <a href="{{ route('admissions.show', $vitalSign->admission_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Back to Admission
                            </a>
                        @else
                            <a href="{{ route('patients.show', $vitalSign->patient_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Back to Patient
                            </a>
                        @endif
                        
                        <a href="{{ route('vital-signs.edit', $vitalSign) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vital Sign Details</h3>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Recorded at</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vitalSign->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Recorded by</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vitalSign->measured_by ?? 'Not specified' }}</dd>
                            </div>
                            
                            @if($vitalSign->admission_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admission</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('admissions.show', $vitalSign->admission_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Admission Details
                                    </a>
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->device_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Measuring Device</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vitalSign->device_model }} ({{ $vitalSign->device_id }})</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Measurements</h3>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            @if($vitalSign->temperature)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                                <dd class="mt-1 text-sm {{ !$vitalSign->isTemperatureNormal() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->temperature }} °C
                                    @if(!$vitalSign->isTemperatureNormal())
                                        <span class="text-xs ml-1">(Abnormal)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->heart_rate)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Heart Rate</dt>
                                <dd class="mt-1 text-sm {{ !$vitalSign->isHeartRateNormal() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->heart_rate }} bpm
                                    @if(!$vitalSign->isHeartRateNormal())
                                        <span class="text-xs ml-1">(Abnormal)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->systolic_bp && $vitalSign->diastolic_bp)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Blood Pressure</dt>
                                <dd class="mt-1 text-sm {{ !$vitalSign->isBpNormal() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->systolic_bp }}/{{ $vitalSign->diastolic_bp }} mmHg
                                    @if(!$vitalSign->isBpNormal())
                                        <span class="text-xs ml-1">(Abnormal)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->respiratory_rate)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Respiratory Rate</dt>
                                <dd class="mt-1 text-sm {{ !$vitalSign->isRespiratoryRateNormal() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->respiratory_rate }} breaths/min
                                    @if(!$vitalSign->isRespiratoryRateNormal())
                                        <span class="text-xs ml-1">(Abnormal)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->oxygen_saturation)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Oxygen Saturation</dt>
                                <dd class="mt-1 text-sm {{ !$vitalSign->isOxygenSaturationNormal() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->oxygen_saturation }}%
                                    @if(!$vitalSign->isOxygenSaturationNormal())
                                        <span class="text-xs ml-1">(Abnormal)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->blood_glucose)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Blood Glucose</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vitalSign->blood_glucose }} mmol/L</dd>
                            </div>
                            @endif
                            
                            @if($vitalSign->pain_level !== null)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pain Level</dt>
                                <dd class="mt-1 text-sm {{ $vitalSign->pain_level > 3 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $vitalSign->pain_level }}/10
                                    @if($vitalSign->pain_level > 3)
                                        <span class="text-xs ml-1">(Significant)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Early Warning Score (EWS)</dt>
                                <dd class="mt-1">
                                    @php
                                        $ewsScore = $vitalSign->ews_score ?? $vitalSign->calculateEWS();
                                        $riskLevel = $vitalSign->getEwsRiskLevel();
                                        $textColorClass = 'text-gray-900';
                                        $bgColorClass = '';
                                        
                                        if ($riskLevel === 'Low') {
                                            $textColorClass = 'text-green-800';
                                            $bgColorClass = 'bg-green-100';
                                        } elseif ($riskLevel === 'Medium') {
                                            $textColorClass = 'text-orange-800';
                                            $bgColorClass = 'bg-orange-100';
                                        } elseif ($riskLevel === 'High') {
                                            $textColorClass = 'text-red-800';
                                            $bgColorClass = 'bg-red-100';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $textColorClass }} {{ $bgColorClass }}">
                                        {{ $ewsScore !== null ? $ewsScore : 'Not available' }}
                                        <span class="ml-1">{{ $riskLevel !== 'Unknown' ? "($riskLevel risk)" : '' }}</span>
                                    </span>
                                    @if($riskLevel === 'Medium' || $riskLevel === 'High')
                                        <p class="mt-1 text-sm text-red-600">
                                            @if($riskLevel === 'Medium')
                                                Medium risk: Consider increasing observation frequency and informing medical staff.
                                            @elseif($riskLevel === 'High')
                                                High risk: Urgent medical review required. Consider escalation to critical care team.
                                            @endif
                                        </p>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                @if($vitalSign->notes)
                <div class="mt-6 border rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ $vitalSign->notes }}</p>
                </div>
                @endif
                
                <div class="mt-6 flex justify-end space-x-3">
                    <form action="{{ route('vital-signs.destroy', $vitalSign) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vital sign record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 