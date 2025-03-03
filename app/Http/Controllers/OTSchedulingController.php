<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OTSchedule;
use App\Models\Surgeon;
use App\Models\Anesthetist;
use App\Models\Patient;
use App\Models\OTRoom;

class OTSchedulingController extends Controller
{
    /**
     * Display the OT scheduling dashboard
     */
    public function dashboard()
    {
        return view('ot-scheduling.dashboard');
    }
    
    /**
     * Display the booking interface
     */
    public function bookings(Request $request)
    {
        // Start with a base query
        $query = OTSchedule::with(['patient', 'surgeon', 'anesthetist']);
        
        // Apply date filter if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('schedule_date', $request->date);
        }
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Get bookings with ordering
        $bookings = $query->orderBy('schedule_date')
                         ->orderBy('start_time')
                         ->get();
        
        return view('ot-scheduling.bookings', compact('bookings'));
    }
    
    /**
     * Show form to create a new booking
     */
    public function createBooking()
    {
        $patients = Patient::all();
        $surgeons = Surgeon::available()->get();
        $anesthetists = Anesthetist::available()->get();
        $rooms = OTRoom::where('is_active', true)->orderBy('room_number')->get();
        
        return view('ot-scheduling.create-booking', compact('patients', 'surgeons', 'anesthetists', 'rooms'));
    }
    
    /**
     * Store a new booking
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'surgeon_id' => 'required|exists:surgeons,id',
            'anesthetist_id' => 'required|exists:anesthetists,id',
            'room_id' => 'required|exists:ot_rooms,id',
            'schedule_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'procedure_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,in-progress,completed,cancelled',
        ]);
        
        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'scheduled';
        }
        
        // Create the OT schedule
        $schedule = OTSchedule::create($validated);
        
        // If a room is assigned, update its status to reserved
        if ($schedule->room) {
            $schedule->room->status = 'reserved';
            $schedule->room->save();
        }
        
        return redirect()->route('ot-scheduling.bookings')
            ->with('success', 'Operation Theater booking created successfully.');
    }
    
    /**
     * Show a specific booking
     */
    public function showBooking(OTSchedule $booking)
    {
        return view('ot-scheduling.show-booking', compact('booking'));
    }
    
    /**
     * Edit a booking
     */
    public function editBooking(OTSchedule $booking)
    {
        $patients = Patient::all();
        $surgeons = Surgeon::available()->get();
        $anesthetists = Anesthetist::available()->get();
        $rooms = OTRoom::where('is_active', true)->orderBy('room_number')->get();
        
        return view('ot-scheduling.edit-booking', compact('booking', 'patients', 'surgeons', 'anesthetists', 'rooms'));
    }
    
    /**
     * Update a booking
     */
    public function updateBooking(Request $request, OTSchedule $booking)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'surgeon_id' => 'required|exists:surgeons,id',
            'anesthetist_id' => 'required|exists:anesthetists,id',
            'room_id' => 'required|exists:ot_rooms,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'procedure_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Check if room is being changed
        $oldRoomId = $booking->room_id;
        $newRoomId = $validated['room_id'];
        
        // Update the booking
        $booking->update($validated);
        
        // Handle room status changes if room has changed
        if ($oldRoomId != $newRoomId) {
            // Set the old room to available if it exists
            if ($oldRoomId) {
                $oldRoom = OTRoom::find($oldRoomId);
                if ($oldRoom) {
                    $oldRoom->status = 'available';
                    $oldRoom->save();
                }
            }
            
            // Set the new room to reserved if it exists
            if ($booking->room) {
                $booking->room->status = 'reserved';
                $booking->room->save();
            }
        }
        
        return redirect()->route('ot-scheduling.bookings')
            ->with('success', 'Operation Theater booking updated successfully.');
    }
    
    /**
     * Delete a booking
     */
    public function destroyBooking(OTSchedule $booking)
    {
        $booking->delete();
        
        return redirect()->route('ot-scheduling.bookings')
            ->with('success', 'Operation Theater booking deleted successfully.');
    }
    
    /**
     * Show staff availability calendar
     */
    public function staffAvailability()
    {
        $surgeons = Surgeon::with('availability')->get();
        $anesthetists = Anesthetist::with('availability')->get();
        
        return view('ot-scheduling.staff-availability', compact('surgeons', 'anesthetists'));
    }

    /**
     * Display OT rooms
     */
    public function rooms(Request $request)
    {
        // Query based on filters
        $query = OTRoom::query();
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Apply type filter if provided
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Get rooms
        $rooms = $query->orderBy('room_number')->get();
        
        return view('ot-scheduling.rooms', compact('rooms'));
    }
    
    /**
     * Show form to create a new OT room
     */
    public function createRoom()
    {
        return view('ot-scheduling.create-room');
    }
    
    /**
     * Store a new OT room
     */
    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:ot_rooms,room_number',
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,occupied,cleaning,maintenance,reserved',
            'floor' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'type' => 'required|in:general,cardiac,orthopedic,neurosurgery,ophthalmic,ent,other',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        OTRoom::create($validated);
        
        return redirect()->route('ot-scheduling.rooms')
            ->with('success', 'Operation Theater room created successfully.');
    }
    
    /**
     * Show a specific OT room
     */
    public function showRoom(OTRoom $room)
    {
        // Get current and upcoming schedules for this room
        $schedules = OTSchedule::where('room_id', $room->id)
            ->whereDate('schedule_date', '>=', now())
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get();
            
        return view('ot-scheduling.show-room', compact('room', 'schedules'));
    }
    
    /**
     * Edit an OT room
     */
    public function editRoom(OTRoom $room)
    {
        return view('ot-scheduling.edit-room', compact('room'));
    }
    
    /**
     * Update an OT room
     */
    public function updateRoom(Request $request, OTRoom $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:ot_rooms,room_number,' . $room->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,occupied,cleaning,maintenance,reserved',
            'floor' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'type' => 'required|in:general,cardiac,orthopedic,neurosurgery,ophthalmic,ent,other',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $room->update($validated);
        
        return redirect()->route('ot-scheduling.rooms')
            ->with('success', 'Operation Theater room updated successfully.');
    }
    
    /**
     * Delete an OT room
     */
    public function destroyRoom(OTRoom $room)
    {
        // Check if the room is being used in any schedules
        $hasSchedules = OTSchedule::where('room_id', $room->id)->exists();
        
        if ($hasSchedules) {
            return redirect()->route('ot-scheduling.rooms')
                ->with('error', 'Operation Theater room cannot be deleted because it is being used in schedules.');
        }
        
        $room->delete();
        
        return redirect()->route('ot-scheduling.rooms')
            ->with('success', 'Operation Theater room deleted successfully.');
    }

    /**
     * Update the status of an OT booking
     */
    public function updateBookingStatus(Request $request, OTSchedule $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
        ]);
        
        // Update the booking status
        $booking->status = $validated['status'];
        $booking->save();
        
        // If the status is in-progress or completed, we might want to update the room status
        if ($booking->room) {
            if ($validated['status'] == 'in-progress') {
                $booking->room->status = 'occupied';
                $booking->room->save();
            } elseif ($validated['status'] == 'completed') {
                $booking->room->status = 'cleaning';
                $booking->room->save();
            }
        }
        
        return redirect()->route('ot-scheduling.show-booking', $booking)
            ->with('success', 'Operation status updated successfully to ' . ucfirst($validated['status']));
    }
    
    /**
     * Update operation details for a booking
     */
    public function updateOperationDetails(Request $request, OTSchedule $booking)
    {
        $validated = $request->validate([
            'procedure_details' => 'nullable|string',
            'anesthesia_details' => 'nullable|string',
            'complications' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);
        
        // Check if we need to add these fields to the OT Schedule model's fillable property
        $booking->procedure_details = $validated['procedure_details'];
        $booking->anesthesia_details = $validated['anesthesia_details'];
        $booking->complications = $validated['complications'];
        $booking->outcome = $validated['outcome'];
        $booking->save();
        
        return redirect()->route('ot-scheduling.show-booking', $booking)
            ->with('success', 'Operation details updated successfully.');
    }

    /**
     * Display the OT rooms status dashboard for monitoring
     */
    public function otDisplay()
    {
        // Get all active rooms with their current bookings
        $rooms = OTRoom::where('is_active', true)
            ->orderBy('room_number')
            ->get();
            
        // Get all in-progress bookings
        $activeBookings = OTSchedule::with(['patient', 'surgeon', 'anesthetist', 'room'])
            ->where('status', 'in-progress')
            ->whereDate('schedule_date', today())
            ->get();
            
        // Get upcoming bookings for today
        $upcomingBookings = OTSchedule::with(['patient', 'surgeon', 'anesthetist', 'room'])
            ->where('status', 'scheduled')
            ->whereDate('schedule_date', today())
            ->orderBy('start_time')
            ->get();
            
        return view('ot-scheduling.display', compact('rooms', 'activeBookings', 'upcomingBookings'));
    }
    
    /**
     * Display a specific OT room's status and details
     */
    public function otDisplayRoom(OTRoom $room)
    {
        // Get the current booking for this room if any
        $currentBooking = OTSchedule::with(['patient', 'surgeon', 'anesthetist'])
            ->where('room_id', $room->id)
            ->where('status', 'in-progress')
            ->whereDate('schedule_date', today())
            ->first();
            
        // Get next booking for this room
        $nextBooking = OTSchedule::with(['patient', 'surgeon', 'anesthetist'])
            ->where('room_id', $room->id)
            ->where('status', 'scheduled')
            ->whereDate('schedule_date', today())
            ->orderBy('start_time')
            ->first();
            
        // Get patient statuses/progress options
        $patientStatuses = [
            'pre_op' => 'Pre-Operation Preparation',
            'anesthesia' => 'Anesthesia Administration',
            'surgery_started' => 'Surgery In Progress',
            'closing' => 'Closing',
            'recovery' => 'Recovery',
            'completed' => 'Operation Completed'
        ];
            
        return view('ot-scheduling.display-room', compact('room', 'currentBooking', 'nextBooking', 'patientStatuses'));
    }
} 