<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit OT Room') }}: {{ $room->room_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('ot-scheduling.update-room', $room) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Room Number and Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="room_number" :value="__('Room Number')" />
                                <x-text-input id="room_number" type="text" name="room_number" :value="old('room_number', $room->room_number)" required class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" type="text" name="name" :value="old('name', $room->name)" required class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Status and Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="cleaning" {{ old('status', $room->status) == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Room Type')" />
                                <select id="type" name="type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="general" {{ old('type', $room->type) == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="cardiac" {{ old('type', $room->type) == 'cardiac' ? 'selected' : '' }}>Cardiac</option>
                                    <option value="orthopedic" {{ old('type', $room->type) == 'orthopedic' ? 'selected' : '' }}>Orthopedic</option>
                                    <option value="neurosurgery" {{ old('type', $room->type) == 'neurosurgery' ? 'selected' : '' }}>Neurosurgery</option>
                                    <option value="ophthalmic" {{ old('type', $room->type) == 'ophthalmic' ? 'selected' : '' }}>Ophthalmic</option>
                                    <option value="ent" {{ old('type', $room->type) == 'ent' ? 'selected' : '' }}>ENT</option>
                                    <option value="other" {{ old('type', $room->type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Floor and Building -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="floor" :value="__('Floor')" />
                                <x-text-input id="floor" type="text" name="floor" :value="old('floor', $room->floor)" class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('floor')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="building" :value="__('Building')" />
                                <x-text-input id="building" type="text" name="building" :value="old('building', $room->building)" class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('building')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <x-input-label for="capacity" :value="__('Capacity')" />
                            <x-text-input id="capacity" type="number" min="1" name="capacity" :value="old('capacity', $room->capacity)" required class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <!-- Equipment -->
                        <div>
                            <x-input-label for="equipment" :value="__('Equipment')" />
                            <textarea id="equipment" name="equipment" rows="3" 
                                      class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('equipment', $room->equipment) }}</textarea>
                            <x-input-error :messages="$errors->get('equipment')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600">List the main equipment available in this OT room</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" 
                                      class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $room->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $room->is_active) ? 'checked' : '' }} 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 text-sm text-gray-600">{{ __('Room is active and can be booked') }}</label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('ot-scheduling.rooms') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Room') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 