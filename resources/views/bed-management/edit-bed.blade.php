@extends('layouts.app')

@section('title', 'Edit Bed')

@section('header', "Edit Bed {$bed->bed_number}")

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('beds.show', $bed) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Bed Details
                        </a>
                    </div>

                    <form action="{{ route('beds.update', $bed) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
                                <input type="text" value="{{ $bed->ward->name }} ({{ $bed->ward->code }})" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-gray-100 sm:text-sm rounded-md" readonly>
                                <input type="hidden" id="ward_id" name="ward_id" value="{{ $bed->ward_id }}">
                                <p class="text-xs text-gray-500 mt-1">Ward cannot be changed after bed creation</p>
                                @error('ward_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bed_number" class="block text-sm font-medium text-gray-700 mb-1">Bed Number</label>
                                <input type="text" id="bed_number" name="bed_number" value="{{ old('bed_number', $bed->bed_number) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('bed_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Bed Type</label>
                                <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($bedTypes as $type)
                                        <option value="{{ $type }}" {{ $bed->type == $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach($statuses as $status)
                                        @if($status !== 'occupied' || $bed->status === 'occupied')
                                            <option value="{{ $status }}" {{ $bed->status == $status ? 'selected' : '' }}
                                                {{ $status === 'occupied' && $bed->status !== 'occupied' ? 'disabled' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="mt-1 text-xs">
                                    @if($bed->status === 'occupied')
                                        <p class="text-yellow-500 mb-1">
                                            Warning: Changing an occupied bed's status will only work if you change it to "cleaning" and will discharge the current patient.
                                        </p>
                                    @elseif($bed->status === 'available')
                                        <p class="text-blue-500">
                                            Available beds can be used for patient admission. You can change status to "maintenance" or "reserved".
                                        </p>
                                    @elseif($bed->status === 'cleaning')
                                        <p class="text-green-500">
                                            Beds in cleaning status should be marked as "available" when cleaning is complete.
                                        </p>
                                    @elseif($bed->status === 'maintenance')
                                        <p class="text-orange-500">
                                            Maintenance status indicates bed is undergoing repairs or maintenance. Change to "available" when ready for use.
                                        </p>
                                    @elseif($bed->status === 'reserved')
                                        <p class="text-purple-500">
                                            Reserved beds are being held for planned admissions. Change to "available" when ready for use.
                                        </p>
                                    @endif
                                </div>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($bed->status !== 'occupied')
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" name="notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('notes', $bed->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @else
                            <input type="hidden" id="notes" name="notes" value="{{ $bed->notes }}">
                            @endif
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Bed
                            </button>
                            <a href="{{ route('beds.show', $bed) }}" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 