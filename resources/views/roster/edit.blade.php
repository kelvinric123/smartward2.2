@extends('layouts.app')

@section('title', 'Edit Nurse Roster')

@section('header')
{{ __('Edit Nurse Roster') }}
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium">Edit Roster for {{ $nurse->name }}</h3>
                    <div>
                        <a href="{{ route('roster.index') }}" class="text-gray-600 hover:text-gray-900 mr-2">
                            Back to Roster
                        </a>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 mb-6 rounded-md">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-sm font-medium text-gray-700">Name</span>
                            <span class="block font-semibold">{{ $nurse->name }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-700">Position</span>
                            <span class="block font-semibold">{{ $nurse->position }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-700">Current Ward</span>
                            <span class="block font-semibold">{{ $nurse->ward_assignment ?: 'Unassigned' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-700">Status</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($nurse->status == 'On Duty') bg-green-100 text-green-800 
                                @elseif($nurse->status == 'Off Duty') bg-gray-100 text-gray-800 
                                @elseif($nurse->status == 'Break') bg-yellow-100 text-yellow-800 
                                @elseif($nurse->status == 'On Leave') bg-red-100 text-red-800 
                                @else bg-blue-100 text-blue-800 
                                @endif">
                                {{ $nurse->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('roster.update', $nurse->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="ward_assignment" class="block text-sm font-medium text-gray-700 mb-1">Assign to Ward</label>
                                <select name="ward_assignment" id="ward_assignment" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($wards as $ward)
                                        <option value="{{ $ward->name }}" {{ $nurse->ward_assignment == $ward->name ? 'selected' : '' }}>
                                            {{ $ward->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="roster_notes" class="block text-sm font-medium text-gray-700 mb-1">Roster Notes</label>
                                <input type="text" name="roster_notes" id="roster_notes" value="{{ $nurse->roster_notes }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3">Weekly Schedule</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    @endphp
                                    
                                    @foreach($days as $day)
                                        <tr>
                                            <td class="py-2 px-4 border-b">
                                                <span class="font-medium">{{ $day }}</span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <select name="roster[{{ $day }}][shift]" class="w-full rounded-md border-gray-300 shadow-sm">
                                                    <option value="">Not Scheduled</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift }}" {{ isset($roster[$day]['shift']) && $roster[$day]['shift'] == $shift ? 'selected' : '' }}>
                                                            {{ $shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="time" name="roster[{{ $day }}][start_time]" value="{{ $roster[$day]['start_time'] ?? '' }}" class="rounded-md border-gray-300 shadow-sm">
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="time" name="roster[{{ $day }}][end_time]" value="{{ $roster[$day]['end_time'] ?? '' }}" class="rounded-md border-gray-300 shadow-sm">
                                            </td>
                                            <td class="py-2 px-4 border-b text-center">
                                                <input type="checkbox" name="roster[{{ $day }}][assigned]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ isset($roster[$day]['assigned']) && $roster[$day]['assigned'] ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3">Shift Preferences</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($shifts as $shift)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                        name="shift_preferences[]" 
                                        id="pref_{{ $shift }}" 
                                        value="{{ $shift }}" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ isset($nurse->shift_preferences) && is_array($nurse->shift_preferences) && in_array($shift, $nurse->shift_preferences) ? 'checked' : '' }}
                                    >
                                    <label for="pref_{{ $shift }}" class="ml-2 text-sm text-gray-700">{{ $shift }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="{{ route('roster.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Roster
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill end times based on shift selection
        const shiftSelects = document.querySelectorAll('select[name^="roster"][name$="[shift]"]');
        
        shiftSelects.forEach(select => {
            select.addEventListener('change', function() {
                const day = this.name.match(/roster\[(.*?)\]/)[1];
                const startTimeInput = document.querySelector(`input[name="roster[${day}][start_time]"]`);
                const endTimeInput = document.querySelector(`input[name="roster[${day}][end_time]"]`);
                const assignedCheckbox = document.querySelector(`input[name="roster[${day}][assigned]"]`);
                
                // Set default times based on shift
                switch (this.value) {
                    case 'Morning':
                        startTimeInput.value = '07:00';
                        endTimeInput.value = '15:00';
                        assignedCheckbox.checked = true;
                        break;
                    case 'Evening':
                        startTimeInput.value = '15:00';
                        endTimeInput.value = '23:00';
                        assignedCheckbox.checked = true;
                        break;
                    case 'Night':
                        startTimeInput.value = '23:00';
                        endTimeInput.value = '07:00';
                        assignedCheckbox.checked = true;
                        break;
                    case '':
                        startTimeInput.value = '';
                        endTimeInput.value = '';
                        assignedCheckbox.checked = false;
                        break;
                    // Keep custom times if Custom is selected
                }
            });
        });
    });
</script>
@endsection 