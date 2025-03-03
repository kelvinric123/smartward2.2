<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit OT Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('ot-scheduling.update-booking', $booking) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Patient Selection -->
                        <div>
                            <x-input-label for="patient_id" :value="__('Patient')" />
                            <select id="patient_id" name="patient_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $booking->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }} ({{ $patient->mrn ?? $patient->id }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="schedule_date" :value="__('Date')" />
                                <x-text-input id="schedule_date" type="date" name="schedule_date" :value="old('schedule_date', $booking->schedule_date->format('Y-m-d'))" required class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('schedule_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="start_time" :value="__('Start Time')" />
                                <x-text-input id="start_time" type="time" name="start_time" :value="old('start_time', $booking->start_time)" required step="900" class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-600">Time slots are in 15-minute intervals</p>
                            </div>
                            <div>
                                <x-input-label for="end_time" :value="__('End Time')" />
                                <x-text-input id="end_time" type="time" name="end_time" :value="old('end_time', $booking->end_time)" required step="900" class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Surgeon Selection -->
                        <div>
                            <x-input-label for="surgeon_id" :value="__('Surgeon')" />
                            <select id="surgeon_id" name="surgeon_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Surgeon</option>
                                @foreach($surgeons as $surgeon)
                                    <option value="{{ $surgeon->id }}" {{ old('surgeon_id', $booking->surgeon_id) == $surgeon->id ? 'selected' : '' }}>
                                        {{ $surgeon->name }} ({{ $surgeon->specialization }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('surgeon_id')" class="mt-2" />
                        </div>

                        <!-- Anesthetist Selection -->
                        <div>
                            <x-input-label for="anesthetist_id" :value="__('Anesthetist')" />
                            <select id="anesthetist_id" name="anesthetist_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Anesthetist</option>
                                @foreach($anesthetists as $anesthetist)
                                    <option value="{{ $anesthetist->id }}" {{ old('anesthetist_id', $booking->anesthetist_id) == $anesthetist->id ? 'selected' : '' }}>
                                        {{ $anesthetist->name }} ({{ $anesthetist->specialization }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('anesthetist_id')" class="mt-2" />
                        </div>
                        
                        <!-- OT Room Selection -->
                        <div>
                            <x-input-label for="room_id" :value="__('Operation Theater Room')" />
                            <select id="room_id" name="room_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select OT Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }} - {{ $room->name }} ({{ ucfirst($room->type) }}) 
                                        @if($room->status !== 'available' && $booking->room_id != $room->id)
                                        - <span class="text-red-500">{{ ucfirst($room->status) }}</span>
                                        @else
                                        - <span class="text-green-500">{{ $booking->room_id == $room->id ? 'Currently Assigned' : 'Available' }}</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-600">Changing rooms will release the currently assigned room.</p>
                        </div>

                        <!-- Procedure Type -->
                        <div>
                            <x-input-label for="procedure_type" :value="__('Procedure Type')" />
                            <select id="procedure_type" name="procedure_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Procedure Type</option>
                                <option value="Appendectomy" {{ old('procedure_type', $booking->procedure_type) == 'Appendectomy' ? 'selected' : '' }}>Appendectomy</option>
                                <option value="Coronary Bypass" {{ old('procedure_type', $booking->procedure_type) == 'Coronary Bypass' ? 'selected' : '' }}>Coronary Bypass</option>
                                <option value="Knee Replacement" {{ old('procedure_type', $booking->procedure_type) == 'Knee Replacement' ? 'selected' : '' }}>Knee Replacement</option>
                                <option value="Hip Replacement" {{ old('procedure_type', $booking->procedure_type) == 'Hip Replacement' ? 'selected' : '' }}>Hip Replacement</option>
                                <option value="Cataract Surgery" {{ old('procedure_type', $booking->procedure_type) == 'Cataract Surgery' ? 'selected' : '' }}>Cataract Surgery</option>
                                <option value="Hernia Repair" {{ old('procedure_type', $booking->procedure_type) == 'Hernia Repair' ? 'selected' : '' }}>Hernia Repair</option>
                                <option value="Tonsillectomy" {{ old('procedure_type', $booking->procedure_type) == 'Tonsillectomy' ? 'selected' : '' }}>Tonsillectomy</option>
                                <option value="Cholecystectomy" {{ old('procedure_type', $booking->procedure_type) == 'Cholecystectomy' ? 'selected' : '' }}>Cholecystectomy (Gallbladder Removal)</option>
                                <option value="Hysterectomy" {{ old('procedure_type', $booking->procedure_type) == 'Hysterectomy' ? 'selected' : '' }}>Hysterectomy</option>
                                <option value="Brain Tumor Removal" {{ old('procedure_type', $booking->procedure_type) == 'Brain Tumor Removal' ? 'selected' : '' }}>Brain Tumor Removal</option>
                                <option value="Thyroidectomy" {{ old('procedure_type', $booking->procedure_type) == 'Thyroidectomy' ? 'selected' : '' }}>Thyroidectomy</option>
                                <option value="Mastectomy" {{ old('procedure_type', $booking->procedure_type) == 'Mastectomy' ? 'selected' : '' }}>Mastectomy</option>
                                <option value="Other" {{ old('procedure_type', $booking->procedure_type) && !in_array(old('procedure_type', $booking->procedure_type), ['Appendectomy', 'Coronary Bypass', 'Knee Replacement', 'Hip Replacement', 'Cataract Surgery', 'Hernia Repair', 'Tonsillectomy', 'Cholecystectomy', 'Hysterectomy', 'Brain Tumor Removal', 'Thyroidectomy', 'Mastectomy']) ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('procedure_type')" class="mt-2" />
                            
                            <!-- Custom Procedure Type (shown only when "Other" is selected) -->
                            <div id="custom_procedure_container" class="mt-2" style="display: none;">
                                <x-input-label for="custom_procedure" :value="__('Specify Procedure')" />
                                <x-text-input id="custom_procedure" type="text" class="block mt-1 w-full" value="{{ old('procedure_type', $booking->procedure_type) }}" />
                                <p class="mt-1 text-sm text-gray-600">Specify the procedure type if you selected "Other"</p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $booking->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('ot-scheduling.bookings') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Booking') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const procedureSelect = document.getElementById('procedure_type');
            const customProcedureContainer = document.getElementById('custom_procedure_container');
            const customProcedureInput = document.getElementById('custom_procedure');
            
            // Function to show/hide custom procedure input
            function toggleCustomProcedure() {
                if (procedureSelect.value === 'Other') {
                    customProcedureContainer.style.display = 'block';
                } else {
                    customProcedureContainer.style.display = 'none';
                }
            }
            
            // Check on page load
            toggleCustomProcedure();
            
            // Check on change
            procedureSelect.addEventListener('change', toggleCustomProcedure);
            
            // Update hidden input before form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                if (procedureSelect.value === 'Other' && customProcedureInput.value.trim() !== '') {
                    procedureSelect.value = customProcedureInput.value.trim();
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 