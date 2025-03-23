<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'floor',
        'capacity',
        'type',
        'status',
    ];

    protected $casts = [
        'floor' => 'integer',
        'capacity' => 'integer',
    ];

    // Relationships
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function medicalDevices()
    {
        return $this->hasMany(MedicalDevice::class);
    }

    // Helper methods
    public function getOccupiedBedsCount()
    {
        return $this->beds()->whereHas('admissions', function ($query) {
            $query->where('status', 'active');
        })->count();
    }

    public function getAvailableBedsCount()
    {
        return $this->beds()->where('status', 'available')->count();
    }

    public function getOccupancyRate()
    {
        if ($this->capacity == 0) {
            return 0;
        }
        
        return ($this->getOccupiedBedsCount() / $this->capacity) * 100;
    }

    /**
     * Get the shift schedules for this ward.
     */
    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
} 