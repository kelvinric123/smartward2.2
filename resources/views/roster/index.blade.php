@extends('layouts.app')

@section('title', 'Nurse Roster Management')

@section('header')
{{ __('Nurse Roster Management') }}
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:justify-between mb-6">
                    <h3 class="text-lg font-medium mb-4">Nurse Roster Schedule</h3>
                    <div class="space-x-2">
                        <button id="toggleFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm">
                            <i class="fas fa-filter mr-1"></i> Filters
                        </button>
                        <a href="{{ route('nurses.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                            Manage Nurses
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div id="filterSection" class="mb-6 p-4 border rounded-md bg-gray-50 hidden">
                    <form action="{{ route('roster.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="ward" class="block text-sm font-medium text-gray-700 mb-1">Filter by Ward</label>
                            <select name="ward" id="ward" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">All Wards</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->name }}" {{ request('ward') == $ward->name ? 'selected' : '' }}>
                                        {{ $ward->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">Filter by Shift</label>
                            <select name="shift" id="shift" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">All Shifts</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift }}" {{ request('shift') == $shift ? 'selected' : '' }}>
                                        {{ $shift }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                                Apply Filters
                            </button>
                            <a href="{{ route('roster.index') }}" class="ml-2 text-gray-600 hover:text-gray-900 px-3 py-2">Clear</a>
                        </div>
                    </form>
                </div>

                <!-- Wards Tabs -->
                <div class="mb-6">
                    <div class="flex flex-wrap border-b">
                        <button class="ward-tab px-4 py-2 text-sm font-medium {{ !request('ward') ? 'bg-indigo-100 text-indigo-800 border-b-2 border-indigo-500' : 'text-gray-600 hover:text-gray-800' }}" data-ward="all">All Wards</button>
                        @foreach($wards as $ward)
                            <button class="ward-tab px-4 py-2 text-sm font-medium {{ request('ward') == $ward->name ? 'bg-indigo-100 text-indigo-800 border-b-2 border-indigo-500' : 'text-gray-600 hover:text-gray-800' }}" data-ward="{{ $ward->name }}">{{ $ward->name }}</button>
                        @endforeach
                    </div>
                </div>

                <!-- Roster By Ward View -->
                <div id="allWardsContent" class="{{ request('ward') ? 'hidden' : '' }}">
                    @foreach($nursesByWard as $wardName => $wardNurses)
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-semibold">{{ $wardName ?: 'Unassigned' }} ({{ $wardNurses->count() }} nurses)</h4>
                                <a href="{{ route('roster.ward', $wardName) }}" class="text-sm text-blue-600 hover:text-blue-800">View Ward Roster</a>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                            <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($wardNurses as $nurse)
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $nurse->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $nurse->position }}</td>
                                                <td class="py-2 px-4 border-b">{{ $nurse->shift }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($nurse->status == 'On Duty') bg-green-100 text-green-800 
                                                        @elseif($nurse->status == 'Off Duty') bg-gray-100 text-gray-800 
                                                        @elseif($nurse->status == 'Break') bg-yellow-100 text-yellow-800 
                                                        @elseif($nurse->status == 'On Leave') bg-red-100 text-red-800 
                                                        @else bg-blue-100 text-blue-800 
                                                        @endif">
                                                        {{ $nurse->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $nurse->last_roster_update ? $nurse->last_roster_update->diffForHumans() : 'Never' }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('roster.edit', $nurse->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit Roster</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Individual Ward Content (shown when ward is selected) -->
                @foreach($wards as $ward)
                    <div id="ward{{ str_replace(' ', '', $ward->name) }}Content" class="ward-content {{ request('ward') == $ward->name ? '' : 'hidden' }}">
                        <div class="mb-4">
                            <a href="{{ route('roster.ward', $ward->name) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm inline-block">
                                View {{ $ward->name }} Ward Roster Schedule
                            </a>
                        </div>
                        
                        <h4 class="text-md font-semibold mb-3">{{ $ward->name }} Ward Nurses</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $wardNurses = $nurses->where('ward_assignment', $ward->name);
                                    @endphp
                                    
                                    @if($wardNurses->count() > 0)
                                        @foreach($wardNurses as $nurse)
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $nurse->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $nurse->position }}</td>
                                                <td class="py-2 px-4 border-b">{{ $nurse->shift }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($nurse->status == 'On Duty') bg-green-100 text-green-800 
                                                        @elseif($nurse->status == 'Off Duty') bg-gray-100 text-gray-800 
                                                        @elseif($nurse->status == 'Break') bg-yellow-100 text-yellow-800 
                                                        @elseif($nurse->status == 'On Leave') bg-red-100 text-red-800 
                                                        @else bg-blue-100 text-blue-800 
                                                        @endif">
                                                        {{ $nurse->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $nurse->last_roster_update ? $nurse->last_roster_update->diffForHumans() : 'Never' }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('roster.edit', $nurse->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit Roster</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="py-3 px-4 text-center text-gray-500">No nurses assigned to this ward.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle filters
        const toggleFiltersBtn = document.getElementById('toggleFilters');
        const filterSection = document.getElementById('filterSection');
        
        toggleFiltersBtn.addEventListener('click', function() {
            filterSection.classList.toggle('hidden');
        });
        
        // Handle ward tabs
        const wardTabs = document.querySelectorAll('.ward-tab');
        const wardContents = document.querySelectorAll('.ward-content');
        const allWardsContent = document.getElementById('allWardsContent');
        
        wardTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const ward = this.getAttribute('data-ward');
                
                // Reset all tabs
                wardTabs.forEach(t => {
                    t.classList.remove('bg-indigo-100', 'text-indigo-800', 'border-b-2', 'border-indigo-500');
                    t.classList.add('text-gray-600', 'hover:text-gray-800');
                });
                
                // Activate clicked tab
                this.classList.add('bg-indigo-100', 'text-indigo-800', 'border-b-2', 'border-indigo-500');
                this.classList.remove('text-gray-600', 'hover:text-gray-800');
                
                // Hide all content
                wardContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                if (ward === 'all') {
                    allWardsContent.classList.remove('hidden');
                } else {
                    const wardContent = document.getElementById('ward' + ward.replace(/\s+/g, '') + 'Content');
                    if (wardContent) {
                        wardContent.classList.remove('hidden');
                        allWardsContent.classList.add('hidden');
                    }
                }
                
                // Update URL without reloading
                const url = new URL(window.location);
                if (ward === 'all') {
                    url.searchParams.delete('ward');
                } else {
                    url.searchParams.set('ward', ward);
                }
                window.history.pushState({}, '', url);
            });
        });
    });
</script>
@endsection 