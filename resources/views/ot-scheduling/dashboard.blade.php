<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Smart OT System Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Quick Stats Card -->
                        <div class="bg-blue-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">OT Statistics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                @php
                                    // Safely handle queries to prevent errors
                                    try {
                                        $todayOps = \App\Models\OTSchedule::whereDate('schedule_date', today())->count();
                                        $pendingOps = \App\Models\OTSchedule::where('status', 'scheduled')->count();
                                        $surgeons = \App\Models\Surgeon::where('status', 'active')->count();
                                        $anesthetists = \App\Models\Anesthetist::where('status', 'active')->count();
                                    } catch (\Exception $e) {
                                        $todayOps = 0;
                                        $pendingOps = 0;
                                        $surgeons = 0;
                                        $anesthetists = 0;
                                    }
                                @endphp
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Today's Operations</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $todayOps }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Pending Operations</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $pendingOps }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Available Surgeons</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $surgeons }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Available Anesthetists</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $anesthetists }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Links Card -->
                        <div class="bg-green-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('ot-scheduling.create-booking') }}" class="block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center">
                                    New OT Booking
                                </a>
                                <a href="{{ route('ot-scheduling.bookings') }}" class="block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center">
                                    View All Bookings
                                </a>
                                <a href="{{ route('ot-scheduling.staff-availability') }}" class="block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center">
                                    Check Staff Availability
                                </a>
                                <a href="{{ route('ot-scheduling.display') }}" class="block bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded text-center">
                                    <i class="fas fa-tv mr-1"></i> Live OT Display
                                </a>
                            </div>
                        </div>
                        
                        <!-- Today's Schedule Card -->
                        <div class="bg-purple-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-purple-800 mb-4">Today's Schedule</h3>
                            <div class="overflow-y-auto max-h-64">
                                @php
                                    try {
                                        $todaySchedules = \App\Models\OTSchedule::with(['patient', 'surgeon', 'anesthetist'])
                                            ->whereDate('schedule_date', today())
                                            ->orderBy('start_time')
                                            ->get();
                                    } catch (\Exception $e) {
                                        $todaySchedules = collect();
                                    }
                                @endphp
                                
                                @if($todaySchedules->count() > 0)
                                    <ul class="space-y-3">
                                        @foreach($todaySchedules as $schedule)
                                            <li class="border-l-4 border-purple-500 pl-3 py-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        @if($schedule->status == 'scheduled') bg-blue-100 text-blue-800
                                                        @elseif($schedule->status == 'in-progress') bg-yellow-100 text-yellow-800
                                                        @elseif($schedule->status == 'completed') bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($schedule->status) }}
                                                    </span>
                                                </div>
                                                <div>Patient: {{ $schedule->patient->name }}</div>
                                                <div class="text-xs text-gray-600">
                                                    <span>Surgeon: {{ $schedule->surgeon->name }}</span> | 
                                                    <span>Procedure: {{ $schedule->procedure_type }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 italic">No operations scheduled for today.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 