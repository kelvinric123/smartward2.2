<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Discharge Checklist Details') }}
            </h2>
            <div>
                <a href="{{ route('patients.show', $dischargeChecklist->patient) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Back to Patient') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Patient Information
                            </h3>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Name:</span> {{ $dischargeChecklist->patient->full_name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">MRN:</span> {{ $dischargeChecklist->patient->mrn }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Gender:</span> {{ ucfirst($dischargeChecklist->patient->gender) }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Date of Birth:</span> {{ $dischargeChecklist->patient->date_of_birth->format('d M Y') }}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Admission Information
                            </h3>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Ward:</span> {{ $dischargeChecklist->admission->ward->name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Bed:</span> {{ $dischargeChecklist->admission->bed->bed_number }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Admission Date:</span> {{ $dischargeChecklist->admission->admission_date->format('d M Y, H:i') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Discharge Date:</span> 
                                {{ $dischargeChecklist->admission->actual_discharge_date ? $dischargeChecklist->admission->actual_discharge_date->format('d M Y, H:i') : 'Not discharged yet' }}
                            </p>
                            
                            @if($dischargeChecklist->planned_discharge_date)
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Planned Discharge:</span> 
                                {{ $dischargeChecklist->planned_discharge_date->format('d M Y') }}
                                <span class="ml-2 {{ $dischargeChecklist->planned_discharge_date->isPast() ? 'text-red-600 font-semibold' : 'text-blue-600 font-semibold' }}">
                                    ({{ $dischargeChecklist->days_until_discharge }})
                                </span>
                            </p>
                            
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Estimated Stay:</span> 
                                {{ $dischargeChecklist->total_admitted_days }} days
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Discharge Checklist
                        </h3>
                        <div class="mb-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $dischargeChecklist->completion_percentage }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-sm text-gray-600">{{ $dischargeChecklist->completion_percentage }}% Complete</p>
                                <p class="text-sm font-medium text-gray-700">{{ $dischargeChecklist->completed_items_count }} / {{ $dischargeChecklist->selected_items_count }} Items</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('blood_test_results') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->blood_test_results ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->blood_test_results)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">Blood Test Results</span>
                                @if($dischargeChecklist->isItemSelected('blood_test_results'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->blood_test_results ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->blood_test_results ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->blood_test_results_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('blood_test_results') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->blood_test_results_notes }}
                                </div>
                            @endif
                            
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('iv_medication') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->iv_medication ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->iv_medication)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">IV Medication</span>
                                @if($dischargeChecklist->isItemSelected('iv_medication'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->iv_medication ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->iv_medication ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->iv_medication_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('iv_medication') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->iv_medication_notes }}
                                </div>
                            @endif
                            
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('imaging') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->imaging ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->imaging)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">Imaging</span>
                                @if($dischargeChecklist->isItemSelected('imaging'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->imaging ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->imaging ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->imaging_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('imaging') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->imaging_notes }}
                                </div>
                            @endif
                            
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('procedures') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->procedures ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->procedures)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">Procedures</span>
                                @if($dischargeChecklist->isItemSelected('procedures'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->procedures ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->procedures ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->procedures_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('procedures') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->procedures_notes }}
                                </div>
                            @endif
                            
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('referral') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->referral ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->referral)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">Referral</span>
                                @if($dischargeChecklist->isItemSelected('referral'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->referral ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->referral ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->referral_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('referral') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->referral_notes }}
                                </div>
                            @endif
                            
                            <div class="flex items-center {{ !$dischargeChecklist->isItemSelected('documentation') ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 h-5 w-5 {{ $dischargeChecklist->documentation ? 'text-green-500' : 'text-gray-300' }}">
                                    @if($dischargeChecklist->documentation)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm text-gray-700">Documentation</span>
                                @if($dischargeChecklist->isItemSelected('documentation'))
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->documentation ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $dischargeChecklist->documentation ? 'Completed' : 'Required' }}
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Optional
                                    </span>
                                @endif
                            </div>
                            @if($dischargeChecklist->documentation_notes)
                                <div class="ml-8 mt-1 mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded {{ !$dischargeChecklist->isItemSelected('documentation') ? 'opacity-50' : '' }}">
                                    {{ $dischargeChecklist->documentation_notes }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($dischargeChecklist->additional_notes)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Additional Notes
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $dischargeChecklist->additional_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Status Information
                        </h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Status:</span> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dischargeChecklist->status === 'completed' ? 'bg-green-100 text-green-800' : ($dischargeChecklist->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $dischargeChecklist->status)) }}
                            </span>
                        </p>
                        @if($dischargeChecklist->completed_by)
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Completed By:</span> {{ $dischargeChecklist->completedBy->name }}
                        </p>
                        @endif
                        @if($dischargeChecklist->completed_at)
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Completed At:</span> {{ $dischargeChecklist->completed_at->format('d M Y, H:i') }}
                        </p>
                        @endif
                    </div>

                    @if($dischargeChecklist->status === 'in_progress')
                    <div class="flex justify-end">
                        <a href="{{ route('discharge-checklist.edit', $dischargeChecklist) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Continue Discharge Process') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 