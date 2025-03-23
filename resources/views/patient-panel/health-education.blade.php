@extends('layouts.app')

@section('title', 'Health Education - Patient Panel')

@section('content')
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('patient-panel.show', ['bed' => $bed->id]) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Patient Panel
        </a>
    </div>

    <!-- Main Panel Container -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <!-- Header Bar - Similar to the patient panel header -->
        <div class="bg-teal-600 text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <div>
                    <div class="text-sm">Health Education Resources</div>
                    <div class="text-xs">{{ now()->format('H:i:s') }} {{ now()->format('D, m/d/Y') }}</div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="mr-4">{{ $bed->ward->temperature ?? '29°C' }}</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <!-- Add fullscreen button -->
                <div class="ml-4 cursor-pointer flex items-center bg-teal-700 rounded px-2 py-1 hover:bg-teal-800 transition-colors" id="fullscreenButton">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                    <span class="text-xs whitespace-nowrap" id="fullscreenText">Fullscreen</span>
                </div>
            </div>
        </div>

        <!-- Patient Info Bar -->
        <div class="bg-teal-500 text-white p-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-white text-teal-600 flex items-center justify-center text-xl font-bold">
                        @if($currentAdmission && $currentAdmission->patient)
                            {{ strtoupper(substr($currentAdmission->patient->first_name, 0, 1) . substr($currentAdmission->patient->last_name, 0, 1)) }}
                        @else
                            NA
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">{{ $patientName }}</h3>
                        <p class="text-white text-opacity-80">MRN: {{ $patientMRN }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold">{{ $bed->bed_number }}</div>
                    <p class="text-white text-opacity-80">{{ $bed->ward->name }}</p>
                </div>
            </div>
        </div>

        <!-- Health Education Content -->
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Health Education Resources</h2>
                <p class="text-gray-600">Access educational materials to improve your health knowledge</p>
            </div>

            <x-health-education />
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fullscreen button functionality
        const fullscreenButton = document.getElementById('fullscreenButton');
        const fullscreenText = document.getElementById('fullscreenText');
        const navigationBar = document.querySelector('nav');
        const pageHeader = document.querySelector('header.bg-white.shadow');
        
        if (fullscreenButton) {
            fullscreenButton.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                    if (fullscreenText) fullscreenText.textContent = 'Exit Fullscreen';
                    
                    // Hide navigation and header when entering fullscreen
                    if (navigationBar) navigationBar.style.display = 'none';
                    if (pageHeader) pageHeader.style.display = 'none';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        if (fullscreenText) fullscreenText.textContent = 'Fullscreen';
                        
                        // Show navigation and header when exiting fullscreen
                        if (navigationBar) navigationBar.style.display = '';
                        if (pageHeader) pageHeader.style.display = '';
                    }
                }
            });
        }
        
        // Update fullscreen text and UI when fullscreen state changes
        document.addEventListener('fullscreenchange', function() {
            if (document.fullscreenElement) {
                if (fullscreenText) fullscreenText.textContent = 'Exit Fullscreen';
                
                // Hide navigation and header when entering fullscreen
                if (navigationBar) navigationBar.style.display = 'none';
                if (pageHeader) pageHeader.style.display = 'none';
            } else {
                if (fullscreenText) fullscreenText.textContent = 'Fullscreen';
                
                // Show navigation and header when exiting fullscreen
                if (navigationBar) navigationBar.style.display = '';
                if (pageHeader) pageHeader.style.display = '';
            }
        });
        
        // Update time every second
        setInterval(function() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeElement = document.querySelector('.bg-teal-600 .text-xs');
            if (timeElement) {
                timeElement.textContent = `${hours}:${minutes}:${seconds} ${now.toLocaleDateString('en-US', { weekday: 'short', month: 'numeric', day: 'numeric', year: 'numeric' })}`;
            }
        }, 1000);
    });
</script>
@endpush 