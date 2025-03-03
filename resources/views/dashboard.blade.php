@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <!-- Flash Messages -->
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif
    
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Statistics Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
        <div class="p-6 text-gray-900">
            <h3 class="font-semibold text-lg mb-4">Hospital Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-600 font-medium">Total Patients</p>
                    <p class="text-2xl font-bold">157</p>
                    <p class="text-xs text-gray-500 mt-1">12% increase from last month</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm text-green-600 font-medium">Available Beds</p>
                    <p class="text-2xl font-bold">42</p>
                    <p class="text-xs text-gray-500 mt-1">86% occupancy rate</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <p class="text-sm text-purple-600 font-medium">Medical Staff</p>
                    <p class="text-2xl font-bold">68</p>
                    <p class="text-xs text-gray-500 mt-1">+3 new this week</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <p class="text-sm text-amber-600 font-medium">Appointments Today</p>
                    <p class="text-2xl font-bold">24</p>
                    <p class="text-xs text-gray-500 mt-1">4 pending confirmation</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
        <div class="p-6 text-gray-900">
            <h3 class="font-semibold text-lg mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-user-plus mr-1"></i> Add Patient
                </a>
                <a href="#" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    <i class="fas fa-calendar-plus mr-1"></i> Schedule Appointment
                </a>
                <a href="#" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium">
                    <i class="fas fa-bed mr-1"></i> Assign Bed
                </a>
                <a href="#" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm font-medium">
                    <i class="fas fa-notes-medical mr-1"></i> View Records
                </a>
                <a href="#" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium">
                    <i class="fas fa-print mr-1"></i> Generate Report
                </a>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="font-semibold text-lg mb-4">Recent Activities</h3>
                <div class="space-y-3">
                    <div class="flex items-start border-b border-gray-200 pb-3">
                        <div class="bg-blue-100 text-blue-600 h-8 w-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">New patient admitted</p>
                            <p class="text-xs text-gray-500">John Doe was admitted to Ward 3, Room 4</p>
                            <p class="text-xs text-gray-400 mt-1">30 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-start border-b border-gray-200 pb-3">
                        <div class="bg-green-100 text-green-600 h-8 w-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Procedure completed</p>
                            <p class="text-xs text-gray-500">Dr. Smith completed surgery for patient #1242</p>
                            <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start border-b border-gray-200 pb-3">
                        <div class="bg-purple-100 text-purple-600 h-8 w-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Patient transferred</p>
                            <p class="text-xs text-gray-500">Jane Smith transferred from ICU to General Ward</p>
                            <p class="text-xs text-gray-400 mt-1">4 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start border-b border-gray-200 pb-3">
                        <div class="bg-amber-100 text-amber-600 h-8 w-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Appointment scheduled</p>
                            <p class="text-xs text-gray-500">Robert Johnson scheduled for checkup on Friday</p>
                            <p class="text-xs text-gray-400 mt-1">Yesterday</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all activities</a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="font-semibold text-lg mb-4">Notifications</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">Bed shortage in Pediatric Ward</p>
                                <p class="text-xs text-gray-500 mt-1">10 minutes ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 bg-red-50 border-l-4 border-red-400 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-radiation text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">Emergency staff meeting at 2 PM</p>
                                <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">New protocol updates available</p>
                                <p class="text-xs text-gray-500 mt-1">Yesterday</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all notifications</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Display user role information -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
        <div class="p-6 text-gray-900">
            <h3 class="font-semibold text-lg mb-2">User Information</h3>
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst(Auth::user()->role) }}</p>
        </div>
    </div>
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("You're logged in!") }}
            
            <div class="mt-4">
                <h3 class="font-semibold text-lg mb-2">Available Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    <!-- Always visible to all authenticated users -->
                    <a href="{{ route('dashboard') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                        <h4 class="font-medium">Dashboard</h4>
                        <p class="text-sm text-gray-600">View system dashboard</p>
                    </a>
                    
                    <!-- Show medical professional links only to appropriate roles -->
                    @if(Auth::user()->hasRoleLevel('doctor'))
                        <a href="{{ route('medical-professional.consultants') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                            <h4 class="font-medium">Consultants</h4>
                            <p class="text-sm text-gray-600">Manage hospital consultants</p>
                        </a>
                        
                        <a href="{{ route('medical-professional.nurses') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                            <h4 class="font-medium">Nurses</h4>
                            <p class="text-sm text-gray-600">Manage nursing staff</p>
                        </a>
                    @endif
                    
                    <!-- Show bed management links only to appropriate roles -->
                    @if(Auth::user()->hasRoleLevel('nurse'))
                        <a href="{{ route('bed-management.dashboard') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                            <h4 class="font-medium">Bed Management</h4>
                            <p class="text-sm text-gray-600">Manage hospital beds</p>
                        </a>
                        
                        <a href="{{ route('bed-management.bed-map') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                            <h4 class="font-medium">Bed Map</h4>
                            <p class="text-sm text-gray-600">View hospital bed map</p>
                        </a>
                        
                        <a href="{{ route('patients.index') }}" class="block p-4 bg-gray-100 rounded hover:bg-gray-200">
                            <h4 class="font-medium">Patients</h4>
                            <p class="text-sm text-gray-600">Manage patient records</p>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
