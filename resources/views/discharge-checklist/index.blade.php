<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Discharge Checklists') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            All Discharge Checklists
                        </h3>
                    </div>

                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Patient
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Completion
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Planned Discharge
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Created At
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Completed At
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($checklists as $checklist)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $checklist->patient->full_name }}
                                                                </div>
                                                                <div class="text-sm text-gray-500">
                                                                    MRN: {{ $checklist->patient->mrn }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $checklist->status === 'completed' ? 'bg-green-100 text-green-800' : ($checklist->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $checklist->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $checklist->completion_percentage }}%"></div>
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $checklist->completion_percentage }}%
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($checklist->planned_discharge_date)
                                                            <div class="text-sm text-gray-900">
                                                                {{ $checklist->planned_discharge_date->format('M d, Y') }}
                                                            </div>
                                                            <div class="text-xs {{ $checklist->planned_discharge_date->isPast() ? 'text-red-600' : 'text-blue-600' }} font-semibold">
                                                                {{ $checklist->days_until_discharge }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $checklist->total_admitted_days }} days admitted
                                                            </div>
                                                        @else
                                                            <span class="text-xs text-gray-500">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $checklist->created_at->format('M d, Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $checklist->completed_at ? $checklist->completed_at->format('M d, Y H:i') : 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('discharge-checklist.show', $checklist) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                        @if($checklist->status === 'in_progress')
                                                            <a href="{{ route('discharge-checklist.edit', $checklist) }}" class="ml-3 text-green-600 hover:text-green-900">Continue</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        No discharge checklists found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $checklists->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 