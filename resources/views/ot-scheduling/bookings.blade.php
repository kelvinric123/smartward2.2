<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OT Bookings & Scheduling') }}
            </h2>
            <a href="{{ route('ot-scheduling.create-booking') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                New Booking
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtering Options -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium mb-2">Filter Bookings</h3>
                        <form action="{{ route('ot-scheduling.bookings') }}" method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <label for="date_filter" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="date_filter" name="date" value="{{ request('date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status_filter" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Statuses</option>
                                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Bookings Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Date</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Time</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Patient</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Procedure</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Surgeon</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Anesthetist</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Status</th>
                                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($bookings as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm">{{ $booking->schedule_date->format('d/m/Y') }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                        </td>
                                        <td class="py-3 px-4 text-sm">{{ $booking->patient->full_name }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $booking->procedure_type }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $booking->surgeon->name }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $booking->anesthetist->name }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($booking->status == 'scheduled') bg-blue-100 text-blue-800
                                                @elseif($booking->status == 'in-progress') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'completed') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm space-x-2">
                                            <a href="{{ route('ot-scheduling.show-booking', $booking) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('ot-scheduling.edit-booking', $booking) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            <form class="inline-block" action="{{ route('ot-scheduling.destroy-booking', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-6 px-4 text-center text-gray-500 italic">No bookings found.</td>
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