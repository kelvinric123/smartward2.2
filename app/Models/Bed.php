<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bed extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bed_number',
        'ward_id',
        'status',
        'type',
        'notes',
    ];

    // Relationships
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Get the relationship for the current active admission.
     * Used for eager loading with 'with()' and 'load()'.
     */
    public function currentAdmissionRelation()
    {
        return $this->hasOne(Admission::class)->where('status', 'active');
    }

    /**
     * Get the current active admission for this bed.
     */
    public function currentAdmission()
    {
        return $this->admissions()->where('status', 'active')->first();
    }

    /**
     * Get the current patient in this bed.
     */
    public function currentPatient()
    {
        $admission = $this->currentAdmission();
        return $admission ? $admission->patient : null;
    }

    // Status checks
    public function isOccupied()
    {
        return $this->status === 'occupied';
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isUnderMaintenance()
    {
        return $this->status === 'maintenance';
    }

    // Methods
    public function occupy(Patient $patient, User $admittingDoctor = null, $diagnosis = null)
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Bed is not available for occupation');
        }

        $admission = new Admission([
            'patient_id' => $patient->id,
            'ward_id' => $this->ward_id,
            'admission_date' => now(),
            'status' => 'active',
            'admitting_doctor_id' => $admittingDoctor ? $admittingDoctor->id : null,
            'diagnosis' => $diagnosis,
        ]);

        $this->admissions()->save($admission);
        $this->update(['status' => 'occupied']);

        return $admission;
    }

    public function vacate()
    {
        $admission = $this->currentAdmission();
        
        if ($admission) {
            $admission->update([
                'status' => 'discharged',
                'actual_discharge_date' => now(),
            ]);
        }

        $this->update(['status' => 'available']);
    }
} 