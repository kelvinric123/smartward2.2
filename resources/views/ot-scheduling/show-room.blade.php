<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OT Room Details') }}: {{ $room->room_number }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('ot-scheduling.edit-room', $room) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Room
                </a>
                <a href="{{ route('ot-scheduling.rooms') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Rooms
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Details -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Information</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Room Number</p>
                                    <p class="text-md font-bold">{{ $room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="text-md font-bold">{{ $room->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="inline-block px-2 py-1 text-xs rounded-full {{ $room->getStatusClass() }}">
                                        {{ ucfirst($room->status) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Type</p>
                                    <p class="text-md font-bold capitalize">{{ $room->type }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Floor</p>
                                    <p class="text-md">{{ $room->floor ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Building</p>
                                    <p class="text-md">{{ $room->building ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Capacity</p>
                                    <p class="text-md">{{ $room->capacity }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Active</p>
                                    <p class="text-md">{{ $room->is_active ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Equipment</p>
                                <p class="text-md whitespace-pre-line">{{ $room->equipment ?? 'None listed' }}</p>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Notes</p>
                                <p class="text-md whitespace-pre-line">{{ $room->notes ?? 'No notes' }}</p>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Status Management</h3>
                            <div class="space-y-3">
                                <form action="{{ route('ot-scheduling.update-room', $room) }}" method="POST" class="inline-block w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="room_number" value="{{ $room->room_number }}">
                                    <input type="hidden" name="name" value="{{ $room->name }}">
                                    <input type="hidden" name="type" value="{{ $room->type }}">
                                    <input type="hidden" name="floor" value="{{ $room->floor }}">
                                    <input type="hidden" name="building" value="{{ $room->building }}">
                                    <input type="hidden" name="capacity" value="{{ $room->capacity }}">
                                    <input type="hidden" name="equipment" value="{{ $room->equipment }}">
                                    <input type="hidden" name="notes" value="{{ $room->notes }}">
                                    <input type="hidden" name="is_active" value="{{ $room->is_active }}">
                                    
                                    <div class="grid grid-cols-1 gap-2">
                                        <button type="submit" name="status" value="available" 
                                                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center disabled:opacity-50" 
                                                {{ $room->status == 'available' ? 'disabled' : '' }}>
                                            Mark as Available
                                        </button>
                                        
                                        <button type="submit" name="status" value="occupied" 
                                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded text-center disabled:opacity-50" 
                                                {{ $room->status == 'occupied' ? 'disabled' : '' }}>
                                            Mark as Occupied
                                        </button>
                                        
                                        <button type="submit" name="status" value="cleaning" 
                                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded text-center disabled:opacity-50" 
                                                {{ $room->status == 'cleaning' ? 'disabled' : '' }}>
                                            Send for Cleaning
                                        </button>
                                        
                                        <button type="submit" name="status" value="maintenance" 
                                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded text-center disabled:opacity-50" 
                                                {{ $room->status == 'maintenance' ? 'disabled' : '' }}>
                                            Send for Maintenance
                                        </button>
                                        
                                        <button type="submit" name="status" value="reserved" 
                                                class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded text-center disabled:opacity-50" 
                                                {{ $room->status == 'reserved' ? 'disabled' : '' }}>
                                            Mark as Reserved
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scheduled Operations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Scheduled Operations</h3>
                    
                    @if($schedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Date</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Time</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Patient</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Procedure</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Surgeon</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Status</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($schedules as $schedule)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 text-sm">{{ $schedule->schedule_date->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-sm">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </td>
                                            <td class="py-3 px-4 text-sm">{{ $schedule->patient->name }}</td>
                                            <td class="py-3 px-4 text-sm">{{ $schedule->procedure_type }}</td>
                                            <td class="py-3 px-4 text-sm">{{ $schedule->surgeon->name }}</td>
                                            <td class="py-3 px-4 text-sm">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($schedule->status == 'scheduled') bg-blue-100 text-blue-800
                                                    @elseif($schedule->status == 'in-progress') bg-yellow-100 text-yellow-800
                                                    @elseif($schedule->status == 'completed') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                <a href="{{ route('ot-scheduling.show-booking', $schedule) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No operations scheduled for this room.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 