@php
    use Illuminate\Support\Str;
    $bedParam = request()->has('bed') ? ['bed' => request()->bed] : [];
@endphp

<div class="bg-white p-6 rounded-lg shadow-lg">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Health Education Resources</h2>
        <p class="text-gray-600">Access educational materials to improve your health knowledge</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach($resources as $resource)
            <a href="{{ route('health.resources', array_merge(['type' => $resource['type'], 'title' => Str::slug($resource['title'])], $bedParam)) }}" class="block">
                <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition duration-300 h-full">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-{{ $resource['icon'] }} text-2xl text-blue-500"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $resource['title'] }}</h3>
                            <p class="text-gray-600 mb-3">{{ $resource['description'] }}</p>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $resource['type'] === 'article' ? 'bg-blue-100 text-blue-800' : ($resource['type'] === 'video' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ ucfirst($resource['type']) }}
                                </span>
                                <span class="ml-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Learn More →
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Need More Resources?</h3>
        <p class="text-blue-600">Contact our health education team for personalized guidance and additional materials.</p>
        <button onclick="window.location.href='{{ route('health.contact', $bedParam) }}'" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
            Contact Health Team
        </button>
    </div>
</div> 