@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">{{ $ward->name }} Ward Shift Schedule</h2>
            
            <div class="flex space-x-3">
                <a href="{{ route('shift-schedule.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    Back to All Schedules
                </a>
                
                <a href="{{ route('shift-schedule.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Add New Shift
                </a>
            </div>
        </div>
        
        <!-- Ward Info -->
        <div class="mb-6 bg-blue-50 p-4 rounded-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="block text-sm font-medium text-gray-700">Ward Code</span>
                    <span class="block font-semibold">{{ $ward->code }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700">Floor</span>
                    <span class="block font-semibold">{{ $ward->floor }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700">Capacity</span>
                    <span class="block font-semibold">{{ $ward->capacity }} beds</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700">Status</span>
                    <span class="block font-semibold">{{ $ward->status ?? 'Active' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Date Filter -->
        <div class="mb-6 p-4 bg-gray-50 rounded-md">
            <form action="{{ route('shift-schedule.ward', $ward->id) }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Weekly Calendar View -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Weekly Schedule</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b"></th>
                            @foreach($dateRange as $date)
                                <th class="py-2 px-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-l">
                                    <div>{{ $date->format('D') }}</div>
                                    <div>{{ $date->format('M d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Morning Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-blue-50 font-medium">
                                Morning<br>
                                <span class="text-xs text-gray-600">(7:00 AM - 3:00 PM)</span>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $groupedSchedules[$date->format('Y-m-d')]['Morning'] ?? collect();
                                        @endphp
                                        
                                        @foreach($daySchedules as $schedule)
                                            <div class="mb-1 p-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-blue-600">
                                                    <span class="px-1 py-0.5 rounded bg-blue-50">{{ ucfirst($schedule->status) }}</span>
                                                </div>
                                                <div class="flex space-x-1 mt-1">
                                                    <a href="{{ route('shift-schedule.edit', $schedule->id) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        
                        <!-- Evening Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-indigo-50 font-medium">
                                Evening<br>
                                <span class="text-xs text-gray-600">(3:00 PM - 11:00 PM)</span>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $groupedSchedules[$date->format('Y-m-d')]['Evening'] ?? collect();
                                        @endphp
                                        
                                        @foreach($daySchedules as $schedule)
                                            <div class="mb-1 p-1 bg-indigo-100 text-indigo-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-indigo-600">
                                                    <span class="px-1 py-0.5 rounded bg-indigo-50">{{ ucfirst($schedule->status) }}</span>
                                                </div>
                                                <div class="flex space-x-1 mt-1">
                                                    <a href="{{ route('shift-schedule.edit', $schedule->id) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        
                        <!-- Night Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-purple-50 font-medium">
                                Night<br>
                                <span class="text-xs text-gray-600">(11:00 PM - 7:00 AM)</span>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $groupedSchedules[$date->format('Y-m-d')]['Night'] ?? collect();
                                        @endphp
                                        
                                        @foreach($daySchedules as $schedule)
                                            <div class="mb-1 p-1 bg-purple-100 text-purple-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-purple-600">
                                                    <span class="px-1 py-0.5 rounded bg-purple-50">{{ ucfirst($schedule->status) }}</span>
                                                </div>
                                                <div class="flex space-x-1 mt-1">
                                                    <a href="{{ route('shift-schedule.edit', $schedule->id) }}" class="text-xs text-purple-600 hover:underline">Edit</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        
                        <!-- Custom Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-gray-50 font-medium">Custom</td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $groupedSchedules[$date->format('Y-m-d')]['Custom'] ?? collect();
                                        @endphp
                                        
                                        @foreach($daySchedules as $schedule)
                                            <div class="mb-1 p-1 bg-gray-100 text-gray-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-gray-600">
                                                    <span class="px-1 py-0.5 rounded bg-gray-50">{{ ucfirst($schedule->status) }}</span>
                                                </div>
                                                <div class="flex space-x-1 mt-1">
                                                    <a href="{{ route('shift-schedule.edit', $schedule->id) }}" class="text-xs text-gray-600 hover:underline">Edit</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="bg-gray-50 p-4 rounded-md">
            <h3 class="text-lg font-semibold mb-4">Ward Shift Coverage Summary</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-3 bg-white rounded-md shadow-sm">
                    <h4 class="text-md font-medium text-blue-800 mb-2">
                        Morning Shift 
                        <span class="font-normal text-sm">(7:00 AM - 3:00 PM)</span>
                    </h4>
                    <p class="text-sm">
                        @php
                            $totalDays = count($dateRange);
                            $daysWithMorningCoverage = 0;
                            
                            foreach($dateRange as $date) {
                                $schedules = $groupedSchedules[$date->format('Y-m-d')]['Morning'] ?? collect();
                                if ($schedules->count() > 0) {
                                    $daysWithMorningCoverage++;
                                }
                            }
                            
                            $coveragePercentage = $totalDays > 0 ? round(($daysWithMorningCoverage / $totalDays) * 100) : 0;
                        @endphp
                        <span class="font-semibold">{{ $coveragePercentage }}%</span> coverage for the selected period.<br>
                        <span class="text-xs text-gray-600">{{ $daysWithMorningCoverage }} of {{ $totalDays }} days have morning shift coverage.</span>
                    </p>
                </div>
                
                <div class="p-3 bg-white rounded-md shadow-sm">
                    <h4 class="text-md font-medium text-indigo-800 mb-2">
                        Evening Shift
                        <span class="font-normal text-sm">(3:00 PM - 11:00 PM)</span>
                    </h4>
                    <p class="text-sm">
                        @php
                            $daysWithEveningCoverage = 0;
                            
                            foreach($dateRange as $date) {
                                $schedules = $groupedSchedules[$date->format('Y-m-d')]['Evening'] ?? collect();
                                if ($schedules->count() > 0) {
                                    $daysWithEveningCoverage++;
                                }
                            }
                            
                            $coveragePercentage = $totalDays > 0 ? round(($daysWithEveningCoverage / $totalDays) * 100) : 0;
                        @endphp
                        <span class="font-semibold">{{ $coveragePercentage }}%</span> coverage for the selected period.<br>
                        <span class="text-xs text-gray-600">{{ $daysWithEveningCoverage }} of {{ $totalDays }} days have evening shift coverage.</span>
                    </p>
                </div>
                
                <div class="p-3 bg-white rounded-md shadow-sm">
                    <h4 class="text-md font-medium text-purple-800 mb-2">
                        Night Shift
                        <span class="font-normal text-sm">(11:00 PM - 7:00 AM)</span>
                    </h4>
                    <p class="text-sm">
                        @php
                            $daysWithNightCoverage = 0;
                            
                            foreach($dateRange as $date) {
                                $schedules = $groupedSchedules[$date->format('Y-m-d')]['Night'] ?? collect();
                                if ($schedules->count() > 0) {
                                    $daysWithNightCoverage++;
                                }
                            }
                            
                            $coveragePercentage = $totalDays > 0 ? round(($daysWithNightCoverage / $totalDays) * 100) : 0;
                        @endphp
                        <span class="font-semibold">{{ $coveragePercentage }}%</span> coverage for the selected period.<br>
                        <span class="text-xs text-gray-600">{{ $daysWithNightCoverage }} of {{ $totalDays }} days have night shift coverage.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Check if URL has no date parameters and set defaults
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        // If no date parameters in URL, the controller already set the defaults
        // This helps with visual feedback on the form
    });
</script>
@endpush 