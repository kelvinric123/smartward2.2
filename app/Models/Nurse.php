<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'ward_assignment',
        'shift',
        'roster',
        'shift_preferences',
        'last_roster_update',
        'roster_notes',
        'status',
        'contact_number',
        'email',
        'employment_date',
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'roster' => 'array',
        'shift_preferences' => 'array',
        'last_roster_update' => 'datetime',
    ];
    
    /**
     * Get the possible shift options for a nurse.
     *
     * @return array
     */
    public static function getShiftOptions()
    {
        return [
            'Morning',
            'Evening',
            'Night',
            'Custom',
        ];
    }
    
    /**
     * Get the standard times for each shift type.
     *
     * @param string $shift
     * @return array with start_time and end_time keys
     */
    public static function getShiftTimes($shift = null)
    {
        $times = [
            'Morning' => [
                'start_time' => '07:00',
                'end_time' => '15:00'
            ],
            'Evening' => [
                'start_time' => '15:00',
                'end_time' => '23:00'
            ],
            'Night' => [
                'start_time' => '23:00',
                'end_time' => '07:00'
            ],
            'Custom' => [
                'start_time' => '',
                'end_time' => ''
            ]
        ];
        
        if ($shift && isset($times[$shift])) {
            return $times[$shift];
        }
        
        return $times;
    }
    
    /**
     * Get the possible status options for a nurse.
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            'On Duty',
            'Off Duty',
            'Break',
            'On Leave',
            'Sick',
        ];
    }
    
    /**
     * Get the ward that this nurse is assigned to.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_assignment', 'name');
    }
    
    /**
     * Get the patients that this nurse is caring for.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'nurse_patient')
            ->withTimestamps();
    }
    
    /**
     * Get the shift schedules for this nurse.
     */
    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
} 