@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Nurse Shift Dashboard</h1>
        <p class="text-gray-600">Manage nurse shift schedules and assignments</p>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('shift-schedule.create') }}" class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <h3 class="text-xl font-semibold">Add New Shift</h3>
            </div>
            <p>Create a new shift schedule for nurses</p>
        </a>

        <a href="{{ route('shift-schedule.index') }}" class="block p-6 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 transition">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <h3 class="text-xl font-semibold">View Schedule</h3>
            </div>
            <p>View and manage all shift schedules</p>
        </a>

        <a href="#ward-section" class="block p-6 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="text-xl font-semibold">Ward Schedules</h3>
            </div>
            <p>View shift schedules by ward</p>
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Shifts</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $totalShifts }}</p>
            <p class="text-sm text-gray-500">Upcoming 7 days</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Unassigned Slots</h3>
            <p class="text-3xl font-bold text-red-600">{{ $unassignedSlots }}</p>
            <p class="text-sm text-gray-500">Requiring attention</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Today's Shifts</h3>
            <p class="text-3xl font-bold text-green-600">{{ $todayShifts }}</p>
            <p class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Coverage</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($coveragePercentage, 1) }}%</p>
            <p class="text-sm text-gray-500">Overall ward coverage</p>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Today's Schedule</h2>
            <a href="{{ route('shift-schedule.index', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" class="text-blue-600 hover:underline">View All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Nurse</th>
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Ward</th>
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($todaySchedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($schedule->shift == 'Morning') bg-blue-100 text-blue-800
                                    @elseif($schedule->shift == 'Evening') bg-indigo-100 text-indigo-800
                                    @elseif($schedule->shift == 'Night') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $schedule->shift }}
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
                                    <a href="{{ route('shift-schedule.show', $schedule->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                                No shifts scheduled for today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ward Section -->
    <div id="ward-section" class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Ward Coverage</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ward List -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Wards</h3>
                
                <div class="space-y-4">
                    @foreach($wards as $ward)
                        <div class="border rounded-md p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $ward->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $ward->code }} - Floor {{ $ward->floor }}</p>
                                </div>
                                <a href="{{ route('shift-schedule.ward', $ward->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">
                                    View Schedule
                                </a>
                            </div>
                            
                            @php
                                $wardCoverage = $wardCoverageData[$ward->id] ?? 0;
                                $coverageClass = $wardCoverage < 50 ? 'bg-red-500' : ($wardCoverage < 80 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            
                            <div class="mt-2">
                                <div class="text-xs text-gray-600 flex justify-between mb-1">
                                    <span>Coverage</span>
                                    <span>{{ number_format($wardCoverage, 1) }}%</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $coverageClass }}" style="width: {{ $wardCoverage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Nurse List -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Nurses</h3>
                
                <div class="space-y-4">
                    @foreach($nurses as $nurse)
                        <div class="border rounded-md p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $nurse->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $nurse->position }}</p>
                                </div>
                                <a href="{{ route('shift-schedule.nurse', $nurse->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 text-sm rounded">
                                    View Schedule
                                </a>
                            </div>
                            
                            <div class="mt-2 flex items-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $nurse->status == 'On Duty' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} mr-2">
                                    {{ $nurse->status }}
                                </span>
                                
                                @if($nurse->ward_assignment)
                                    <span class="text-xs text-gray-600">
                                        Assigned to: <a href="{{ route('shift-schedule.ward', $wardsByName[$nurse->ward_assignment] ?? '#') }}" class="text-blue-600 hover:underline">{{ $nurse->ward_assignment }}</a>
                                    </span>
                                @else
                                    <span class="text-xs text-gray-600">No ward assignment</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 