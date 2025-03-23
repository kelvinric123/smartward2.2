@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Shift Schedule</h2>
            
            <a href="{{ route('shift-schedule.index') }}" class="text-indigo-600 hover:text-indigo-900">
                Back to Shift Schedules
            </a>
        </div>
        
        <form action="{{ route('shift-schedule.update', $shiftSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nurse_id" class="block text-sm font-medium text-gray-700 mb-1">Nurse</label>
                    <select name="nurse_id" id="nurse_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select Nurse</option>
                        @foreach($nurses as $nurse)
                            <option value="{{ $nurse->id }}" {{ old('nurse_id', $shiftSchedule->nurse_id) == $nurse->id ? 'selected' : '' }}>
                                {{ $nurse->name }} ({{ $nurse->ward_assignment }})
                            </option>
                        @endforeach
                    </select>
                    @error('nurse_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
                    <select name="ward_id" id="ward_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select Ward</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}" {{ old('ward_id', $shiftSchedule->ward_id) == $ward->id ? 'selected' : '' }}>
                                {{ $ward->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('ward_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="schedule_date" id="schedule_date" value="{{ old('schedule_date', $shiftSchedule->schedule_date->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    <p class="text-xs text-gray-500 mt-1">Note: Date ranges are only available when creating new shifts</p>
                    @error('schedule_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select name="shift" id="shift" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift }}" {{ old('shift', $shiftSchedule->shift) == $shift ? 'selected' : '' }}>
                                {{ $shift }}
                            </option>
                        @endforeach
                    </select>
                    @error('shift')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $shiftSchedule->start_time->format('H:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('start_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $shiftSchedule->end_time->format('H:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('end_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="confirmed" selected>Confirmed</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $shiftSchedule->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Update Shift Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill times based on shift selection
        const shiftSelect = document.getElementById('shift');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        // Define shift times from backend data
        const shiftTimes = @json($shiftTimes);
        
        console.log('Edit form - Shift times from backend:', shiftTimes);
        
        shiftSelect.addEventListener('change', function() {
            const selectedShift = this.value;
            console.log('Edit form - Selected shift:', selectedShift);
            
            if (selectedShift && shiftTimes[selectedShift]) {
                console.log('Edit form - Setting times:', shiftTimes[selectedShift]);
                startTimeInput.value = shiftTimes[selectedShift].start_time;
                endTimeInput.value = shiftTimes[selectedShift].end_time;
            } else {
                console.log('Edit form - No shift times found for:', selectedShift);
            }
        });
        
        // Auto-select ward based on nurse selection
        const nurseSelect = document.getElementById('nurse_id');
        const wardSelect = document.getElementById('ward_id');
        
        nurseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption) {
                const wardName = selectedOption.textContent.match(/\((.*?)\)/);
                if (wardName && wardName[1]) {
                    for (let i = 0; i < wardSelect.options.length; i++) {
                        if (wardSelect.options[i].textContent.trim() === wardName[1].trim()) {
                            wardSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
        });
        
        // Trigger change event on page load if shift is already selected
        if (shiftSelect.value) {
            console.log('Edit form - Initial shift value:', shiftSelect.value);
            shiftSelect.dispatchEvent(new Event('change'));
        } else {
            console.log('Edit form - No initial shift selected');
        }
    });
</script>
@endpush 