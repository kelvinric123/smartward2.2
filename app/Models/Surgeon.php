<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgeon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'employee_id',
        'specialization',
        'contact_number',
        'email',
        'status',
    ];

    /**
     * Get all OT schedules associated with this surgeon.
     */
    public function schedules()
    {
        return $this->hasMany(OTSchedule::class);
    }

    /**
     * Get all availability slots for this surgeon.
     */
    public function availability()
    {
        return $this->hasMany(StaffAvailability::class, 'staff_id')
            ->where('staff_type', 'surgeon');
    }

    /**
     * Scope to find surgeons who are active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to find surgeons who are available
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if surgeon is available on a specific date and time
     */
    public function isAvailableAt($date, $startTime, $endTime)
    {
        // Check if surgeon has any conflicting schedules
        $conflictingSchedules = $this->schedules()
            ->whereDate('schedule_date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })
            ->where('status', '!=', 'cancelled')
            ->count();

        // Check if surgeon has availability for this time slot
        $hasAvailability = $this->availability()
            ->whereDate('date', $date)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->count() > 0;

        return $conflictingSchedules === 0 && $hasAvailability;
    }
} 