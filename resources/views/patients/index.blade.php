@extends('layouts.app')

@section('title', 'Patients')

@section('header', 'Patients')

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Top Controls -->
                    <div class="flex flex-col md:flex-row justify-between mb-6">
                        <div class="mb-4 md:mb-0">
                            <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Patient
                            </a>
                        </div>
                        
                        <div class="w-full md:w-1/3">
                            <form action="{{ route('patients.index') }}" method="GET">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        value="{{ $search ?? '' }}" 
                                        placeholder="Search patients..." 
                                        class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <button type="submit" class="absolute inset-y-0 right-0 px-4 flex items-center bg-gray-100 rounded-r-md hover:bg-gray-200">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Patients Table -->
                    <div class="overflow-x-auto">
                        @if($patients->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MRN</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $patient->last_name }}, {{ $patient->first_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $patient->contact_number ?? 'No contact' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $patient->mrn }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ ucfirst($patient->gender) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $patient->date_of_birth->age }} years
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($patient->isAdmitted)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Admitted
                                                    </span>
                                                    @if($patient->activeAdmission() && $patient->activeAdmission()->dischargeChecklist)
                                                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                            Discharge in Progress ({{ $patient->activeAdmission()->dischargeChecklist->completion_percentage }}%)
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Not Admitted
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('patients.show', $patient) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                                                <a href="{{ route('patients.edit', $patient) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a>
                                                @if(!$patient->isAdmitted)
                                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="bg-gray-50 p-4 rounded">
                                @if(isset($search) && $search)
                                    <p class="text-gray-700">No patients found matching "{{ $search }}". Try a different search term or <a href="{{ route('patients.create') }}" class="text-indigo-600 hover:text-indigo-900">add a new patient</a>.</p>
                                @else
                                    <p class="text-gray-700">No patients have been added yet. <a href="{{ route('patients.create') }}" class="text-indigo-600 hover:text-indigo-900">Add your first patient</a>.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 