@extends('layouts.app')

@section('title', 'Bed Statistics')

@section('header', 'Bed Management Statistics')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Total Admissions -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-500 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Admissions
                                </dt>
                                <dd class="mt-1">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $totalAdmissions }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Admissions -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-500 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </div>
                        <div class="flex-1 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Current Admissions
                                </dt>
                                <dd class="mt-1">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $currentAdmissions }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Length of Stay -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-500 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Avg. Length of Stay
                                </dt>
                                <dd class="mt-1">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($avgLos, 1) }} days
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ward Occupancy Rates -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ward Occupancy Rates</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ward Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy Rate</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupied/Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($wardOccupancy as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $data['ward'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($data['rate'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500 mt-1 inline-block">{{ number_format($data['rate']) }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data['occupied'] }}/{{ $data['total'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Occupancy Rate Over Time -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Patient Activity - {{ $timeRangeTitle }}</h3>
                
                <!-- Time Range Selector -->
                <div class="flex items-center space-x-2">
                    <label for="timeRange" class="text-sm text-gray-600">Time Period:</label>
                    <form id="timeRangeForm" action="{{ route('bed-management.statistics') }}" method="GET" class="inline-block">
                        <select id="timeRange" name="timeRange" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" onchange="this.form.submit()">
                            <option value="month" {{ $timeRange === 'month' ? 'selected' : '' }}>Last Month</option>
                            <option value="90days" {{ $timeRange === '90days' ? 'selected' : '' }}>Last 90 Days</option>
                            <option value="year" {{ $timeRange === 'year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(count($occupancyRateData) > 0)
                    <div class="h-96">
                        <canvas id="admissionsChart"></canvas>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-6">No historical data available yet.</p>
                @endif
            </div>
        </div>
    </div>

    @if(count($occupancyRateData) > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('admissionsChart').getContext('2d');
            const data = @json($occupancyRateData);
            
            const labels = data.map(item => item.formatted_date);
            const admissionsData = data.map(item => item.admissions);
            const activeData = data.map(item => item.active);
            const dischargesData = data.map(item => item.discharges);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'New Admissions',
                            data: admissionsData,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        },
                        {
                            label: 'Active Patients',
                            data: activeData,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        },
                        {
                            label: 'Discharges',
                            data: dischargesData,
                            backgroundColor: 'rgba(244, 63, 94, 0.2)',
                            borderColor: 'rgba(244, 63, 94, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Patients'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date ({{ $timeRange === "year" ? "MM/YYYY" : "DD/MM/YYYY" }})'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Patient Activity - {{ $timeRangeTitle }}'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
    @endif
@endsection 