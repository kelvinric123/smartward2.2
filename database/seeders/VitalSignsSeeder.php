<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\User;
use App\Models\Admission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VitalSignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a staff member to use as the recorder of vital signs
        $staff = User::where('role', 'nurse')->first();
        if (!$staff) {
            $staff = User::first(); // If no nurse found, use any user
        }
        
        if (!$staff) {
            $this->command->error("No users found in the database. Cannot seed vital signs.");
            return;
        }
        
        // Get patients for different scenarios
        // Find patients who are currently admitted (up to 3)
        $admittedPatients = Patient::whereHas('admissions', function($query) {
            $query->where('status', 'active');
        })->take(3)->get();
        
        // Find patients who are not currently admitted (up to 2)
        $notAdmittedPatients = Patient::whereDoesntHave('admissions', function($query) {
            $query->where('status', 'active');
        })->orWhereDoesntHave('admissions')->take(2)->get();
        
        // Find a child patient (age < 18) if available
        $childPatient = Patient::whereDate('date_of_birth', '>', now()->subYears(18))->first();
        
        // Find an elderly patient (age > 60) if available
        $elderlyPatient = Patient::whereDate('date_of_birth', '<', now()->subYears(60))->first();
        
        // Seed vital signs for admitted patients
        foreach ($admittedPatients as $index => $patient) {
            $isChild = $childPatient && $patient->id === $childPatient->id;
            $isElderly = $elderlyPatient && $patient->id === $elderlyPatient->id;
            $hasFever = $index === 0; // First admitted patient will have fever progression
            
            $this->seedVitalSignsForPatient($patient, $staff->id, true, $isChild, $isElderly, $hasFever);
        }
        
        // Seed vital signs for non-admitted patients
        foreach ($notAdmittedPatients as $index => $patient) {
            $isChild = $childPatient && $patient->id === $childPatient->id;
            $isElderly = $elderlyPatient && $patient->id === $elderlyPatient->id;
            $hasFever = $index === 0; // First non-admitted patient will have fever progression
            
            $this->seedVitalSignsForPatient($patient, $staff->id, false, $isChild, $isElderly, $hasFever);
        }
    }
    
    /**
     * Create vital signs for a specific patient
     */
    private function seedVitalSignsForPatient(Patient $patient, int $staffId, bool $isAdmitted, bool $isChild = false, bool $isElderly = false, bool $hasFever = false)
    {
        // Get admission id if the patient is admitted
        $admissionId = null;
        if ($isAdmitted) {
            $admission = $patient->admissions()->where('status', 'active')->first();
            if ($admission) {
                $admissionId = $admission->id;
            }
        }
        
        // Create multiple vital sign readings over the past week
        for ($i = 0; $i < 5; $i++) {
            $daysAgo = 7 - $i;
            $hoursOffset = rand(0, 23);
            $minutesOffset = rand(0, 59);
            
            $timestamp = Carbon::now()
                ->subDays($daysAgo)
                ->setHour($hoursOffset)
                ->setMinute($minutesOffset);
            
            // Calculate appropriate vital signs based on patient characteristics
            $vitalSigns = $this->generateVitalSigns($isChild, $isElderly, $hasFever, $i);
            
            // Create the vital sign record
            VitalSign::create([
                'patient_id' => $patient->id,
                'admission_id' => $admissionId,
                'measured_by' => $staffId,
                'temperature' => $vitalSigns['temperature'],
                'heart_rate' => $vitalSigns['heart_rate'],
                'respiratory_rate' => $vitalSigns['respiratory_rate'],
                'systolic_bp' => $vitalSigns['systolic_bp'],
                'diastolic_bp' => $vitalSigns['diastolic_bp'],
                'oxygen_saturation' => $vitalSigns['oxygen_saturation'],
                'blood_glucose' => $vitalSigns['blood_glucose'],
                'pain_level' => $vitalSigns['pain_level'],
                'notes' => $vitalSigns['notes'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
        
        $this->command->info("Created vital signs for patient {$patient->full_name} (ID: {$patient->id})");
    }
    
    /**
     * Generate appropriate vital signs based on patient characteristics
     */
    private function generateVitalSigns(bool $isChild, bool $isElderly, bool $hasFever, int $dayIndex): array
    {
        // Default vital signs (healthy adult)
        $temperature = 36.5 + (rand(0, 10) / 10);
        $heartRate = 70 + rand(-10, 10);
        $respiratoryRate = 16 + rand(-2, 3);
        $systolicBp = 120 + rand(-10, 15);
        $diastolicBp = 80 + rand(-5, 10);
        $oxygenSaturation = 98 + rand(-2, 2);
        $bloodGlucose = 5.0 + (rand(0, 10) / 10);
        $painLevel = rand(0, 2);
        $notes = "Routine vital signs check.";
        
        // Adjust for child
        if ($isChild) {
            $heartRate = 90 + rand(-5, 15);
            $respiratoryRate = 22 + rand(-2, 4);
            $systolicBp = 90 + rand(-5, 10);
            $diastolicBp = 60 + rand(-5, 5);
        }
        
        // Adjust for elderly
        if ($isElderly) {
            $heartRate = 75 + rand(-5, 10);
            $systolicBp = 135 + rand(-10, 15);
            $diastolicBp = 85 + rand(-5, 10);
            $bloodGlucose = 5.5 + (rand(0, 15) / 10);
        }
        
        // Introduce fever progression for patients with fever
        if ($hasFever) {
            // Fever gets worse then better
            if ($dayIndex < 2) {
                $temperature = 38.0 + (rand(0, 15) / 10);
                $heartRate += 15 + rand(0, 10);
                $respiratoryRate += 4 + rand(0, 3);
                $painLevel = 3 + rand(0, 3);
                $notes = "Patient reporting fever and general discomfort. Advised to rest and stay hydrated.";
            } else if ($dayIndex == 2) {
                $temperature = 39.0 + (rand(0, 10) / 10);
                $heartRate += 20 + rand(0, 15);
                $respiratoryRate += 6 + rand(0, 4);
                $oxygenSaturation -= rand(3, 5);
                $painLevel = 5 + rand(0, 3);
                $notes = "Fever persisting. Patient reporting headache and body aches. Started on antipyretics.";
            } else {
                // Recovery phase
                $temperature = 37.5 - ($dayIndex - 3) * (rand(5, 10) / 10);
                $temperature = max(36.5, $temperature);
                $heartRate = min($heartRate, 100 - ($dayIndex - 3) * rand(5, 10));
                $painLevel = max(0, 3 - ($dayIndex - 3));
                $notes = "Fever improving. Patient reporting feeling better.";
            }
        }
        
        // Randomize the last digit to create some variation
        $temperature = round($temperature * 10) / 10;
        $bloodGlucose = round($bloodGlucose * 10) / 10;
        
        // Ensure values are within reasonable limits
        $temperature = max(35.0, min(42.0, $temperature));
        $heartRate = max(40, min(200, $heartRate));
        $respiratoryRate = max(8, min(40, $respiratoryRate));
        $systolicBp = max(70, min(200, $systolicBp));
        $diastolicBp = max(40, min(120, $diastolicBp));
        $oxygenSaturation = max(80, min(100, $oxygenSaturation));
        $bloodGlucose = max(3.0, min(20.0, $bloodGlucose));
        $painLevel = max(0, min(10, $painLevel));
        
        return [
            'temperature' => $temperature,
            'heart_rate' => $heartRate,
            'respiratory_rate' => $respiratoryRate,
            'systolic_bp' => $systolicBp,
            'diastolic_bp' => $diastolicBp,
            'oxygen_saturation' => $oxygenSaturation,
            'blood_glucose' => $bloodGlucose,
            'pain_level' => $painLevel,
            'notes' => $notes
        ];
    }
} 