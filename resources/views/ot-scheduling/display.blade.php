<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Operation Theater Live Display') }}
            </h2>
            <div class="flex space-x-2">
                <span id="current-time" class="text-lg font-bold bg-blue-100 text-blue-800 px-3 py-1 rounded"></span>
                <span id="current-date" class="text-lg font-bold bg-blue-100 text-blue-800 px-3 py-1 rounded"></span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Active Operations -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 bg-red-600 text-white py-2 px-4 rounded-t-lg">
                    <i class="fas fa-broadcast-tower mr-2"></i> LIVE: Operations In Progress
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($activeBookings as $booking)
                        <a href="{{ route('ot-scheduling.display-room', $booking->room) }}" class="block">
                            <div class="bg-white border-2 border-red-500 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <div class="bg-red-500 text-white px-4 py-2 font-bold flex justify-between items-center">
                                    <span>Room {{ $booking->room->room_number }}</span>
                                    <span class="animate-pulse flex items-center">
                                        <span class="h-3 w-3 rounded-full bg-white mr-2"></span>
                                        LIVE
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h4 class="font-bold text-lg mb-2">{{ $booking->procedure_type }}</h4>
                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        <div>
                                            <p class="text-sm text-gray-600">Patient</p>
                                            <p class="font-semibold">{{ $booking->patient->full_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Duration</p>
                                            <p class="font-semibold">
                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-sm text-gray-600">Surgeon</p>
                                            <p class="font-semibold">{{ $booking->surgeon->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Anesthetist</p>
                                            <p class="font-semibold">{{ $booking->anesthetist->name }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress bar placeholder - would be dynamic in a real system -->
                                    <div class="mt-4">
                                        <div class="relative pt-1">
                                            <div class="flex mb-2 items-center justify-between">
                                                <div>
                                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                                        In Progress
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs font-semibold inline-block text-green-600">
                                                        Surgery In Progress
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                                                <div style="width: 60%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500 transition-all duration-500"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-center">
                                        <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                            Click to view live feed
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-3 bg-white p-6 rounded-lg shadow-md text-center">
                            <p class="text-gray-500 italic">No operations currently in progress.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Upcoming Operations -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 bg-blue-600 text-white py-2 px-4 rounded-t-lg">
                    <i class="fas fa-calendar-day mr-2"></i> Upcoming Operations Today
                </h3>
                
                <div class="overflow-x-auto bg-white shadow-md rounded-b-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-3 px-4 text-left">Time</th>
                                <th class="py-3 px-4 text-left">Room</th>
                                <th class="py-3 px-4 text-left">Patient</th>
                                <th class="py-3 px-4 text-left">Procedure</th>
                                <th class="py-3 px-4 text-left">Surgeon</th>
                                <th class="py-3 px-4 text-left">Anesthetist</th>
                                <th class="py-3 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingBookings as $booking)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </td>
                                    <td class="py-3 px-4">{{ $booking->room->room_number }}</td>
                                    <td class="py-3 px-4">{{ $booking->patient->full_name }}</td>
                                    <td class="py-3 px-4">{{ $booking->procedure_type }}</td>
                                    <td class="py-3 px-4">{{ $booking->surgeon->name }}</td>
                                    <td class="py-3 px-4">{{ $booking->anesthetist->name }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            Scheduled
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500 italic">
                                        No upcoming operations scheduled for today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- OT Rooms Overview -->
            <div>
                <h3 class="text-lg font-semibold mb-4 bg-gray-700 text-white py-2 px-4 rounded-t-lg">
                    <i class="fas fa-door-open mr-2"></i> Operation Theater Rooms Status
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($rooms as $room)
                        <a href="{{ route('ot-scheduling.display-room', $room) }}" class="block">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-bold">Room {{ $room->room_number }}</h4>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $room->getStatusClass() }}">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $room->name }}</p>
                                    <p class="text-xs text-gray-500">Type: <span class="font-medium">{{ ucfirst($room->type) }}</span></p>
                                    
                                    @php
                                        $hasActiveBooking = $activeBookings->where('room_id', $room->id)->first();
                                    @endphp
                                    
                                    @if($hasActiveBooking)
                                        <div class="mt-2 text-center">
                                            <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full animate-pulse">
                                                <i class="fas fa-video mr-1"></i> Live Operation
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update current time and date
        function updateDateTime() {
            const now = new Date();
            
            // Format time: HH:MM:SS
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: false 
            });
            
            // Format date: Day, DD Month YYYY
            const dateStr = now.toLocaleDateString('en-US', { 
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            document.getElementById('current-time').textContent = timeStr;
            document.getElementById('current-date').textContent = dateStr;
        }
        
        // Initial call
        updateDateTime();
        
        // Update every second
        setInterval(updateDateTime, 1000);
        
        // Auto refresh the page every 30 seconds to get updated data
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
    @endpush
</x-app-layout> 