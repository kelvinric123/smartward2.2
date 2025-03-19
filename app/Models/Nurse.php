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
} 