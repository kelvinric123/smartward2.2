<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'admission_id',
        'temperature',
        'heart_rate',
        'blood_pressure',
        'respiratory_rate',
        'systolic_bp',
        'diastolic_bp',
        'oxygen_saturation',
        'blood_glucose',
        'pain_level',
        'device_id',
        'device_model',
        'measured_by',
        'notes',
        'ews_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    /**
     * Get the admission that the vital sign belongs to.
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    /**
     * Get the user who recorded the vital sign.
     */
    public function recordedBy(): BelongsTo
    {
        // Check if the table has measured_by instead of recorded_by
        if (in_array('measured_by', $this->fillable)) {
            return $this->belongsTo(User::class, 'measured_by');
        } else {
            return $this->belongsTo(User::class, 'recorded_by');
        }
    }

    // Helper methods
    public function getBpAttribute()
    {
        if (!$this->systolic_bp || !$this->diastolic_bp) {
            return null;
        }
        return $this->systolic_bp . '/' . $this->diastolic_bp;
    }

    public function isTemperatureNormal()
    {
        return $this->temperature >= 36.5 && $this->temperature <= 37.5;
    }

    public function isHeartRateNormal()
    {
        return $this->heart_rate >= 60 && $this->heart_rate <= 100;
    }

    public function isRespiratoryRateNormal()
    {
        return $this->respiratory_rate >= 12 && $this->respiratory_rate <= 20;
    }

    public function isBpNormal()
    {
        return $this->systolic_bp >= 90 && $this->systolic_bp <= 120 &&
               $this->diastolic_bp >= 60 && $this->diastolic_bp <= 80;
    }

    public function isOxygenSaturationNormal()
    {
        return $this->oxygen_saturation >= 95;
    }

    public function hasAbnormalValues()
    {
        return !$this->isTemperatureNormal() || 
               !$this->isHeartRateNormal() || 
               !$this->isRespiratoryRateNormal() || 
               !$this->isBpNormal() || 
               !$this->isOxygenSaturationNormal() ||
               ($this->pain_level && $this->pain_level > 3);
    }

    /**
     * Calculate Early Warning Score (EWS) based on vital signs.
     * This uses the National Early Warning Score (NEWS) 2 system.
     *
     * @return int|null The calculated EWS or null if vital signs are insufficient
     */
    public function calculateEWS()
    {
        // Check if we have enough data to calculate EWS
        if ($this->respiratory_rate === null &&
            $this->oxygen_saturation === null &&
            $this->systolic_bp === null &&
            $this->heart_rate === null &&
            $this->temperature === null) {
            return null;
        }

        $score = 0;

        // Respiratory Rate
        if ($this->respiratory_rate !== null) {
            if ($this->respiratory_rate <= 8) {
                $score += 3;
            } elseif ($this->respiratory_rate >= 9 && $this->respiratory_rate <= 11) {
                $score += 1;
            } elseif ($this->respiratory_rate >= 12 && $this->respiratory_rate <= 20) {
                $score += 0;
            } elseif ($this->respiratory_rate >= 21 && $this->respiratory_rate <= 24) {
                $score += 2;
            } elseif ($this->respiratory_rate >= 25) {
                $score += 3;
            }
        }

        // Oxygen Saturation
        if ($this->oxygen_saturation !== null) {
            if ($this->oxygen_saturation <= 91) {
                $score += 3;
            } elseif ($this->oxygen_saturation >= 92 && $this->oxygen_saturation <= 93) {
                $score += 2;
            } elseif ($this->oxygen_saturation >= 94 && $this->oxygen_saturation <= 95) {
                $score += 1;
            } elseif ($this->oxygen_saturation >= 96) {
                $score += 0;
            }
        }

        // Systolic Blood Pressure
        if ($this->systolic_bp !== null) {
            if ($this->systolic_bp <= 90) {
                $score += 3;
            } elseif ($this->systolic_bp >= 91 && $this->systolic_bp <= 100) {
                $score += 2;
            } elseif ($this->systolic_bp >= 101 && $this->systolic_bp <= 110) {
                $score += 1;
            } elseif ($this->systolic_bp >= 111 && $this->systolic_bp <= 219) {
                $score += 0;
            } elseif ($this->systolic_bp >= 220) {
                $score += 3;
            }
        }

        // Heart Rate
        if ($this->heart_rate !== null) {
            if ($this->heart_rate <= 40) {
                $score += 3;
            } elseif ($this->heart_rate >= 41 && $this->heart_rate <= 50) {
                $score += 1;
            } elseif ($this->heart_rate >= 51 && $this->heart_rate <= 90) {
                $score += 0;
            } elseif ($this->heart_rate >= 91 && $this->heart_rate <= 110) {
                $score += 1;
            } elseif ($this->heart_rate >= 111 && $this->heart_rate <= 130) {
                $score += 2;
            } elseif ($this->heart_rate >= 131) {
                $score += 3;
            }
        }

        // Temperature
        if ($this->temperature !== null) {
            if ($this->temperature <= 35.0) {
                $score += 3;
            } elseif ($this->temperature >= 35.1 && $this->temperature <= 36.0) {
                $score += 1;
            } elseif ($this->temperature >= 36.1 && $this->temperature <= 38.0) {
                $score += 0;
            } elseif ($this->temperature >= 38.1 && $this->temperature <= 39.0) {
                $score += 1;
            } elseif ($this->temperature >= 39.1) {
                $score += 2;
            }
        }

        return $score;
    }

    /**
     * Get the EWS risk level based on the score
     *
     * @return string The risk level (Low, Low-Medium, Medium, High)
     */
    public function getEwsRiskLevel()
    {
        $score = $this->ews_score ?? $this->calculateEWS();
        
        if ($score === null) {
            return 'Unknown';
        }
        
        if ($score <= 4) {
            return 'Low';
        } elseif ($score >= 5 && $score <= 6) {
            return 'Medium';
        } elseif ($score >= 7) {
            return 'High';
        }
        
        return 'Unknown';
    }

    /**
     * Get the color associated with the EWS risk level
     *
     * @return string The color code
     */
    public function getEwsColorCode()
    {
        $riskLevel = $this->getEwsRiskLevel();
        
        switch ($riskLevel) {
            case 'Low':
                return 'green';
            case 'Medium':
                return 'orange';
            case 'High':
                return 'red';
            default:
                return 'gray';
        }
    }

    /**
     * Save the model with automatic EWS calculation
     */
    public function save(array $options = [])
    {
        // Calculate and set EWS score before saving
        if (!$this->ews_score) {
            $this->ews_score = $this->calculateEWS();
        }
        
        return parent::save($options);
    }

    // Static method to receive data from a device
    public static function receiveFromDevice(array $data, MedicalDevice $device = null)
    {
        $vitalSign = new self([
            'patient_id' => $data['patient_id'],
            'admission_id' => $data['admission_id'] ?? null,
            'temperature' => $data['temperature'] ?? null,
            'heart_rate' => $data['heart_rate'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'systolic_bp' => $data['systolic_bp'] ?? null,
            'diastolic_bp' => $data['diastolic_bp'] ?? null,
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'blood_glucose' => $data['blood_glucose'] ?? null,
            'pain_level' => $data['pain_level'] ?? null,
            'device_id' => $device ? $device->serial_number : ($data['device_id'] ?? null),
            'device_model' => $device ? $device->model : ($data['device_model'] ?? null),
            'measured_by' => $device ? 'device' : ($data['measured_by'] ?? 'manual'),
            'notes' => $data['notes'] ?? null,
        ]);

        // Calculate EWS score
        $vitalSign->ews_score = $vitalSign->calculateEWS();

        $vitalSign->save();
        return $vitalSign;
    }
} 