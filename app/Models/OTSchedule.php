<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTSchedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ot_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'surgeon_id',
        'anesthetist_id',
        'room_id',
        'schedule_date',
        'start_time',
        'end_time',
        'procedure_type',
        'status',
        'notes',
        'procedure_details',
        'anesthesia_details',
        'complications',
        'outcome',
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
     * Get the patient associated with this booking.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the surgeon associated with this booking.
     */
    public function surgeon()
    {
        return $this->belongsTo(Surgeon::class);
    }

    /**
     * Get the anesthetist associated with this booking.
     */
    public function anesthetist()
    {
        return $this->belongsTo(Anesthetist::class);
    }

    /**
     * Get the room associated with this booking.
     */
    public function room()
    {
        return $this->belongsTo(OTRoom::class);
    }
    
    /**
     * Scope to find bookings on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('schedule_date', $date);
    }
    
    /**
     * Scope to find bookings that are scheduled
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }
    
    /**
     * Scope to find bookings that are completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope to find bookings that are cancelled
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
} 