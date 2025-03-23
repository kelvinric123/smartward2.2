@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Shift Schedule Details</h2>
            
            <div class="flex space-x-2">
                <a href="{{ route('shift-schedule.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Back to List
                </a>
                <a href="{{ route('shift-schedule.edit', $shiftSchedule->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Edit Schedule
                </a>
            </div>
        </div>
        
        <!-- Shift Schedule Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Schedule Information</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2">
                        <div class="text-sm font-medium text-gray-500">Schedule Date</div>
                        <div class="text-sm font-semibold">{{ $shiftSchedule->schedule_date->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2">
                        <div class="text-sm font-medium text-gray-500">Shift Type</div>
                        <div class="text-sm font-semibold">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($shiftSchedule->shift == 'Morning') bg-blue-100 text-blue-800
                                @elseif($shiftSchedule->shift == 'Evening') bg-indigo-100 text-indigo-800
                                @elseif($shiftSchedule->shift == 'Night') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $shiftSchedule->getShiftLabel() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2">
                        <div class="text-sm font-medium text-gray-500">Time</div>
                        <div class="text-sm font-semibold">{{ $shiftSchedule->start_time->format('H:i') }} - {{ $shiftSchedule->end_time->format('H:i') }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="text-sm font-semibold">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($shiftSchedule->status == 'scheduled') bg-yellow-100 text-yellow-800
                                @elseif($shiftSchedule->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($shiftSchedule->status == 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($shiftSchedule->status) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($shiftSchedule->notes)
                    <div class="col-span-2 mt-2">
                        <div class="text-sm font-medium text-gray-500 mb-1">Notes</div>
                        <div class="text-sm p-2 bg-white rounded border border-gray-200">{{ $shiftSchedule->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Assignment Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Nurse</span>
                        <div class="flex items-center mt-1">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-800 font-bold mr-2">
                                {{ strtoupper(substr($shiftSchedule->nurse->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold">
                                    <a href="{{ route('shift-schedule.nurse', $shiftSchedule->nurse->id) }}" class="text-blue-600 hover:underline">
                                        {{ $shiftSchedule->nurse->name }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500">{{ $shiftSchedule->nurse->position }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Ward</span>
                        <div class="flex items-center mt-1">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center text-green-800 font-bold mr-2">
                                {{ strtoupper(substr($shiftSchedule->ward->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold">
                                    <a href="{{ route('shift-schedule.ward', $shiftSchedule->ward->id) }}" class="text-blue-600 hover:underline">
                                        {{ $shiftSchedule->ward->name }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500">Code: {{ $shiftSchedule->ward->code }} | Floor: {{ $shiftSchedule->ward->floor }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="border-t pt-4 flex justify-between">
            <form action="{{ route('shift-schedule.destroy', $shiftSchedule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift schedule?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                    Delete Schedule
                </button>
            </form>
            
            <div class="flex space-x-2">
                <a href="{{ route('shift-schedule.edit', $shiftSchedule->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Edit Schedule
                </a>
                <a href="{{ route('shift-schedule.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 