@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-6">
            @if(request()->has('bed'))
                <a href="{{ route('patient-panel.health-education', ['bed' => request()->bed]) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Health Resources
                </a>
            @else
                <a href="{{ route('health.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Resources
                </a>
            @endif
        </div>
        
        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-4 {{ $type === 'article' ? 'bg-blue-100 text-blue-800' : ($type === 'video' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
            {{ ucfirst($type) }}
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $title }}</h1>
        
        <div class="prose max-w-none">
            {!! $content !!}
        </div>
        
        @if($type === 'video')
        <div class="mt-8 bg-gray-100 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Video Resources</h3>
            <div class="aspect-w-16 aspect-h-9">
                <div class="bg-gray-200 rounded-lg flex items-center justify-center">
                    <p class="text-gray-600">Video player would be displayed here</p>
                </div>
            </div>
        </div>
        @endif
        
        <div class="mt-8 border-t pt-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Related Resources</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="bg-white border border-gray-200 p-4 rounded-lg hover:shadow-md transition duration-300">
                    <h4 class="font-medium text-gray-800">Healthy Eating Habits</h4>
                    <p class="text-sm text-gray-600 mt-1">Tips for maintaining a healthy diet</p>
                </a>
                <a href="#" class="bg-white border border-gray-200 p-4 rounded-lg hover:shadow-md transition duration-300">
                    <h4 class="font-medium text-gray-800">Sleep Hygiene</h4>
                    <p class="text-sm text-gray-600 mt-1">Importance of good sleep habits</p>
                </a>
                <a href="#" class="bg-white border border-gray-200 p-4 rounded-lg hover:shadow-md transition duration-300">
                    <h4 class="font-medium text-gray-800">Stress Management</h4>
                    <p class="text-sm text-gray-600 mt-1">Techniques to reduce daily stress</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 