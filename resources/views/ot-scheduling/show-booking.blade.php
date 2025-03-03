<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OT Booking Details') }} #{{ $schedule->id }}
            </h2>
            <div class="space-x-2">
                <form action="{{ route('ot-scheduling.update-booking-status', $schedule) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    
                    @if($schedule->status == 'scheduled')
                        <button type="submit" name="status" value="in-progress" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Start Operation
                        </button>
                    @elseif($schedule->status == 'in-progress')
                        <button type="submit" name="status" value="completed" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Complete Operation
                        </button>
                    @endif
                    
                    @if($schedule->status != 'cancelled' && $schedule->status != 'completed')
                        <button type="submit" name="status" value="cancelled" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                onclick="return confirm('Are you sure you want to cancel this operation?')">
                            Cancel Operation
                        </button>
                    @endif
                </form>
                
                <a href="{{ route('ot-scheduling.bookings') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Bookings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Primary Booking Information -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Booking Information</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($schedule->status == 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($schedule->status == 'in-progress') bg-yellow-100 text-yellow-800
                                            @elseif($schedule->status == 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-medium">{{ $schedule->schedule_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Start Time</p>
                                    <p class="font-medium">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">End Time</p>
                                    <p class="font-medium">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Duration</p>
                                    <p class="font-medium">
                                        @php
                                            $start = \Carbon\Carbon::parse($schedule->start_time);
                                            $end = \Carbon\Carbon::parse($schedule->end_time);
                                            $duration = $end->diffInMinutes($start);
                                            $hours = floor($duration / 60);
                                            $minutes = $duration % 60;
                                        @endphp
                                        {{ $hours }} hr {{ $minutes }} min
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Procedure</p>
                                    <p class="font-medium">{{ $schedule->procedure_type }}</p>
                                </div>
                            </div>
                            
                            @if($schedule->room)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Assigned Room</p>
                                <div class="flex items-center">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full {{ $schedule->room->getStatusClass() }} mr-2">
                                        {{ ucfirst($schedule->room->status) }}
                                    </span>
                                    <a href="{{ route('ot-scheduling.show-room', $schedule->room) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $schedule->room->room_number }} - {{ $schedule->room->name }} ({{ ucfirst($schedule->room->type) }})
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Notes</p>
                                <p class="whitespace-pre-line bg-white p-3 rounded border">{{ $schedule->notes ?? 'No notes provided' }}</p>
                            </div>
                        </div>
                        
                        <!-- Personnel Information -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Personnel & Patient</h3>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Patient</p>
                                <div class="bg-white p-3 rounded border">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $schedule->patient->full_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $schedule->patient->gender }}, {{ $schedule->patient->calculateAge() }} years</p>
                                            <p class="text-sm text-gray-600">Reg: {{ $schedule->patient->mrn }}</p>
                                        </div>
                                        <a href="{{ route('patients.show', $schedule->patient) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Profile</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Surgeon</p>
                                <div class="bg-white p-3 rounded border">
                                    <p class="font-medium">{{ $schedule->surgeon->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->surgeon->specialization }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->surgeon->contact_number }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Anesthetist</p>
                                <div class="bg-white p-3 rounded border">
                                    <p class="font-medium">{{ $schedule->anesthetist->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->anesthetist->specialization }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->anesthetist->contact_number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Operation Details - Only shown when in progress or completed -->
                    @if(in_array($schedule->status, ['in-progress', 'completed']))
                    <div class="mt-8 bg-white p-6 border rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Operation Details</h3>
                        
                        <form action="{{ route('ot-scheduling.update-operation-details', $schedule) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="procedure_details" :value="__('Procedure Details')" />
                                    <x-textarea id="procedure_details" name="procedure_details" rows="4" class="w-full mt-1">{{ $schedule->procedure_details }}</x-textarea>
                                    <x-input-error :messages="$errors->get('procedure_details')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="anesthesia_details" :value="__('Anesthesia Details')" />
                                    <x-textarea id="anesthesia_details" name="anesthesia_details" rows="4" class="w-full mt-1">{{ $schedule->anesthesia_details }}</x-textarea>
                                    <x-input-error :messages="$errors->get('anesthesia_details')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="complications" :value="__('Complications (if any)')" />
                                    <x-textarea id="complications" name="complications" rows="4" class="w-full mt-1">{{ $schedule->complications }}</x-textarea>
                                    <x-input-error :messages="$errors->get('complications')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="outcome" :value="__('Outcome')" />
                                    <x-textarea id="outcome" name="outcome" rows="4" class="w-full mt-1">{{ $schedule->outcome }}</x-textarea>
                                    <x-input-error :messages="$errors->get('outcome')" class="mt-2" />
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <x-primary-button>{{ __('Update Operation Details') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 