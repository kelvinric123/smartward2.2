<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalDevice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'serial_number',
        'model',
        'manufacturer',
        'device_type',
        'status',
        'last_calibration_date',
        'next_calibration_date',
        'ip_address',
        'port',
        'connection_protocol',
        'api_key',
        'connection_details',
        'notes',
        'ward_id',
    ];

    protected $casts = [
        'last_calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'port' => 'integer',
        'connection_details' => 'json',
    ];

    // Relationships
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function needsCalibration()
    {
        return $this->next_calibration_date && $this->next_calibration_date->isPast();
    }

    public function getConnectionInfoAttribute()
    {
        if (!$this->ip_address) {
            return null;
        }
        
        return $this->ip_address . ($this->port ? ':' . $this->port : '');
    }

    // Device communication methods
    public function connect()
    {
        // Implementation would depend on the specific device integration
        // This is a stub for the future implementation
        return [
            'success' => true,
            'message' => 'Connected to device ' . $this->name,
        ];
    }

    public function fetchData($patientId = null)
    {
        // Implementation would depend on the specific device integration
        // This is a stub for the future implementation
        return [
            'success' => true,
            'data' => [
                'device_id' => $this->serial_number,
                'device_model' => $this->model,
                'timestamp' => now()->toIso8601String(),
                'patient_id' => $patientId,
                // Sample vital sign data
                'temperature' => rand(360, 380) / 10, // 36.0 - 38.0
                'heart_rate' => rand(60, 100),
                'respiratory_rate' => rand(12, 20),
                'systolic_bp' => rand(90, 140),
                'diastolic_bp' => rand(60, 90),
                'oxygen_saturation' => rand(95, 100),
            ]
        ];
    }

    public function recordVitalSigns($patientId, $admissionId = null)
    {
        $response = $this->fetchData($patientId);
        
        if ($response['success']) {
            $data = $response['data'];
            $data['admission_id'] = $admissionId;
            
            return VitalSign::receiveFromDevice($data, $this);
        }
        
        return null;
    }
} 