<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DischargeChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_id',
        'patient_id',
        'blood_test_results',
        'blood_test_results_notes',
        'iv_medication',
        'iv_medication_notes',
        'imaging',
        'imaging_notes',
        'procedures',
        'procedures_notes',
        'referral',
        'referral_notes',
        'documentation',
        'documentation_notes',
        'additional_notes',
        'completed_by',
        'completed_at',
        'status',
        'planned_discharge_date',
    ];

    protected $casts = [
        'blood_test_results' => 'boolean',
        'iv_medication' => 'boolean',
        'imaging' => 'boolean',
        'procedures' => 'boolean',
        'referral' => 'boolean',
        'documentation' => 'boolean',
        'completed_at' => 'datetime',
        'planned_discharge_date' => 'date',
    ];

    /**
     * Get the admission this checklist belongs to.
     */
    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    /**
     * Get the patient this checklist belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who completed the checklist.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Check if all required items are complete.
     */
    public function isComplete()
    {
        $requiredItems = [];
        
        // Only check items that have been selected (ticked)
        if ($this->attributes['blood_test_results'] == 1) $requiredItems[] = 'blood_test_results';
        if ($this->attributes['iv_medication'] == 1) $requiredItems[] = 'iv_medication';
        if ($this->attributes['imaging'] == 1) $requiredItems[] = 'imaging';
        if ($this->attributes['procedures'] == 1) $requiredItems[] = 'procedures';
        if ($this->attributes['referral'] == 1) $requiredItems[] = 'referral';
        if ($this->attributes['documentation'] == 1) $requiredItems[] = 'documentation';
        
        // If no items are selected, checklist is not complete
        if (empty($requiredItems)) {
            return false;
        }
        
        // Check if all selected items are completed
        foreach ($requiredItems as $item) {
            if (!$this->$item) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute()
    {
        // Count selected (ticked) items
        $selectedItems = 0;
        $completedItems = 0;
        
        // Check which items are selected and completed
        if ($this->attributes['blood_test_results'] == 1) {
            $selectedItems++;
            if ($this->blood_test_results) $completedItems++;
        }
        
        if ($this->attributes['iv_medication'] == 1) {
            $selectedItems++;
            if ($this->iv_medication) $completedItems++;
        }
        
        if ($this->attributes['imaging'] == 1) {
            $selectedItems++;
            if ($this->imaging) $completedItems++;
        }
        
        if ($this->attributes['procedures'] == 1) {
            $selectedItems++;
            if ($this->procedures) $completedItems++;
        }
        
        if ($this->attributes['referral'] == 1) {
            $selectedItems++;
            if ($this->referral) $completedItems++;
        }
        
        if ($this->attributes['documentation'] == 1) {
            $selectedItems++;
            if ($this->documentation) $completedItems++;
        }
        
        // If no items are selected, return 0%
        if ($selectedItems === 0) {
            return 0;
        }
        
        return round(($completedItems / $selectedItems) * 100);
    }

    /**
     * Get the days until planned discharge.
     */
    public function getDaysUntilDischargeAttribute()
    {
        if (!$this->planned_discharge_date) {
            return null;
        }
        
        $today = now()->startOfDay();
        $plannedDate = $this->planned_discharge_date->startOfDay();
        
        $days = $today->diffInDays($plannedDate, false);
        
        if ($days < 0) {
            return "Overdue by " . abs($days) . " " . (abs($days) == 1 ? 'day' : 'days');
        } elseif ($days === 0) {
            return "Today";
        } else {
            return $days . " " . ($days == 1 ? 'day' : 'days') . " left";
        }
    }
    
    /**
     * Get the total days admitted.
     */
    public function getTotalAdmittedDaysAttribute()
    {
        if (!$this->admission) {
            return null;
        }
        
        $admissionDate = $this->admission->admission_date->startOfDay();
        
        if ($this->planned_discharge_date) {
            $dischargeDate = $this->planned_discharge_date->startOfDay();
        } elseif ($this->admission->actual_discharge_date) {
            $dischargeDate = $this->admission->actual_discharge_date->startOfDay();
        } else {
            $dischargeDate = now()->startOfDay();
        }
        
        return $admissionDate->diffInDays($dischargeDate) + 1; // +1 to include the day of admission
    }

    /**
     * Check if a specific item is selected (ticked).
     */
    public function isItemSelected($item)
    {
        if (!in_array($item, [
            'blood_test_results',
            'iv_medication',
            'imaging',
            'procedures',
            'referral',
            'documentation'
        ])) {
            return false;
        }
        
        return $this->attributes[$item] == 1;
    }
    
    /**
     * Get the number of selected items.
     */
    public function getSelectedItemsCountAttribute()
    {
        $count = 0;
        
        if ($this->attributes['blood_test_results'] == 1) $count++;
        if ($this->attributes['iv_medication'] == 1) $count++;
        if ($this->attributes['imaging'] == 1) $count++;
        if ($this->attributes['procedures'] == 1) $count++;
        if ($this->attributes['referral'] == 1) $count++;
        if ($this->attributes['documentation'] == 1) $count++;
        
        return $count;
    }
    
    /**
     * Get the number of completed items.
     */
    public function getCompletedItemsCountAttribute()
    {
        $count = 0;
        
        if ($this->attributes['blood_test_results'] == 1 && $this->blood_test_results) $count++;
        if ($this->attributes['iv_medication'] == 1 && $this->iv_medication) $count++;
        if ($this->attributes['imaging'] == 1 && $this->imaging) $count++;
        if ($this->attributes['procedures'] == 1 && $this->procedures) $count++;
        if ($this->attributes['referral'] == 1 && $this->referral) $count++;
        if ($this->attributes['documentation'] == 1 && $this->documentation) $count++;
        
        return $count;
    }
} 