@extends('layouts.app')

@section('title', 'Nurse Details')

@section('header')
    {{ __('Nurse Details') }}
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Nurse Profile: {{ $nurse->name }}</h3>
                        <div>
                            <a href="{{ route('nurses.edit', $nurse->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm mr-2">
                                Edit
                            </a>
                            <a href="{{ route('nurses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">
                                Back to List
                            </a>
                        </div>
                    </div>
                    
                    <!-- Nurse Status Badge -->
                    <div class="mb-6">
                        <span class="text-sm font-medium text-gray-700">Current Status:</span>
                        @if ($nurse->status == 'On Duty')
                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $nurse->status }}
                            </span>
                        @elseif ($nurse->status == 'Off Duty')
                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $nurse->status }}
                            </span>
                        @elseif ($nurse->status == 'Break')
                            <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $nurse->status }}
                            </span>
                        @elseif ($nurse->status == 'On Leave')
                            <span class="ml-2 bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $nurse->status }}
                            </span>
                        @else
                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $nurse->status }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Nurse Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-md font-medium mb-4">Professional Details</h4>
                            <dl class="space-y-2">
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->name }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Position</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->position }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Ward Assignment</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->ward_assignment }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Shift</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->shift }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Employment Date</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->employment_date ? date('F j, Y', strtotime($nurse->employment_date)) : 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium mb-4">Contact Information</h4>
                            <dl class="space-y-2">
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->contact_number ?? 'Not provided' }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $nurse->email ?? 'Not provided' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Notes Section -->
                    <div class="mt-8">
                        <h4 class="text-md font-medium mb-2">Notes</h4>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-700">{{ $nurse->notes ?? 'No additional notes available.' }}</p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end">
                            <form action="{{ route('nurses.deactivate', $nurse->id) }}" method="POST" class="inline mr-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm" onclick="return confirm('Are you sure you want to deactivate this nurse?')">
                                    Deactivate
                                </button>
                            </form>
                            
                            <form action="{{ route('nurses.destroy', $nurse->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm" onclick="return confirm('Are you sure you want to delete this nurse? This action cannot be undone.')">
                                    Delete Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 