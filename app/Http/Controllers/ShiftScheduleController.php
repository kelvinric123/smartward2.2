<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Models\Ward;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ShiftScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the shift schedules.
     */
    public function index(Request $request)
    {
        $wards = Ward::orderBy('name')->get();
        $nurses = Nurse::orderBy('name')->get();
        $shifts = Nurse::getShiftOptions();
        
        // Get selected ward if any
        $wardId = $request->input('ward_id');
        $selectedWard = null;
        if ($wardId) {
            $selectedWard = Ward::find($wardId);
        }
        
        // Get date range, default to today and next 2 weeks
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : Carbon::now()->startOfDay();
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now()->startOfDay()->addDays(14);
        
        // Prepare date range for display
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get schedules for the date range
        $query = ShiftSchedule::with(['nurse', 'ward'])
            ->whereBetween('schedule_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        // Filter by ward if selected
        if ($selectedWard) {
            $query->where('ward_id', $selectedWard->id);
        }
        
        $schedules = $query->orderBy('schedule_date')->get();
        
        // Group schedules by date for calendar view
        $calendarData = [];
        foreach ($dateRange as $date) {
            $daySchedules = $schedules->filter(function ($schedule) use ($date) {
                return $schedule->schedule_date->format('Y-m-d') === $date->format('Y-m-d');
            });
            
            $calendarData[$date->format('Y-m-d')] = $daySchedules;
        }
        
        return view('shift-schedule.index', compact(
            'wards', 
            'nurses', 
            'shifts', 
            'selectedWard', 
            'startDate', 
            'endDate', 
            'dateRange', 
            'calendarData'
        ));
    }

    /**
     * Show the form for creating a new shift schedule.
     */
    public function create()
    {
        $nurses = Nurse::orderBy('name')->get();
        $wards = Ward::orderBy('name')->get();
        $shifts = Nurse::getShiftOptions();
        $shiftTimes = Nurse::getShiftTimes();
        
        return view('shift-schedule.create', compact('nurses', 'wards', 'shifts', 'shiftTimes'));
    }

    /**
     * Store a newly created shift schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'ward_id' => 'required|exists:wards,id',
            'schedule_start_date' => 'required|date',
            'schedule_end_date' => 'required|date|after_or_equal:schedule_start_date',
            'shift' => 'required|string|in:' . implode(',', Nurse::getShiftOptions()),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);
        
        // Always set status to confirmed
        $validated['status'] = 'confirmed';
        
        // Use standard times for standard shifts if applicable
        if ($validated['shift'] !== 'Custom') {
            $standardTimes = Nurse::getShiftTimes($validated['shift']);
            
            // Only override if standard times are available and user hasn't specified custom times
            if ($standardTimes && 
                (empty($validated['start_time']) || empty($validated['end_time']))) {
                $validated['start_time'] = $standardTimes['start_time'];
                $validated['end_time'] = $standardTimes['end_time'];
            }
        }
        
        // Create date range
        $startDate = Carbon::parse($validated['schedule_start_date']);
        $endDate = Carbon::parse($validated['schedule_end_date']);
        
        // Create a shift schedule for each day in the range
        $currentDate = $startDate->copy();
        $schedulesCreated = 0;
        
        while ($currentDate->lte($endDate)) {
            ShiftSchedule::create([
                'nurse_id' => $validated['nurse_id'],
                'ward_id' => $validated['ward_id'],
                'schedule_date' => $currentDate->format('Y-m-d'),
                'shift' => $validated['shift'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);
            
            $schedulesCreated++;
            $currentDate->addDay();
        }
        
        return redirect()->route('shift-schedule.index')
            ->with('success', $schedulesCreated . ' shift schedule(s) created successfully');
    }

    /**
     * Display the specified shift schedule.
     */
    public function show(ShiftSchedule $shiftSchedule)
    {
        return view('shift-schedule.show', compact('shiftSchedule'));
    }

    /**
     * Show the form for editing the specified shift schedule.
     */
    public function edit(ShiftSchedule $shiftSchedule)
    {
        $nurses = Nurse::orderBy('name')->get();
        $wards = Ward::orderBy('name')->get();
        $shifts = Nurse::getShiftOptions();
        $shiftTimes = Nurse::getShiftTimes();
        
        return view('shift-schedule.edit', compact('shiftSchedule', 'nurses', 'wards', 'shifts', 'shiftTimes'));
    }

    /**
     * Update the specified shift schedule in storage.
     */
    public function update(Request $request, ShiftSchedule $shiftSchedule)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:nurses,id',
            'ward_id' => 'required|exists:wards,id',
            'schedule_date' => 'required|date',
            'shift' => 'required|string|in:' . implode(',', Nurse::getShiftOptions()),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);
        
        // Always set status to confirmed
        $validated['status'] = 'confirmed';
        
        // Use standard times for standard shifts if applicable
        if ($validated['shift'] !== 'Custom') {
            $standardTimes = Nurse::getShiftTimes($validated['shift']);
            
            // Only override if standard times are available and user hasn't specified custom times
            if ($standardTimes && 
                (empty($validated['start_time']) || empty($validated['end_time']))) {
                $validated['start_time'] = $standardTimes['start_time'];
                $validated['end_time'] = $standardTimes['end_time'];
            }
        }
        
        $shiftSchedule->update($validated);
        
        return redirect()->route('shift-schedule.index')
            ->with('success', 'Shift schedule updated successfully');
    }

    /**
     * Remove the specified shift schedule from storage.
     */
    public function destroy(ShiftSchedule $shiftSchedule)
    {
        $shiftSchedule->delete();
        
        return redirect()->route('shift-schedule.index')
            ->with('success', 'Shift schedule deleted successfully');
    }
    
    /**
     * Display shift schedules for a specific ward.
     */
    public function wardSchedule(Ward $ward, Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : Carbon::now()->startOfDay();
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now()->startOfDay()->addDays(14);
        
        // Prepare date range for display
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get schedules for the ward and date range
        $schedules = ShiftSchedule::with('nurse')
            ->where('ward_id', $ward->id)
            ->whereBetween('schedule_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get();
        
        // Group schedules by date and shift for easier display
        $groupedSchedules = [];
        foreach ($dateRange as $date) {
            $daySchedules = $schedules->filter(function ($schedule) use ($date) {
                return $schedule->schedule_date->format('Y-m-d') === $date->format('Y-m-d');
            });
            
            $groupedSchedules[$date->format('Y-m-d')] = [
                'Morning' => $daySchedules->filter(function ($schedule) {
                    return $schedule->shift === 'Morning';
                }),
                'Evening' => $daySchedules->filter(function ($schedule) {
                    return $schedule->shift === 'Evening';
                }),
                'Night' => $daySchedules->filter(function ($schedule) {
                    return $schedule->shift === 'Night';
                }),
                'Custom' => $daySchedules->filter(function ($schedule) {
                    return $schedule->shift === 'Custom';
                }),
            ];
        }
        
        return view('shift-schedule.ward', compact(
            'ward', 
            'startDate', 
            'endDate', 
            'dateRange', 
            'groupedSchedules'
        ));
    }
    
    /**
     * Display shift schedules for a specific nurse.
     */
    public function nurseSchedule(Nurse $nurse, Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : Carbon::now()->startOfDay();
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now()->startOfDay()->addDays(14);
        
        // Prepare date range for display
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get schedules for the nurse and date range
        $schedules = ShiftSchedule::with('ward')
            ->where('nurse_id', $nurse->id)
            ->whereBetween('schedule_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get();
        
        // Group schedules by date for calendar view
        $schedulesByDate = [];
        foreach ($dateRange as $date) {
            $daySchedules = $schedules->filter(function ($schedule) use ($date) {
                return $schedule->schedule_date->format('Y-m-d') === $date->format('Y-m-d');
            });
            
            $schedulesByDate[$date->format('Y-m-d')] = $daySchedules;
        }
        
        return view('shift-schedule.nurse', compact(
            'nurse', 
            'startDate', 
            'endDate', 
            'dateRange', 
            'schedulesByDate'
        ));
    }

    /**
     * Display a dashboard of shift scheduling information.
     */
    public function dashboard()
    {
        // Get wards and nurses
        $wards = Ward::orderBy('name')->get();
        $nurses = Nurse::orderBy('name')->get();
        
        // Create a map of ward names to IDs for easy lookup
        $wardsByName = [];
        foreach ($wards as $ward) {
            $wardsByName[$ward->name] = $ward->id;
        }
        
        // Get upcoming shifts (next 7 days)
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(6);
        
        $upcomingShifts = ShiftSchedule::with(['nurse', 'ward'])
            ->whereBetween('schedule_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        
        // Get today's schedules
        $todaySchedules = ShiftSchedule::with(['nurse', 'ward'])
            ->whereDate('schedule_date', Carbon::today())
            ->orderBy('start_time')
            ->get();
        
        // Calculate statistics
        $totalShifts = $upcomingShifts->count();
        $todayShifts = $todaySchedules->count();
        
        // Calculate ward coverage data
        $wardCoverageData = [];
        $requiredShiftsPerDay = 3; // Morning, Evening, Night
        $dateRange = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }
        
        $totalDays = count($dateRange);
        $totalRequiredShiftsPerWard = $totalDays * $requiredShiftsPerDay;
        
        foreach ($wards as $ward) {
            $wardShifts = $upcomingShifts->filter(function ($schedule) use ($ward) {
                return $schedule->ward_id === $ward->id;
            });
            
            // Count shifts per day
            $shiftCounts = [];
            foreach ($dateRange as $date) {
                $dateShifts = $wardShifts->filter(function ($schedule) use ($date) {
                    return $schedule->schedule_date->format('Y-m-d') === $date;
                });
                
                // Count morning, evening, night shifts
                $morning = $dateShifts->filter(function ($schedule) {
                    return $schedule->shift === 'Morning';
                })->count() > 0 ? 1 : 0;
                
                $evening = $dateShifts->filter(function ($schedule) {
                    return $schedule->shift === 'Evening';
                })->count() > 0 ? 1 : 0;
                
                $night = $dateShifts->filter(function ($schedule) {
                    return $schedule->shift === 'Night';
                })->count() > 0 ? 1 : 0;
                
                $shiftCounts[$date] = $morning + $evening + $night;
            }
            
            // Calculate coverage percentage
            $totalCoveredShifts = array_sum($shiftCounts);
            $coveragePercentage = ($totalCoveredShifts / $totalRequiredShiftsPerWard) * 100;
            
            $wardCoverageData[$ward->id] = $coveragePercentage;
        }
        
        // Calculate overall coverage
        $coveragePercentage = array_sum($wardCoverageData) / count($wardCoverageData);
        
        // Calculate unassigned slots (shift slots that don't have a nurse scheduled)
        $unassignedSlots = $totalRequiredShiftsPerWard * $wards->count() - $totalShifts;
        $unassignedSlots = max(0, $unassignedSlots); // Ensure it's not negative
        
        return view('shift-schedule.dashboard', compact(
            'wards',
            'nurses',
            'wardsByName',
            'totalShifts',
            'todayShifts',
            'unassignedSlots',
            'coveragePercentage',
            'wardCoverageData',
            'todaySchedules'
        ));
    }
}
