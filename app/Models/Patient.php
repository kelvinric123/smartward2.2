<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'mrn',
        'contact_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_number',
        'medical_history',
        'allergies',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the patient's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get all admissions for the patient.
     */
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Get active admission for the patient.
     */
    public function activeAdmission()
    {
        return $this->admissions()->where('status', 'active')->first();
    }

    /**
     * Check if patient is currently admitted.
     */
    public function getIsAdmittedAttribute()
    {
        return $this->admissions()->where('status', 'active')->exists();
    }

    /**
     * Get the current bed for the patient if admitted.
     */
    public function getCurrentBedAttribute()
    {
        $activeAdmission = $this->activeAdmission();
        return $activeAdmission ? $activeAdmission->bed : null;
    }

    /**
     * Get the consultants associated with this patient.
     */
    public function consultants()
    {
        return $this->belongsToMany(Consultant::class, 'consultant_patient')
            ->withPivot('relationship_type', 'assignment_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the nurses associated with this patient.
     */
    public function nurses()
    {
        return $this->belongsToMany(Nurse::class, 'nurse_patient')
            ->withPivot('care_type', 'assignment_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Get all vital signs for the patient.
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }
    
    /**
     * Get the most recent vital signs for the patient.
     */
    public function latestVitalSigns()
    {
        return $this->hasOne(VitalSign::class)->latest();
    }

    /**
     * Get the transfer history for this patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transferHistory()
    {
        return $this->hasMany(TransferHistory::class);
    }

    /**
     * Scope a query to search for patients.
     */
    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('mrn', 'LIKE', "%{$searchTerm}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        return $query;
    }
} 