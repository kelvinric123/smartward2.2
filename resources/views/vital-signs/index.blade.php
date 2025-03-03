@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Vital Signs for {{ $patient->full_name }}
                    </h2>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('patients.show', $patient) }}" class="text-indigo-600 hover:text-indigo-900">
                            Back to Patient
                        </a>
                        <a href="{{ route('vital-signs.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Record New Vital Signs
                        </a>
                    </div>
                </div>
                
                @if($vitalSigns->count() > 0)
                    <!-- Vital Signs Chart Section -->
                    <div class="mb-8">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Vital Signs Trends</h3>
                            
                            <div class="flex flex-wrap mb-4" id="chart-legend">
                                <!-- Legend items will be dynamically shown/hidden based on selected filter -->
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="temperature">
                                    <span class="inline-block w-4 h-4 mr-1 bg-red-500 rounded"></span>
                                    <span class="text-sm">Temperature</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="vitals">
                                    <span class="inline-block w-4 h-4 mr-1 bg-blue-500 rounded"></span>
                                    <span class="text-sm">Heart Rate</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="vitals">
                                    <span class="inline-block w-4 h-4 mr-1 bg-teal-500 rounded"></span>
                                    <span class="text-sm">Respiratory Rate</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="bp">
                                    <span class="inline-block w-4 h-4 mr-1 bg-purple-500 rounded"></span>
                                    <span class="text-sm">Systolic BP</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="bp">
                                    <span class="inline-block w-4 h-4 mr-1 bg-orange-500 rounded"></span>
                                    <span class="text-sm">Diastolic BP</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="vitals">
                                    <span class="inline-block w-4 h-4 mr-1 bg-yellow-500 rounded"></span>
                                    <span class="text-sm">O₂ Saturation</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="other">
                                    <span class="inline-block w-4 h-4 mr-1 bg-green-500 rounded"></span>
                                    <span class="text-sm">Blood Glucose</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="other">
                                    <span class="inline-block w-4 h-4 mr-1 bg-red-700 rounded"></span>
                                    <span class="text-sm">Pain Level</span>
                                </div>
                                <div class="flex items-center mr-4 mb-2 legend-item" data-group="ews">
                                    <span class="inline-block w-4 h-4 mr-1 bg-black rounded"></span>
                                    <span class="text-sm">EWS Score</span>
                                </div>
                            </div>
                            
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="vitalSignsChart"></canvas>
                            </div>
                            
                            <div class="mt-3 flex justify-center">
                                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    <button type="button" class="chart-toggle px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-l-lg hover:bg-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-500 active-chart" data-type="all">
                                        All
                                    </button>
                                    <button type="button" class="chart-toggle px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-500" data-type="temperature">
                                        Temperature
                                    </button>
                                    <button type="button" class="chart-toggle px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-500" data-type="vitals">
                                        Vitals
                                    </button>
                                    <button type="button" class="chart-toggle px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-500" data-type="bp">
                                        Blood Pressure
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heart Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Pressure</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respiratory Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O₂ Saturation</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Glucose</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pain Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EWS Score</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vitalSigns as $vitalSign)
                                    <tr class="{{ $vitalSign->hasAbnormalValues() ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isTemperatureNormal() && $vitalSign->temperature ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $vitalSign->temperature ? $vitalSign->temperature . ' °C' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isHeartRateNormal() && $vitalSign->heart_rate ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isBpNormal() && $vitalSign->systolic_bp && $vitalSign->diastolic_bp ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ ($vitalSign->systolic_bp && $vitalSign->diastolic_bp) ? $vitalSign->systolic_bp . '/' . $vitalSign->diastolic_bp . ' mmHg' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isRespiratoryRateNormal() && $vitalSign->respiratory_rate ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . ' breaths/min' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isOxygenSaturationNormal() && $vitalSign->oxygen_saturation ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->blood_glucose ? $vitalSign->blood_glucose . ' mmol/L' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $vitalSign->pain_level > 3 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $vitalSign->pain_level !== null ? $vitalSign->pain_level . '/10' : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                                {{ $ewsScore !== null ? $ewsScore : '-' }}
                                                <span class="ml-1 text-xs">{{ $riskLevel !== 'Unknown' ? "($riskLevel)" : '' }}</span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $vitalSign->measured_by ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('vital-signs.edit', $vitalSign) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            
                                            <form method="POST" action="{{ route('vital-signs.destroy', $vitalSign) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $vitalSigns->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-md text-center">
                        <p class="text-gray-500">No vital signs have been recorded for this patient.</p>
                        <a href="{{ route('vital-signs.create', ['patient_id' => $patient->id]) }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Record First Vital Signs
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($vitalSigns->count() > 0)
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get chart data from Laravel
        const labels = @json($labels);
        const datasetsRaw = @json($datasets);
        
        // Create Chart.js chart
        const ctx = document.getElementById('vitalSignsChart').getContext('2d');
        let vitalSignsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasetsRaw
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date/Time'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Value'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Function to update the legend based on filter
        function updateLegend(chartType) {
            // Hide all legend items first
            const legendItems = document.querySelectorAll('.legend-item');
            legendItems.forEach(item => {
                item.style.display = 'none';
            });
            
            // Show only relevant legend items
            switch(chartType) {
                case 'temperature':
                    document.querySelectorAll('.legend-item[data-group="temperature"]').forEach(item => {
                        item.style.display = 'flex';
                    });
                    break;
                case 'vitals':
                    document.querySelectorAll('.legend-item[data-group="vitals"]').forEach(item => {
                        item.style.display = 'flex';
                    });
                    break;
                case 'bp':
                    document.querySelectorAll('.legend-item[data-group="bp"]').forEach(item => {
                        item.style.display = 'flex';
                    });
                    break;
                case 'all':
                default:
                    legendItems.forEach(item => {
                        item.style.display = 'flex';
                    });
                    break;
            }
        }
        
        // Initialize the legend
        updateLegend('all');
        
        // Toggle chart visibility based on selected parameters
        const chartToggleButtons = document.querySelectorAll('.chart-toggle');
        chartToggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Reset active state
                chartToggleButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'active-chart');
                    btn.classList.add('bg-white', 'text-gray-900');
                });
                
                // Set active state on clicked button
                this.classList.remove('bg-white', 'text-gray-900');
                this.classList.add('bg-indigo-600', 'text-white', 'active-chart');
                
                const chartType = this.getAttribute('data-type');
                
                // Update legend based on the selected filter
                updateLegend(chartType);
                
                // Update chart based on selected type
                switch(chartType) {
                    case 'temperature':
                        vitalSignsChart.data.datasets = [datasetsRaw[0]]; // Temperature only
                        break;
                    case 'vitals':
                        vitalSignsChart.data.datasets = [
                            datasetsRaw[1], // Heart Rate
                            datasetsRaw[2], // Respiratory Rate
                            datasetsRaw[5]  // Oxygen Saturation
                        ];
                        break;
                    case 'bp':
                        vitalSignsChart.data.datasets = [
                            datasetsRaw[3], // Systolic BP
                            datasetsRaw[4]  // Diastolic BP
                        ];
                        break;
                    case 'all':
                    default:
                        vitalSignsChart.data.datasets = datasetsRaw;
                        break;
                }
                
                vitalSignsChart.update();
            });
        });
    });
</script>
@endif
@endsection 