<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OT Room Management') }}
            </h2>
            <a href="{{ route('ot-scheduling.create-room') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Room
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Success and Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    <!-- Filtering Options -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium mb-2">Filter Rooms</h3>
                        <form action="{{ route('ot-scheduling.rooms') }}" method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status_filter" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Statuses</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="cleaning" {{ request('status') == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                </select>
                            </div>
                            <div>
                                <label for="type_filter" class="block text-sm font-medium text-gray-700">Room Type</label>
                                <select id="type_filter" name="type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Types</option>
                                    <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="cardiac" {{ request('type') == 'cardiac' ? 'selected' : '' }}>Cardiac</option>
                                    <option value="orthopedic" {{ request('type') == 'orthopedic' ? 'selected' : '' }}>Orthopedic</option>
                                    <option value="neurosurgery" {{ request('type') == 'neurosurgery' ? 'selected' : '' }}>Neurosurgery</option>
                                    <option value="ophthalmic" {{ request('type') == 'ophthalmic' ? 'selected' : '' }}>Ophthalmic</option>
                                    <option value="ent" {{ request('type') == 'ent' ? 'selected' : '' }}>ENT</option>
                                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Rooms Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Room Number</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Name</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Type</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Status</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Floor</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Building</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($rooms as $room)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm font-medium">{{ $room->room_number }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $room->name }}</td>
                                        <td class="py-3 px-4 text-sm capitalize">{{ $room->type }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $room->getStatusClass() }}">
                                                {{ ucfirst($room->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm">{{ $room->floor ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $room->building ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm space-x-2">
                                            <a href="{{ route('ot-scheduling.show-room', $room) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('ot-scheduling.edit-room', $room) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            <form class="inline-block" action="{{ route('ot-scheduling.destroy-room', $room) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 px-4 text-center text-gray-500 italic">No rooms found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 