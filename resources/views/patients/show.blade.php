@extends('layouts.app')

@section('title', 'Patient Details')

@section('header', 'Patient Details')

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 flex justify-between items-center">
                        @if(request()->has('from') && request()->get('from') === 'bed-map')
                            @if(request()->has('ward_id'))
                                <a href="{{ route('bed-management.bed-map', ['ward_id' => request()->get('ward_id')]) }}" class="text-indigo-600 hover:text-indigo-900">
                                    ← Back to Bed Map
                                </a>
                            @else
                                <a href="{{ route('bed-management.bed-map') }}" class="text-indigo-600 hover:text-indigo-900">
                                    ← Back to Bed Map
                                </a>
                            @endif
                        @else
                            <a href="{{ route('patients.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                ← Back to Patients
                            </a>
                        @endif
                        <div>
                            <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Edit Patient
                            </a>
                            @if(!$patient->admissions || $patient->admissions->where('status', 'active')->count() === 0)
                            <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this patient?')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete Patient
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Information Card -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Patient Information</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Medical Record Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->mrn }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->date_of_birth->format('M d, Y') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Age</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->date_of_birth->age }} years</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->gender) }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($patient->admissions && $patient->admissions->where('status', 'active')->count() > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Currently Admitted
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Not Admitted
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Contact Information Card -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Contact Information</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->address ?? 'Not provided' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->contact_number ?? 'Not provided' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->email ?? 'Not provided' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Emergency Contact</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_name ?? 'Not provided' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Emergency Contact Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_number ?? 'Not provided' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Medical Information Card -->
                        <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Medical Information</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Medical History</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->medical_history ?? 'No medical history recorded' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Allergies</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->allergies ?? 'No allergies recorded' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->notes ?? 'No notes recorded' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Vital Signs Section -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Vital Signs</h3>
                            <div>
                                <a href="{{ route('vital-signs.index', $patient) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    View All Vital Signs
                                </a>
                                <a href="{{ route('vital-signs.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Record New Vital Signs
                                </a>
                            </div>
                        </div>
                        
                        @if($patient->vitalSigns && $patient->vitalSigns->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heart Rate</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Pressure</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respiratory Rate</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O₂ Saturation</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($patient->vitalSigns->sortByDesc('created_at')->take(5) as $vitalSign)
                                            <tr class="{{ $vitalSign->hasAbnormalValues() ? 'bg-red-50' : '' }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $vitalSign->created_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isTemperatureNormal() && $vitalSign->temperature ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ $vitalSign->temperature ? $vitalSign->temperature . ' °C' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isHeartRateNormal() && $vitalSign->heart_rate ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isBpNormal() && $vitalSign->systolic_bp && $vitalSign->diastolic_bp ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ ($vitalSign->systolic_bp && $vitalSign->diastolic_bp) ? $vitalSign->systolic_bp . '/' . $vitalSign->diastolic_bp . ' mmHg' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isRespiratoryRateNormal() && $vitalSign->respiratory_rate ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . ' breaths/min' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$vitalSign->isOxygenSaturationNormal() && $vitalSign->oxygen_saturation ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('vital-signs.edit', $vitalSign) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                @if($patient->vitalSigns->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('vital-signs.index', $patient) }}" class="text-indigo-600 hover:text-indigo-900">
                                            View All {{ $patient->vitalSigns->count() }} Vital Signs Records
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-gray-700">No vital signs records found for this patient.</p>
                                <div class="mt-4">
                                    <a href="{{ route('vital-signs.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Record First Vital Signs
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Admissions Section -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Admission History</h3>
                            <!-- Add Transfer Ward Button for active admissions -->
                            @if($patient->admissions && $patient->admissions->where('status', 'active')->count() > 0)
                                @php $activeAdmission = $patient->admissions->where('status', 'active')->first(); @endphp
                                <button id="openTransferModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Transfer Ward
                                </button>
                            @endif
                        </div>
                        
                        @if($patient->admissions && $patient->admissions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bed/Ward</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discharge Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($patient->admissions->sortByDesc('admission_date') as $admission)
                                            <tr class="admission-row" data-admission-id="{{ $admission->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $admission->admission_date->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $admission->bed->bed_number ?? 'Not assigned' }} ({{ $admission->bed->ward->name ?? 'N/A' }})
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $admission->diagnosis ?? 'Not specified' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($admission->status === 'discharged')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Discharged
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $admission->actual_discharge_date ? $admission->actual_discharge_date->format('M d, Y H:i') : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('admissions.show', $admission) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                                                    
                                                    @if($admission->transferHistory && $admission->transferHistory->count() > 0)
                                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 toggle-transfer-history" data-admission-id="{{ $admission->id }}">
                                                            Show Transfers ({{ $admission->transferHistory->count() }})
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            <!-- Transfer History Row (initially hidden) -->
                                            @if($admission->transferHistory && $admission->transferHistory->count() > 0)
                                                <tr class="transfer-history-row bg-gray-50 hidden" id="transfer-history-{{ $admission->id }}">
                                                    <td colspan="6" class="px-6 py-4">
                                                        <h4 class="font-medium text-gray-900 mb-2">Transfer History</h4>
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-full divide-y divide-gray-200">
                                                                <thead class="bg-gray-100">
                                                                    <tr>
                                                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">By</th>
                                                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="bg-white divide-y divide-gray-200">
                                                                    @foreach($admission->transferHistory as $transfer)
                                                                        <tr>
                                                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                                                {{ $transfer->transfer_date->format('M d, Y H:i') }}
                                                                            </td>
                                                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                                                Bed {{ $transfer->fromBed->bed_number ?? 'Unknown' }} ({{ $transfer->fromWard->name ?? 'Unknown Ward' }})
                                                                            </td>
                                                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                                                Bed {{ $transfer->toBed->bed_number ?? 'Unknown' }} ({{ $transfer->toWard->name ?? 'Unknown Ward' }})
                                                                            </td>
                                                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                                                {{ $transfer->transferredBy->name ?? 'Unknown' }}
                                                                            </td>
                                                                            <td class="px-3 py-2 text-xs text-gray-900">
                                                                                {{ $transfer->notes ?? 'No notes' }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-gray-700">No admission records found for this patient.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Ward Modal -->
    @if($patient->admissions && $patient->admissions->where('status', 'active')->count() > 0)
        @php $activeAdmission = $patient->admissions->where('status', 'active')->first(); @endphp
        <div id="transferModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden max-w-lg w-full">
                <div class="px-6 py-4 bg-indigo-600 text-white">
                    <h3 class="text-lg font-medium">Transfer Patient to Another Ward</h3>
                </div>
                <form action="{{ route('patients.transfer-ward', $patient) }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="admission_id" value="{{ $activeAdmission->id }}">
                    @if(request()->has('from'))
                        <input type="hidden" name="from" value="{{ request()->get('from') }}">
                    @endif
                    @if(request()->has('ward_id'))
                        <input type="hidden" name="ward_id" value="{{ request()->get('ward_id') }}">
                    @endif
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">
                            Current Location: <span class="font-medium">Bed {{ $activeAdmission->bed->bed_number ?? 'Unknown' }} in {{ $activeAdmission->bed->ward->name ?? 'Unknown Ward' }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Select Destination Ward</label>
                        <select id="ward_id" name="ward_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select Ward</option>
                            <!-- Wards with available beds will be populated via AJAX -->
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Only wards with available beds are shown</p>
                    </div>
                    
                    <div id="availableBedsContainer" class="mb-4 hidden">
                        <label for="bed_id" class="block text-sm font-medium text-gray-700 mb-2">Select Bed</label>
                        <select id="bed_id" name="bed_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select a ward first</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="transfer_notes" class="block text-sm font-medium text-gray-700 mb-2">Transfer Notes</label>
                        <textarea id="transfer_notes" name="transfer_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Reason for transfer"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeTransferModal" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Transfer Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('transferModal');
                const openBtn = document.getElementById('openTransferModal');
                const closeBtn = document.getElementById('closeTransferModal');
                const wardSelect = document.getElementById('ward_id');
                const bedSelect = document.getElementById('bed_id');
                const bedContainer = document.getElementById('availableBedsContainer');
                
                // Open modal
                openBtn.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                    // Fetch available wards via AJAX
                    fetch('{{ route("patients.available-wards") }}')
                        .then(response => response.json())
                        .then(data => {
                            wardSelect.innerHTML = '<option value="">Select Ward</option>';
                            data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.id;
                                option.textContent = `${ward.name} (${ward.available_beds} available beds)`;
                                wardSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching wards:', error);
                            wardSelect.innerHTML = '<option value="">Error loading wards</option>';
                        });
                });
                
                // Close modal
                closeBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
                
                // Ward selection change
                wardSelect.addEventListener('change', function() {
                    const wardId = this.value;
                    if (!wardId) {
                        bedContainer.classList.add('hidden');
                        return;
                    }
                    
                    // Fetch available beds in selected ward
                    fetch('{{ route("patients.available-beds", ["ward" => "__WARD_ID__"]) }}'.replace('__WARD_ID__', wardId))
                        .then(response => response.json())
                        .then(data => {
                            bedSelect.innerHTML = '<option value="">Select Bed</option>';
                            data.forEach(bed => {
                                const option = document.createElement('option');
                                option.value = bed.id;
                                option.textContent = `Bed ${bed.bed_number} (${bed.type})`;
                                bedSelect.appendChild(option);
                            });
                            bedContainer.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error fetching beds:', error);
                            bedSelect.innerHTML = '<option value="">Error loading beds</option>';
                        });
                });
                
                // Close modal when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add toggle functionality for transfer history
            const toggleButtons = document.querySelectorAll('.toggle-transfer-history');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const admissionId = this.getAttribute('data-admission-id');
                    const historyRow = document.getElementById(`transfer-history-${admissionId}`);
                    
                    // Toggle visibility
                    if (historyRow.classList.contains('hidden')) {
                        historyRow.classList.remove('hidden');
                        this.textContent = `Hide Transfers (${this.textContent.match(/\d+/)[0]})`;
                    } else {
                        historyRow.classList.add('hidden');
                        this.textContent = `Show Transfers (${this.textContent.match(/\d+/)[0]})`;
                    }
                });
            });
        });
    </script>
@endsection 