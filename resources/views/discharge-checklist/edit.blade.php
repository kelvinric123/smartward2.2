<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Discharge Checklist') }}
            </h2>
            <div>
                @if(isset($from) && $from === 'bed-details' && isset($bedId))
                    <a href="{{ route('beds.show', $bedId) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Back to Bed Details') }}
                    </a>
                @else
                    <a href="{{ route('patients.show', $dischargeChecklist->patient) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Back to Patient') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Patient: {{ $dischargeChecklist->patient->full_name }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ __('MRN') }}: {{ $dischargeChecklist->patient->mrn }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Ward') }}: {{ $dischargeChecklist->admission->ward->name }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Bed') }}: {{ $dischargeChecklist->admission->bed->bed_number }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Admission Date') }}: {{ $dischargeChecklist->admission->admission_date->format('d M Y, H:i') }}
                        </p>
                        
                        @if($dischargeChecklist->planned_discharge_date)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">
                                    {{ __('Planned Discharge') }}: {{ $dischargeChecklist->planned_discharge_date->format('d M Y') }}
                                </p>
                                <p class="text-sm font-semibold {{ $dischargeChecklist->planned_discharge_date->isPast() ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ $dischargeChecklist->days_until_discharge }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ __('Total Admitted Days') }}: {{ $dischargeChecklist->total_admitted_days }} days
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $dischargeChecklist->completion_percentage }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-600">{{ $dischargeChecklist->completion_percentage }}% Complete</p>
                            <p class="text-sm font-medium text-gray-700">{{ $dischargeChecklist->completed_items_count }} / {{ $dischargeChecklist->selected_items_count }} Items</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('discharge-checklist.update', $dischargeChecklist) }}" id="checklist-form">
                        @csrf
                        @method('PATCH')
                        
                        @if(isset($from))
                            <input type="hidden" name="from" value="{{ $from }}">
                        @endif
                        
                        @if(isset($bedId))
                            <input type="hidden" name="bed_id" value="{{ $bedId }}">
                        @endif
                        
                        <div class="space-y-4">
                            <div>
                                <label for="planned_discharge_date" class="block text-sm font-medium text-gray-700">Planned Discharge Date</label>
                                <input type="date" id="planned_discharge_date" name="planned_discharge_date" value="{{ $dischargeChecklist->planned_discharge_date ? $dischargeChecklist->planned_discharge_date->format('Y-m-d') : '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="text-xs text-gray-500 mt-1">Set the expected discharge date to track progress</p>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-lg font-semibold mb-4">Checklist Items</h3>
                                
                                <!-- Blood Test Results -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="blood_test_results" name="blood_test_results" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->blood_test_results ? 'checked' : '' }} data-item="blood_test_results">
                                            <label for="blood_test_results" class="ml-3 block text-sm font-medium text-gray-700">Blood Test Results</label>
                                        </div>
                                        <div id="blood_test_results_complete_button" class="{{ $dischargeChecklist->isItemSelected('blood_test_results') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'blood_test_results']) }}">
                                                @csrf
                                                <input type="hidden" name="blood_test_results" value="1">
                                                <input type="hidden" name="blood_test_results_notes" value="{{ old('blood_test_results_notes', $dischargeChecklist->blood_test_results_notes) }}">
                                                
                                                @if(isset($from))
                                                    <input type="hidden" name="from" value="{{ $from }}">
                                                @endif
                                                
                                                @if(isset($bedId))
                                                    <input type="hidden" name="bed_id" value="{{ $bedId }}">
                                                @endif
                                                
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="blood_test_results_notes_container" class="{{ $dischargeChecklist->isItemSelected('blood_test_results') ? '' : 'hidden' }}">
                                        <label for="blood_test_results_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="blood_test_results_notes" name="blood_test_results_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('blood_test_results_notes', $dischargeChecklist->blood_test_results_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- IV Medication -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="iv_medication" name="iv_medication" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->iv_medication ? 'checked' : '' }} data-item="iv_medication">
                                            <label for="iv_medication" class="ml-3 block text-sm font-medium text-gray-700">IV Medication</label>
                                        </div>
                                        <div id="iv_medication_complete_button" class="{{ $dischargeChecklist->isItemSelected('iv_medication') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'iv_medication']) }}">
                                                @csrf
                                                <input type="hidden" name="iv_medication" value="1">
                                                <input type="hidden" name="iv_medication_notes" value="{{ old('iv_medication_notes', $dischargeChecklist->iv_medication_notes) }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="iv_medication_notes_container" class="{{ $dischargeChecklist->isItemSelected('iv_medication') ? '' : 'hidden' }}">
                                        <label for="iv_medication_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="iv_medication_notes" name="iv_medication_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('iv_medication_notes', $dischargeChecklist->iv_medication_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Imaging -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="imaging" name="imaging" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->imaging ? 'checked' : '' }} data-item="imaging">
                                            <label for="imaging" class="ml-3 block text-sm font-medium text-gray-700">Imaging</label>
                                        </div>
                                        <div id="imaging_complete_button" class="{{ $dischargeChecklist->isItemSelected('imaging') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'imaging']) }}">
                                                @csrf
                                                <input type="hidden" name="imaging" value="1">
                                                <input type="hidden" name="imaging_notes" value="{{ old('imaging_notes', $dischargeChecklist->imaging_notes) }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="imaging_notes_container" class="{{ $dischargeChecklist->isItemSelected('imaging') ? '' : 'hidden' }}">
                                        <label for="imaging_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="imaging_notes" name="imaging_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('imaging_notes', $dischargeChecklist->imaging_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Procedures -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="procedures" name="procedures" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->procedures ? 'checked' : '' }} data-item="procedures">
                                            <label for="procedures" class="ml-3 block text-sm font-medium text-gray-700">Procedures</label>
                                        </div>
                                        <div id="procedures_complete_button" class="{{ $dischargeChecklist->isItemSelected('procedures') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'procedures']) }}">
                                                @csrf
                                                <input type="hidden" name="procedures" value="1">
                                                <input type="hidden" name="procedures_notes" value="{{ old('procedures_notes', $dischargeChecklist->procedures_notes) }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="procedures_notes_container" class="{{ $dischargeChecklist->isItemSelected('procedures') ? '' : 'hidden' }}">
                                        <label for="procedures_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="procedures_notes" name="procedures_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('procedures_notes', $dischargeChecklist->procedures_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Referral -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="referral" name="referral" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->referral ? 'checked' : '' }} data-item="referral">
                                            <label for="referral" class="ml-3 block text-sm font-medium text-gray-700">Referral</label>
                                        </div>
                                        <div id="referral_complete_button" class="{{ $dischargeChecklist->isItemSelected('referral') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'referral']) }}">
                                                @csrf
                                                <input type="hidden" name="referral" value="1">
                                                <input type="hidden" name="referral_notes" value="{{ old('referral_notes', $dischargeChecklist->referral_notes) }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="referral_notes_container" class="{{ $dischargeChecklist->isItemSelected('referral') ? '' : 'hidden' }}">
                                        <label for="referral_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="referral_notes" name="referral_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('referral_notes', $dischargeChecklist->referral_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Documentation -->
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <input id="documentation" name="documentation" type="checkbox" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded checklist-item" value="1" {{ $dischargeChecklist->documentation ? 'checked' : '' }} data-item="documentation">
                                            <label for="documentation" class="ml-3 block text-sm font-medium text-gray-700">Documentation</label>
                                        </div>
                                        <div id="documentation_complete_button" class="{{ $dischargeChecklist->isItemSelected('documentation') ? '' : 'hidden' }}">
                                            <form method="POST" action="{{ route('discharge-checklist.complete-item', [$dischargeChecklist, 'documentation']) }}">
                                                @csrf
                                                <input type="hidden" name="documentation" value="1">
                                                <input type="hidden" name="documentation_notes" value="{{ old('documentation_notes', $dischargeChecklist->documentation_notes) }}">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    {{ __('Complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="documentation_notes_container" class="{{ $dischargeChecklist->isItemSelected('documentation') ? '' : 'hidden' }}">
                                        <label for="documentation_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea id="documentation_notes" name="documentation_notes" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('documentation_notes', $dischargeChecklist->documentation_notes) }}</textarea>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="additional_notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                    <textarea id="additional_notes" name="additional_notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $dischargeChecklist->additional_notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Save Progress') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <form method="POST" action="{{ route('discharge-checklist.complete', $dischargeChecklist) }}">
                            @csrf
                            
                            <input type="hidden" name="blood_test_results" value="{{ $dischargeChecklist->blood_test_results ? '1' : '0' }}">
                            <input type="hidden" name="blood_test_results_notes" value="{{ $dischargeChecklist->blood_test_results_notes }}">
                            <input type="hidden" name="iv_medication" value="{{ $dischargeChecklist->iv_medication ? '1' : '0' }}">
                            <input type="hidden" name="iv_medication_notes" value="{{ $dischargeChecklist->iv_medication_notes }}">
                            <input type="hidden" name="imaging" value="{{ $dischargeChecklist->imaging ? '1' : '0' }}">
                            <input type="hidden" name="imaging_notes" value="{{ $dischargeChecklist->imaging_notes }}">
                            <input type="hidden" name="procedures" value="{{ $dischargeChecklist->procedures ? '1' : '0' }}">
                            <input type="hidden" name="procedures_notes" value="{{ $dischargeChecklist->procedures_notes }}">
                            <input type="hidden" name="referral" value="{{ $dischargeChecklist->referral ? '1' : '0' }}">
                            <input type="hidden" name="referral_notes" value="{{ $dischargeChecklist->referral_notes }}">
                            <input type="hidden" name="documentation" value="{{ $dischargeChecklist->documentation ? '1' : '0' }}">
                            <input type="hidden" name="documentation_notes" value="{{ $dischargeChecklist->documentation_notes }}">
                            <input type="hidden" name="additional_notes" value="{{ $dischargeChecklist->additional_notes }}">
                            <input type="hidden" name="planned_discharge_date" value="{{ $dischargeChecklist->planned_discharge_date ? $dischargeChecklist->planned_discharge_date->format('Y-m-d') : '' }}">
                            
                            @if(isset($from))
                                <input type="hidden" name="from" value="{{ $from }}">
                            @endif
                            
                            @if(isset($bedId))
                                <input type="hidden" name="bed_id" value="{{ $bedId }}">
                            @endif
                            
                            <div>
                                <label for="discharge_notes" class="block text-sm font-medium text-gray-700">Discharge Notes</label>
                                <textarea id="discharge_notes" name="discharge_notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            
                            <div class="mt-6 flex justify-between items-center">
                                <div>
                                    @if (!$dischargeChecklist->isComplete())
                                        <p class="text-sm text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            All selected items must be completed before discharge
                                        </p>
                                    @elseif($dischargeChecklist->selected_items_count == 0)
                                        <p class="text-sm text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            At least one checklist item must be selected
                                        </p>
                                    @endif
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" {{ (!$dischargeChecklist->isComplete() || $dischargeChecklist->selected_items_count == 0) ? 'disabled' : '' }}>
                                    {{ __('Complete Discharge Process') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all checkboxes with the checklist-item class
            const checklistItems = document.querySelectorAll('.checklist-item');
            
            // Add event listeners to each checkbox
            checklistItems.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const itemName = this.dataset.item;
                    const completeButton = document.getElementById(`${itemName}_complete_button`);
                    const notesContainer = document.getElementById(`${itemName}_notes_container`);
                    
                    // Show/hide the complete button and notes container based on checkbox state
                    if (this.checked) {
                        completeButton.classList.remove('hidden');
                        notesContainer.classList.remove('hidden');
                    } else {
                        completeButton.classList.add('hidden');
                        notesContainer.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</x-app-layout> 