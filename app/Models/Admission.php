<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'bed_id',
        'ward_id',
        'admission_date',
        'expected_discharge_date',
        'actual_discharge_date',
        'status',
        'consultant_id',
        'diagnosis',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'expected_discharge_date' => 'datetime',
        'actual_discharge_date' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function admittingDoctor()
    {
        return $this->belongsTo(User::class, 'admitting_doctor_id');
    }

    /**
     * Get the consultant associated with this admission.
     */
    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }

    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    public function transferHistory()
    {
        return $this->hasMany(TransferHistory::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isDischarged()
    {
        return $this->status === 'discharged';
    }

    public function discharge($notes = null)
    {
        $this->update([
            'status' => 'discharged',
            'actual_discharge_date' => now(),
            'notes' => $notes ? $this->notes . "\n\nDischarge notes: " . $notes : $this->notes,
        ]);

        // Update the bed status
        if ($this->bed) {
            $this->bed->update(['status' => 'cleaning']);
        }

        return $this;
    }

    public function transfer(Bed $newBed, $notes = null)
    {
        if ($newBed->status !== 'available') {
            throw new \Exception('The target bed is not available for transfer');
        }

        $oldBed = $this->bed;
        $oldWard = $this->ward;
        $transferNote = "Transferred from Bed #{$oldBed->bed_number} in Ward {$oldWard->name} to Bed #{$newBed->bed_number} in Ward {$newBed->ward->name} on " . now()->format('Y-m-d H:i');

        // Record transfer history
        TransferHistory::create([
            'admission_id' => $this->id,
            'patient_id' => $this->patient_id,
            'from_ward_id' => $oldWard->id,
            'from_bed_id' => $oldBed->id,
            'to_ward_id' => $newBed->ward_id,
            'to_bed_id' => $newBed->id,
            'transferred_by' => auth()->id(),
            'transfer_date' => now(),
            'notes' => $notes,
        ]);

        // Update admission
        $this->update([
            'bed_id' => $newBed->id,
            'ward_id' => $newBed->ward_id,
            'notes' => $this->notes . "\n\n" . $transferNote . ($notes ? "\nTransfer reason: " . $notes : ''),
        ]);

        // Update beds status
        $oldBed->update(['status' => 'cleaning']);
        $newBed->update(['status' => 'occupied']);

        return $this;
    }

    public function getLengthOfStay()
    {
        $end = $this->actual_discharge_date ?? now();
        return $this->admission_date->diffInDays($end);
    }

    /**
     * Check if a patient already has an active admission.
     * 
     * @param int $patientId
     * @param int|null $excludeAdmissionId Admission ID to exclude from the check (useful for updates)
     * @return bool
     */
    public static function patientHasActiveAdmission($patientId, $excludeAdmissionId = null)
    {
        $query = self::where('patient_id', $patientId)
                    ->where('status', 'active');
                    
        if ($excludeAdmissionId) {
            $query->where('id', '!=', $excludeAdmissionId);
        }
        
        return $query->exists();
    }
} 