@extends('layouts.app')

@section('title', 'Nurses')

@section('header')
    {{ __('Nurses') }}
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Nursing Staff Directory</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium">List of Nursing Staff</h4>
                            <a href="{{ route('nurses.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                                Add New Nurse
                            </a>
                        </div>
                        
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Name</th>
                                    <th class="py-2 px-4 border-b text-left">Position</th>
                                    <th class="py-2 px-4 border-b text-left">Ward Assignment</th>
                                    <th class="py-2 px-4 border-b text-left">Shift</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nurses as $nurse)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $nurse->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $nurse->position }}</td>
                                        <td class="py-2 px-4 border-b">{{ $nurse->ward_assignment }}</td>
                                        <td class="py-2 px-4 border-b">{{ $nurse->shift }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($nurse->status == 'On Duty')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $nurse->status }}
                                                </span>
                                            @elseif ($nurse->status == 'Off Duty')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $nurse->status }}
                                                </span>
                                            @elseif ($nurse->status == 'Break')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $nurse->status }}
                                                </span>
                                            @elseif ($nurse->status == 'On Leave')
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $nurse->status }}
                                                </span>
                                            @else
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $nurse->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('nurses.show', $nurse->id) }}" class="text-blue-500 hover:underline mr-2">View</a>
                                            <a href="{{ route('nurses.edit', $nurse->id) }}" class="text-indigo-500 hover:underline mr-2">Edit</a>
                                            <form action="{{ route('nurses.deactivate', $nurse->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to deactivate this nurse?')">Deactivate</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">
                                            No nurses found. Add your first nurse to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-8">
                        <h4 class="text-md font-medium mb-4">Shift Schedule</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-medium mb-2">Morning Shift (7:00 AM - 3:00 PM)</h5>
                                @if($morningShiftNurses->count() > 0)
                                    <ul class="list-disc list-inside text-sm text-gray-700">
                                        @foreach($morningShiftNurses as $nurse)
                                            <li>{{ $nurse->name }} ({{ $nurse->ward_assignment }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">No nurses assigned to this shift.</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-medium mb-2">Evening Shift (3:00 PM - 11:00 PM)</h5>
                                @if($eveningShiftNurses->count() > 0)
                                    <ul class="list-disc list-inside text-sm text-gray-700">
                                        @foreach($eveningShiftNurses as $nurse)
                                            <li>{{ $nurse->name }} ({{ $nurse->ward_assignment }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">No nurses assigned to this shift.</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-medium mb-2">Night Shift (11:00 PM - 7:00 AM)</h5>
                                @if($nightShiftNurses->count() > 0)
                                    <ul class="list-disc list-inside text-sm text-gray-700">
                                        @foreach($nightShiftNurses as $nurse)
                                            <li>{{ $nurse->name }} ({{ $nurse->ward_assignment }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">No nurses assigned to this shift.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 