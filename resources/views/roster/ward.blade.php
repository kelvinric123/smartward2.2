@extends('layouts.app')

@section('title', 'Ward Roster - ' . $ward->name)

@section('header')
{{ __('Ward Roster - ' . $ward->name) }}
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-medium mb-2">{{ $ward->name }} Ward Roster</h3>
                        <p class="text-sm text-gray-600">{{ $ward->description }}</p>
                    </div>
                    <div class="space-x-2 mt-3 md:mt-0">
                        <a href="{{ route('roster.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm inline-block">
                            Back to All Rosters
                        </a>
                    </div>
                </div>

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
                            <span class="block text-sm font-medium text-gray-700">Assigned Nurses</span>
                            <span class="block font-semibold">{{ $nurses->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Weekly Roster Calendar -->
                <div class="mb-6">
                    <h4 class="text-md font-medium mb-3">Weekly Roster Schedule</h4>
                    
                    @if($nurses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nurse</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Monday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tuesday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Wednesday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thursday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Friday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Saturday</th>
                                        <th class="py-2 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sunday</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($nurses as $nurse)
                                        <tr>
                                            <td class="py-2 px-4 border-b">
                                                <div class="font-medium text-gray-900">{{ $nurse->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $nurse->position }}</div>
                                            </td>
                                            
                                            @php
                                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            @endphp
                                            
                                            @foreach($days as $day)
                                                <td class="py-2 px-3 border-b text-center">
                                                    @if(isset($nurse->roster[$day]) && isset($nurse->roster[$day]['assigned']) && $nurse->roster[$day]['assigned'])
                                                        <div class="text-xs p-1 rounded 
                                                            @if(isset($nurse->roster[$day]['shift']) && $nurse->roster[$day]['shift'] == 'Morning')
                                                                bg-blue-100 text-blue-800
                                                            @elseif(isset($nurse->roster[$day]['shift']) && $nurse->roster[$day]['shift'] == 'Evening') 
                                                                bg-indigo-100 text-indigo-800
                                                            @elseif(isset($nurse->roster[$day]['shift']) && $nurse->roster[$day]['shift'] == 'Night')
                                                                bg-purple-100 text-purple-800
                                                            @else
                                                                bg-gray-100 text-gray-800
                                                            @endif
                                                        ">
                                                            <span class="font-medium">{{ $nurse->roster[$day]['shift'] ?? 'Custom' }}</span>
                                                            @if(isset($nurse->roster[$day]['start_time']) && isset($nurse->roster[$day]['end_time']))
                                                                <br>
                                                                <span>{{ $nurse->roster[$day]['start_time'] }} - {{ $nurse->roster[$day]['end_time'] }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Off</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <p class="text-yellow-700">No nurses are currently assigned to this ward. <a href="{{ route('nurses.index') }}" class="text-blue-600 hover:underline">Manage nurses</a> to assign them to this ward.</p>
                        </div>
                    @endif
                </div>

                <!-- Daily Shift Coverage -->
                <div class="mb-6">
                    <h4 class="text-md font-medium mb-3">Daily Shift Coverage</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-md">
                            <h5 class="font-medium text-blue-800 mb-2">Morning Shift</h5>
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $morningCoverage = [];
                                
                                foreach($days as $day) {
                                    $morningCoverage[$day] = 0;
                                    
                                    foreach($nurses as $nurse) {
                                        if(isset($nurse->roster[$day]) && 
                                          isset($nurse->roster[$day]['assigned']) && 
                                          $nurse->roster[$day]['assigned'] && 
                                          isset($nurse->roster[$day]['shift']) && 
                                          $nurse->roster[$day]['shift'] == 'Morning') {
                                            $morningCoverage[$day]++;
                                        }
                                    }
                                }
                            @endphp
                            
                            <ul class="space-y-1">
                                @foreach($days as $day)
                                    <li class="flex justify-between items-center text-sm">
                                        <span>{{ $day }}</span>
                                        <span class="font-medium {{ $morningCoverage[$day] == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $morningCoverage[$day] }} nurses
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="bg-indigo-50 p-4 rounded-md">
                            <h5 class="font-medium text-indigo-800 mb-2">Evening Shift</h5>
                            @php
                                $eveningCoverage = [];
                                
                                foreach($days as $day) {
                                    $eveningCoverage[$day] = 0;
                                    
                                    foreach($nurses as $nurse) {
                                        if(isset($nurse->roster[$day]) && 
                                          isset($nurse->roster[$day]['assigned']) && 
                                          $nurse->roster[$day]['assigned'] && 
                                          isset($nurse->roster[$day]['shift']) && 
                                          $nurse->roster[$day]['shift'] == 'Evening') {
                                            $eveningCoverage[$day]++;
                                        }
                                    }
                                }
                            @endphp
                            
                            <ul class="space-y-1">
                                @foreach($days as $day)
                                    <li class="flex justify-between items-center text-sm">
                                        <span>{{ $day }}</span>
                                        <span class="font-medium {{ $eveningCoverage[$day] == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $eveningCoverage[$day] }} nurses
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-md">
                            <h5 class="font-medium text-purple-800 mb-2">Night Shift</h5>
                            @php
                                $nightCoverage = [];
                                
                                foreach($days as $day) {
                                    $nightCoverage[$day] = 0;
                                    
                                    foreach($nurses as $nurse) {
                                        if(isset($nurse->roster[$day]) && 
                                          isset($nurse->roster[$day]['assigned']) && 
                                          $nurse->roster[$day]['assigned'] && 
                                          isset($nurse->roster[$day]['shift']) && 
                                          $nurse->roster[$day]['shift'] == 'Night') {
                                            $nightCoverage[$day]++;
                                        }
                                    }
                                }
                            @endphp
                            
                            <ul class="space-y-1">
                                @foreach($days as $day)
                                    <li class="flex justify-between items-center text-sm">
                                        <span>{{ $day }}</span>
                                        <span class="font-medium {{ $nightCoverage[$day] == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $nightCoverage[$day] }} nurses
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Individual Nurse Roster Management -->
                <div>
                    <h4 class="text-md font-medium mb-3">Manage Individual Rosters</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($nurses as $nurse)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $nurse->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $nurse->position }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($nurse->status == 'On Duty') bg-green-100 text-green-800 
                                                @elseif($nurse->status == 'Off Duty') bg-gray-100 text-gray-800 
                                                @elseif($nurse->status == 'Break') bg-yellow-100 text-yellow-800 
                                                @elseif($nurse->status == 'On Leave') bg-red-100 text-red-800 
                                                @else bg-blue-100 text-blue-800 
                                                @endif">
                                                {{ $nurse->status }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $nurse->last_roster_update ? $nurse->last_roster_update->diffForHumans() : 'Never' }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('roster.edit', $nurse->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit Roster</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 