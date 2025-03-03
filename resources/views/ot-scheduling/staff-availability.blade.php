<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Surgeon & Anesthetist Availability') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Date Selector -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium mb-2">Select Date</h3>
                        <form action="{{ route('ot-scheduling.staff-availability') }}" method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <input type="date" id="date_filter" name="date" value="{{ request('date', now()->format('Y-m-d')) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Check Availability
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Surgeons Availability -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Surgeons Availability</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Name</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Specialization</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Availability</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Scheduled Operations</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($surgeons as $surgeon)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 text-sm font-medium">{{ $surgeon->name }}</td>
                                            <td class="py-3 px-4 text-sm">{{ $surgeon->specialization }}</td>
                                            <td class="py-3 px-4 text-sm">
                                                @if($surgeon->availability->where('date', request('date', now()->format('Y-m-d')))->count() > 0)
                                                    @foreach($surgeon->availability->where('date', request('date', now()->format('Y-m-d'))) as $slot)
                                                        <div class="mb-1 flex items-center">
                                                            <div class="w-3 h-3 rounded-full {{ $slot->is_available ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                                                            <span>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                            @if(!$slot->is_available)
                                                                <span class="ml-2 text-xs text-gray-500">({{ $slot->reason }})</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-yellow-600">No availability data for this date</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                @php
                                                    $scheduledOps = \App\Models\OTSchedule::where('surgeon_id', $surgeon->id)
                                                        ->whereDate('schedule_date', request('date', now()->format('Y-m-d')))
                                                        ->orderBy('start_time')
                                                        ->get();
                                                @endphp
                                                
                                                @if($scheduledOps->count() > 0)
                                                    <ul class="list-disc list-inside">
                                                        @foreach($scheduledOps as $op)
                                                            <li>
                                                                {{ \Carbon\Carbon::parse($op->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($op->end_time)->format('H:i') }}
                                                                <span class="text-xs text-gray-600">({{ $op->procedure_type }})</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-500">No operations scheduled</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 px-4 text-center text-gray-500 italic">No surgeons found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Anesthetists Availability -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Anesthetists Availability</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Name</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Specialization</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Availability</th>
                                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Scheduled Operations</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($anesthetists as $anesthetist)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 text-sm font-medium">{{ $anesthetist->name }}</td>
                                            <td class="py-3 px-4 text-sm">{{ $anesthetist->specialization }}</td>
                                            <td class="py-3 px-4 text-sm">
                                                @if($anesthetist->availability->where('date', request('date', now()->format('Y-m-d')))->count() > 0)
                                                    @foreach($anesthetist->availability->where('date', request('date', now()->format('Y-m-d'))) as $slot)
                                                        <div class="mb-1 flex items-center">
                                                            <div class="w-3 h-3 rounded-full {{ $slot->is_available ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                                                            <span>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                            @if(!$slot->is_available)
                                                                <span class="ml-2 text-xs text-gray-500">({{ $slot->reason }})</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-yellow-600">No availability data for this date</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                @php
                                                    $scheduledOps = \App\Models\OTSchedule::where('anesthetist_id', $anesthetist->id)
                                                        ->whereDate('schedule_date', request('date', now()->format('Y-m-d')))
                                                        ->orderBy('start_time')
                                                        ->get();
                                                @endphp
                                                
                                                @if($scheduledOps->count() > 0)
                                                    <ul class="list-disc list-inside">
                                                        @foreach($scheduledOps as $op)
                                                            <li>
                                                                {{ \Carbon\Carbon::parse($op->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($op->end_time)->format('H:i') }}
                                                                <span class="text-xs text-gray-600">({{ $op->procedure_type }})</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-500">No operations scheduled</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 px-4 text-center text-gray-500 italic">No anesthetists found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 