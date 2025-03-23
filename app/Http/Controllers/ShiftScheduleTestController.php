<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Models\Ward;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftScheduleTestController extends Controller
{
    /**
     * Test dashboard method.
     */
    public function testDashboard()
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
        $coveragePercentage = count($wardCoverageData) > 0 ? array_sum($wardCoverageData) / count($wardCoverageData) : 0;
        
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
    
    /**
     * Simple test method that returns text.
     */
    public function simpleTest()
    {
        return 'ShiftScheduleTestController works!';
    }
}
