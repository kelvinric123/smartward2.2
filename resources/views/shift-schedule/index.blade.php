@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Nurse Shift Schedule</h2>
            
            <a href="{{ route('shift-schedule.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Add New Shift
            </a>
        </div>
        
        <!-- Filters -->
        <div class="mb-6 p-4 bg-gray-50 rounded-md">
            <form action="{{ route('shift-schedule.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
                    <select name="ward_id" id="ward_id" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Wards</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}" {{ request('ward_id') == $ward->id ? 'selected' : '' }}>{{ $ward->name }}</option>
                        @endforeach
                    </select>
                </div>
                
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
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Ward Info if filtered by ward -->
        @if($selectedWard)
            <div class="mb-6 bg-blue-50 p-4 rounded-md">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold text-blue-800">{{ $selectedWard->name }} Ward Schedule</h3>
                    <a href="{{ route('shift-schedule.ward', $selectedWard->id) }}" class="text-blue-600 hover:underline">
                        View Detailed Ward Schedule
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Ward Code</span>
                        <span class="block font-semibold">{{ $selectedWard->code }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Floor</span>
                        <span class="block font-semibold">{{ $selectedWard->floor }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Capacity</span>
                        <span class="block font-semibold">{{ $selectedWard->capacity }} beds</span>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Calendar View -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Calendar View</h3>
            
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
                                Morning
                                <div class="text-xs font-normal">(7:00 AM - 3:00 PM)</div>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $calendarData[$date->format('Y-m-d')] ?? collect();
                                            $morningSchedules = $daySchedules->filter(function ($schedule) {
                                                return $schedule->shift === 'Morning';
                                            });
                                        @endphp
                                        
                                        @foreach($morningSchedules as $schedule)
                                            <div class="mb-1 p-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-blue-600">{{ $schedule->ward->name }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        
                        <!-- Evening Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-indigo-50 font-medium">
                                Evening
                                <div class="text-xs font-normal">(3:00 PM - 11:00 PM)</div>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $calendarData[$date->format('Y-m-d')] ?? collect();
                                            $eveningSchedules = $daySchedules->filter(function ($schedule) {
                                                return $schedule->shift === 'Evening';
                                            });
                                        @endphp
                                        
                                        @foreach($eveningSchedules as $schedule)
                                            <div class="mb-1 p-1 bg-indigo-100 text-indigo-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-indigo-600">{{ $schedule->ward->name }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        
                        <!-- Night Shift Row -->
                        <tr>
                            <td class="py-2 px-3 border-b bg-purple-50 font-medium">
                                Night
                                <div class="text-xs font-normal">(11:00 PM - 7:00 AM)</div>
                            </td>
                            @foreach($dateRange as $date)
                                <td class="py-2 px-3 border-b border-l align-top">
                                    <div class="min-h-[100px]">
                                        @php
                                            $daySchedules = $calendarData[$date->format('Y-m-d')] ?? collect();
                                            $nightSchedules = $daySchedules->filter(function ($schedule) {
                                                return $schedule->shift === 'Night';
                                            });
                                        @endphp
                                        
                                        @foreach($nightSchedules as $schedule)
                                            <div class="mb-1 p-1 bg-purple-100 text-purple-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-purple-600">{{ $schedule->ward->name }}</div>
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
                                            $daySchedules = $calendarData[$date->format('Y-m-d')] ?? collect();
                                            $customSchedules = $daySchedules->filter(function ($schedule) {
                                                return $schedule->shift === 'Custom';
                                            });
                                        @endphp
                                        
                                        @foreach($customSchedules as $schedule)
                                            <div class="mb-1 p-1 bg-gray-100 text-gray-800 rounded text-xs">
                                                <div class="font-medium">{{ $schedule->nurse->name }}</div>
                                                <div>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                <div class="text-xs text-gray-600">{{ $schedule->ward->name }}</div>
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
        
        <!-- List View -->
        <div>
            <h3 class="text-lg font-semibold mb-4">List View</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nurse</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ward</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $allSchedules = collect();
                            foreach ($calendarData as $daySchedules) {
                                $allSchedules = $allSchedules->merge($daySchedules);
                            }
                            $allSchedules = $allSchedules->sortBy([
                                ['schedule_date', 'asc'],
                                ['start_time', 'asc'],
                            ]);
                        @endphp
                        
                        @forelse($allSchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 text-sm">{{ $schedule->schedule_date->format('M d, Y') }}</td>
                                <td class="py-2 px-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($schedule->shift == 'Morning') bg-blue-100 text-blue-800
                                        @elseif($schedule->shift == 'Evening') bg-indigo-100 text-indigo-800
                                        @elseif($schedule->shift == 'Night') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($schedule->shift == 'Morning')
                                            Morning (7:00 AM - 3:00 PM)
                                        @elseif($schedule->shift == 'Evening')
                                            Evening (3:00 PM - 11:00 PM)
                                        @elseif($schedule->shift == 'Night')
                                            Night (11:00 PM - 7:00 AM)
                                        @else
                                            {{ $schedule->shift }}
                                        @endif
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-sm">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                <td class="py-2 px-4 text-sm">
                                    <a href="{{ route('shift-schedule.nurse', $schedule->nurse->id) }}" class="text-blue-600 hover:underline">
                                        {{ $schedule->nurse->name }}
                                    </a>
                                </td>
                                <td class="py-2 px-4 text-sm">
                                    <a href="{{ route('shift-schedule.ward', $schedule->ward->id) }}" class="text-blue-600 hover:underline">
                                        {{ $schedule->ward->name }}
                                    </a>
                                </td>
                                <td class="py-2 px-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($schedule->status == 'scheduled') bg-yellow-100 text-yellow-800
                                        @elseif($schedule->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($schedule->status == 'completed') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('shift-schedule.edit', $schedule->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('shift-schedule.destroy', $schedule->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shift?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                    No shift schedules found for the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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