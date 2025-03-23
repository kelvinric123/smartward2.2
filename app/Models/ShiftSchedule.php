<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nurse_id',
        'ward_id',
        'schedule_date',
        'shift',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    /**
     * Get the nurse associated with this shift schedule.
     */
    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
    
    /**
     * Get the ward associated with this shift schedule.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
    
    /**
     * Scope a query to only include shifts for a specific ward.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $wardId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForWard($query, $wardId)
    {
        return $query->where('ward_id', $wardId);
    }
    
    /**
     * Scope a query to only include shifts for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('schedule_date', $date);
    }

    /**
     * Get a formatted display label for the shift with its standard time.
     *
     * @return string
     */
    public function getShiftLabel()
    {
        $standardTimes = Nurse::getShiftTimes($this->shift);
        
        if ($this->shift === 'Custom') {
            return "{$this->shift} Shift ({$this->start_time->format('g:i A')} - {$this->end_time->format('g:i A')})";
        }
        
        if ($standardTimes) {
            return "{$this->shift} Shift ({$this->formatTime($standardTimes['start_time'])} - {$this->formatTime($standardTimes['end_time'])})";
        }
        
        return "{$this->shift} Shift";
    }

    /**
     * Format a time string to 12-hour format with AM/PM.
     *
     * @param string $time Time in 24-hour format (HH:MM)
     * @return string Time in 12-hour format with AM/PM
     */
    protected function formatTime($time)
    {
        return date('g:i A', strtotime($time));
    }
}
