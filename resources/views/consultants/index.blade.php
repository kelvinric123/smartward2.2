@extends('layouts.app')

@section('title', 'Consultants')

@section('header')
    {{ __('Consultants') }}
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Consultant Directory</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium">List of Available Consultants</h4>
                            <a href="{{ route('consultants.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                                Add New Consultant
                            </a>
                        </div>
                        
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Name</th>
                                    <th class="py-2 px-4 border-b text-left">Specialty</th>
                                    <th class="py-2 px-4 border-b text-left">Contact Number</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($consultants as $consultant)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $consultant->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $consultant->specialty }}</td>
                                        <td class="py-2 px-4 border-b">{{ $consultant->contact_number }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($consultant->status == 'Available')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $consultant->status }}
                                                </span>
                                            @elseif ($consultant->status == 'On Call')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $consultant->status }}
                                                </span>
                                            @elseif ($consultant->status == 'In Surgery')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $consultant->status }}
                                                </span>
                                            @elseif ($consultant->status == 'On Leave')
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $consultant->status }}
                                                </span>
                                            @else
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $consultant->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('consultants.show', $consultant->id) }}" class="text-blue-500 hover:underline mr-2">View</a>
                                            <a href="{{ route('consultants.edit', $consultant->id) }}" class="text-indigo-500 hover:underline mr-2">Edit</a>
                                            <form action="{{ route('consultants.deactivate', $consultant->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to deactivate this consultant?')">Deactivate</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">
                                            No consultants found. Add your first consultant to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 