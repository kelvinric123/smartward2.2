<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('OT Room') }} {{ $room->room_number }} - {{ $room->name }}
            </h2>
            <a href="{{ route('ot-scheduling.display') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Room Status Banner -->
            <div class="mb-6">
                <div class="flex justify-between items-center bg-gray-800 text-white py-3 px-6 rounded-t-lg">
                    <div class="flex items-center">
                        <span class="text-xl font-bold">Room {{ $room->room_number }}</span>
                        <span class="ml-4 px-3 py-1 text-sm rounded-full {{ $room->getStatusClass() }} text-black">
                            {{ ucfirst($room->status) }}
                        </span>
                    </div>
                    <div class="flex space-x-4">
                        <span id="current-time" class="text-lg font-bold px-3 py-1 rounded"></span>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Room Details and Personnel -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Room Details -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-gray-700 text-white px-4 py-2 font-bold">
                            Room Details
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Room Name</p>
                                <p class="font-semibold">{{ $room->name }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Type</p>
                                <p class="font-semibold capitalize">{{ $room->type }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Location</p>
                                <p class="font-semibold">
                                    {{ $room->building ? $room->building . ', ' : '' }}
                                    {{ $room->floor ? 'Floor ' . $room->floor : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Equipment</p>
                                <p class="font-semibold">{{ $room->equipment ?: 'Standard OT Equipment' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($currentBooking)
                    <!-- Current Personnel -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-blue-600 text-white px-4 py-2 font-bold">
                            Medical Team
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Lead Surgeon</p>
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-md text-blue-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $currentBooking->surgeon->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $currentBooking->surgeon->specialization }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Anesthetist</p>
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-md text-green-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $currentBooking->anesthetist->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $currentBooking->anesthetist->specialization }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($nextBooking && !$currentBooking)
                    <!-- Next Scheduled Operation -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-yellow-500 text-white px-4 py-2 font-bold">
                            Next Scheduled Operation
                        </div>
                        <div class="p-4">
                            <div class="mb-3">
                                <p class="text-sm text-gray-600">Time</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($nextBooking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($nextBooking->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="text-sm text-gray-600">Patient</p>
                                <p class="font-semibold">{{ $nextBooking->patient->full_name }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-sm text-gray-600">Procedure</p>
                                <p class="font-semibold">{{ $nextBooking->procedure_type }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-sm text-gray-600">Surgeon</p>
                                <p class="font-semibold">{{ $nextBooking->surgeon->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Anesthetist</p>
                                <p class="font-semibold">{{ $nextBooking->anesthetist->name }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Center Column: Video Feed and Progress -->
                <div class="lg:col-span-2 space-y-6">
                    @if($currentBooking)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-red-600 text-white px-4 py-2 font-bold flex justify-between items-center">
                            <span>Live Operation Feed</span>
                            <span class="animate-pulse flex items-center">
                                <span class="h-3 w-3 rounded-full bg-white mr-2"></span>
                                LIVE
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg mb-4 relative overflow-hidden">
                                <!-- This would be a real video feed in production -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-video text-gray-500 text-6xl mb-4"></i>
                                        <p class="text-white text-xl">OT Room {{ $room->room_number }} Live Feed</p>
                                        <p class="text-gray-400 mt-2">Connected and streaming</p>
                                        
                                        <!-- Simulated video controls -->
                                        <div class="mt-4 flex justify-center space-x-4">
                                            <button class="bg-gray-700 hover:bg-gray-600 text-white rounded-full w-10 h-10 flex items-center justify-center">
                                                <i class="fas fa-expand-alt"></i>
                                            </button>
                                            <button class="bg-gray-700 hover:bg-gray-600 text-white rounded-full w-10 h-10 flex items-center justify-center">
                                                <i class="fas fa-volume-mute"></i>
                                            </button>
                                            <button class="bg-red-700 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center">
                                                <i class="fas fa-record-vinyl"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Video Overlay Elements -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                                    <div class="flex justify-between text-white">
                                        <div>
                                            <p class="font-bold">{{ $currentBooking->procedure_type }}</p>
                                            <p class="text-sm">Dr. {{ $currentBooking->surgeon->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p id="operation-duration" class="font-bold">00:45:22</p>
                                            <p class="text-sm">Duration</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Operation Details -->
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">Current Operation Details</h3>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Patient</p>
                                        <p class="font-semibold">{{ $currentBooking->patient->full_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Procedure</p>
                                        <p class="font-semibold">{{ $currentBooking->procedure_type }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Start Time</p>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($currentBooking->start_time)->format('H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Estimated End</p>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($currentBooking->end_time)->format('H:i') }}</p>
                                    </div>
                                </div>
                                
                                <h4 class="font-bold mb-2">Progress Status</h4>
                                
                                <!-- Interactive Progress Tracking -->
                                <div class="relative mb-6">
                                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200 mt-1">
                                        <div id="progress-bar" style="width: 60%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        @foreach($patientStatuses as $key => $status)
                                            <div class="text-center">
                                                <div class="w-6 h-6 rounded-full mx-auto mb-1 
                                                    @if($key == 'surgery_started') bg-blue-500 ring-4 ring-blue-200
                                                    @elseif(array_search($key, array_keys($patientStatuses)) < array_search('surgery_started', array_keys($patientStatuses))) bg-blue-500
                                                    @else bg-gray-300 @endif"></div>
                                                <span class="text-xs block">{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Patient Status Form - In a real app, this would submit via AJAX -->
                                <form id="status-form" class="mt-4">
                                    <div class="mb-4">
                                        <label for="patient_status" class="block text-sm font-medium text-gray-700 mb-1">Update Patient Status</label>
                                        <select id="patient_status" name="patient_status" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            @foreach($patientStatuses as $key => $status)
                                                <option value="{{ $key }}" @if($key == 'surgery_started') selected @endif>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="status_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea id="status_notes" name="status_notes" rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" id="update-status-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Update Status
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    @elseif($room->status == 'cleaning')
                    <!-- Room Cleaning Display -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-blue-500 text-white px-4 py-2 font-bold">
                            Room Cleaning In Progress
                        </div>
                        <div class="p-6 text-center">
                            <div class="mx-auto w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                                <i class="fas fa-broom text-blue-500 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">This room is currently being cleaned</h3>
                            <p class="text-gray-600 mb-6">Preparing for the next operation</p>
                            
                            <div class="flex justify-center">
                                <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                    Mark Cleaning Complete
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    @elseif($room->status == 'maintenance')
                    <!-- Room Maintenance Display -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-yellow-500 text-white px-4 py-2 font-bold">
                            Room Under Maintenance
                        </div>
                        <div class="p-6 text-center">
                            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-100 flex items-center justify-center mb-4">
                                <i class="fas fa-tools text-yellow-500 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">This room is currently under maintenance</h3>
                            <p class="text-gray-600">The room will be available once maintenance is completed</p>
                        </div>
                    </div>
                    
                    @else
                    <!-- Room Available or Reserved Display -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-green-500 text-white px-4 py-2 font-bold">
                            Room {{ $room->status == 'available' ? 'Available' : 'Reserved' }}
                        </div>
                        <div class="p-6 text-center">
                            <div class="mx-auto w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mb-4">
                                <i class="fas fa-door-open text-green-500 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">This room is currently {{ $room->status }}</h3>
                            
                            @if($nextBooking)
                                <p class="text-gray-600 mb-4">Next operation scheduled at {{ \Carbon\Carbon::parse($nextBooking->start_time)->format('H:i') }}</p>
                            @else
                                <p class="text-gray-600 mb-4">No operations scheduled for today</p>
                            @endif
                            
                            @if($room->status == 'available')
                                <div class="flex justify-center">
                                    <a href="{{ route('ot-scheduling.create-booking') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                        Schedule New Operation
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Vital Signs Monitor (only shown when operation is in progress) -->
                    @if($currentBooking && $room->status == 'occupied')
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="bg-gray-800 text-white px-4 py-2 font-bold">
                            Vital Signs Monitor
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Heart Rate -->
                                <div class="bg-red-50 p-3 rounded-lg text-center">
                                    <i class="fas fa-heartbeat text-red-500 text-2xl mb-1"></i>
                                    <p class="text-xs text-gray-500">Heart Rate</p>
                                    <p class="text-xl font-bold text-red-600">78</p>
                                    <p class="text-xs text-gray-500">bpm</p>
                                </div>
                                
                                <!-- Blood Pressure -->
                                <div class="bg-blue-50 p-3 rounded-lg text-center">
                                    <i class="fas fa-stethoscope text-blue-500 text-2xl mb-1"></i>
                                    <p class="text-xs text-gray-500">Blood Pressure</p>
                                    <p class="text-xl font-bold text-blue-600">120/80</p>
                                    <p class="text-xs text-gray-500">mmHg</p>
                                </div>
                                
                                <!-- Oxygen Saturation -->
                                <div class="bg-green-50 p-3 rounded-lg text-center">
                                    <i class="fas fa-lungs text-green-500 text-2xl mb-1"></i>
                                    <p class="text-xs text-gray-500">SpO2</p>
                                    <p class="text-xl font-bold text-green-600">98%</p>
                                    <p class="text-xs text-gray-500">Oxygen</p>
                                </div>
                                
                                <!-- Temperature -->
                                <div class="bg-yellow-50 p-3 rounded-lg text-center">
                                    <i class="fas fa-thermometer-half text-yellow-500 text-2xl mb-1"></i>
                                    <p class="text-xs text-gray-500">Temperature</p>
                                    <p class="text-xl font-bold text-yellow-600">36.8°</p>
                                    <p class="text-xs text-gray-500">Celsius</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: false 
            });
            document.getElementById('current-time').textContent = timeStr;
        }
        
        // Initial calls
        updateTime();
        
        // Update every second
        setInterval(updateTime, 1000);
        
        // Handle status update button
        const updateStatusBtn = document.getElementById('update-status-btn');
        if (updateStatusBtn) {
            updateStatusBtn.addEventListener('click', function() {
                const statusSelect = document.getElementById('patient_status');
                const selectedStatus = statusSelect.options[statusSelect.selectedIndex].text;
                
                // Show notification (in a real app, this would send an AJAX request)
                alert(`Patient status updated to: ${selectedStatus}`);
                
                // Update progress bar and status markers based on selection
                updateProgressDisplay(statusSelect.value);
            });
        }
        
        // Update the progress display based on selected status
        function updateProgressDisplay(status) {
            const statuses = @json(array_keys($patientStatuses));
            const currentIndex = statuses.indexOf(status);
            const progressPercentage = Math.round((currentIndex / (statuses.length - 1)) * 100);
            
            // Update progress bar
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                progressBar.style.width = `${progressPercentage}%`;
            }
            
            // Update status markers
            statuses.forEach((statusKey, index) => {
                const statusMarkers = document.querySelectorAll('.rounded-full');
                if (index <= currentIndex) {
                    statusMarkers[index].classList.remove('bg-gray-300');
                    statusMarkers[index].classList.add('bg-blue-500');
                } else {
                    statusMarkers[index].classList.remove('bg-blue-500', 'ring-4', 'ring-blue-200');
                    statusMarkers[index].classList.add('bg-gray-300');
                }
                
                // Add ring to current status
                if (index === currentIndex) {
                    statusMarkers[index].classList.add('ring-4', 'ring-blue-200');
                } else {
                    statusMarkers[index].classList.remove('ring-4', 'ring-blue-200');
                }
            });
        }
        
        // In a real app, this would be calculated based on actual start time
        let seconds = 2722; // 45 minutes and 22 seconds for demo
        function updateOperationTimer() {
            const operationDuration = document.getElementById('operation-duration');
            if (operationDuration) {
                seconds++;
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                
                operationDuration.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }
        
        // Update operation timer every second
        setInterval(updateOperationTimer, 1000);
        
        // Auto refresh the page every 60 seconds
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
    @endpush
</x-app-layout> 