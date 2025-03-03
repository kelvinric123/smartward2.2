<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_id',
        'patient_id',
        'from_ward_id',
        'from_bed_id',
        'to_ward_id',
        'to_bed_id',
        'transferred_by',
        'transfer_date',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
    ];

    /**
     * Get the patient associated with the transfer.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the admission associated with the transfer.
     */
    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    /**
     * Get the ward from which the patient was transferred.
     */
    public function fromWard()
    {
        return $this->belongsTo(Ward::class, 'from_ward_id');
    }

    /**
     * Get the bed from which the patient was transferred.
     */
    public function fromBed()
    {
        return $this->belongsTo(Bed::class, 'from_bed_id');
    }

    /**
     * Get the ward to which the patient was transferred.
     */
    public function toWard()
    {
        return $this->belongsTo(Ward::class, 'to_ward_id');
    }

    /**
     * Get the bed to which the patient was transferred.
     */
    public function toBed()
    {
        return $this->belongsTo(Bed::class, 'to_bed_id');
    }

    /**
     * Get the user who performed the transfer.
     */
    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
