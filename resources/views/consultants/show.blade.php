@extends('layouts.app')

@section('title', 'Consultant Details')

@section('header')
    {{ __('Consultant Details') }}
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Consultant Profile: {{ $consultant->name }}</h3>
                        <div>
                            <a href="{{ route('consultants.edit', $consultant->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm mr-2">
                                Edit
                            </a>
                            <a href="{{ route('consultants.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">
                                Back to List
                            </a>
                        </div>
                    </div>
                    
                    <!-- Consultant Status Badge -->
                    <div class="mb-6">
                        <span class="text-sm font-medium text-gray-700">Current Status:</span>
                        @if ($consultant->status == 'Available')
                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $consultant->status }}
                            </span>
                        @elseif ($consultant->status == 'On Call')
                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $consultant->status }}
                            </span>
                        @elseif ($consultant->status == 'In Surgery')
                            <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $consultant->status }}
                            </span>
                        @elseif ($consultant->status == 'On Leave')
                            <span class="ml-2 bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $consultant->status }}
                            </span>
                        @else
                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $consultant->status }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Consultant Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-md font-medium mb-4">Professional Details</h4>
                            <dl class="space-y-2">
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $consultant->name }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Specialty</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $consultant->specialty }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Office Location</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $consultant->office_location ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium mb-4">Contact Information</h4>
                            <dl class="space-y-2">
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $consultant->contact_number }}</dd>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $consultant->email ?? 'Not provided' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Notes Section -->
                    <div class="mt-8">
                        <h4 class="text-md font-medium mb-2">Notes</h4>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-700">{{ $consultant->notes ?? 'No additional notes available.' }}</p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end">
                            <form action="{{ route('consultants.deactivate', $consultant->id) }}" method="POST" class="inline mr-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm" onclick="return confirm('Are you sure you want to deactivate this consultant?')">
                                    Deactivate
                                </button>
                            </form>
                            
                            <form action="{{ route('consultants.destroy', $consultant->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm" onclick="return confirm('Are you sure you want to delete this consultant? This action cannot be undone.')">
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