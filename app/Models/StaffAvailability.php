<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAvailability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'staff_type',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    /**
     * Get the staff member (surgeon or anesthetist) that this availability belongs to.
     */
    public function staff()
    {
        if ($this->staff_type === 'surgeon') {
            return $this->belongsTo(Surgeon::class, 'staff_id');
        } else if ($this->staff_type === 'anesthetist') {
            return $this->belongsTo(Anesthetist::class, 'staff_id');
        }
        
        return null;
    }

    /**
     * Scope to find availabilities for a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to find availabilities where staff is available
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to find availabilities for surgeons
     */
    public function scopeSurgeons($query)
    {
        return $query->where('staff_type', 'surgeon');
    }

    /**
     * Scope to find availabilities for anesthetists
     */
    public function scopeAnesthetists($query)
    {
        return $query->where('staff_type', 'anesthetist');
    }
} 