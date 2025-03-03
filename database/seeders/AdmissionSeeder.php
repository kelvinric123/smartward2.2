<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Bed;
use App\Models\Ward;
use App\Models\User;
use App\Models\Consultant;
use Carbon\Carbon;

class AdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available models to use in the seeder
        $patients = Patient::all();
        $consultants = Consultant::all();
        $beds = Bed::all();
        $wards = Ward::all();
        
        // Make sure we have data to work with
        if ($patients->isEmpty() || $beds->isEmpty() || $wards->isEmpty()) {
            $this->command->error('Please seed patients, beds and wards first');
            return;
        }
        
        $this->command->info('Creating admissions over the past 3 months...');
        
        // Seed 30 admissions and discharges over the past 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        
        // Create 15 admissions with patients who have been discharged
        for ($i = 0; $i < 15; $i++) {
            // Generate random dates for admission and discharge within the 3-month range
            $admissionDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->subDays(5)->timestamp)
            );
            
            // Discharge date between admission and now (with a stay of 1 to 14 days)
            $dischargeDate = clone $admissionDate;
            $dischargeDate->addDays(rand(1, 14));
            
            // If discharge date is in the future, set it to today
            if ($dischargeDate > Carbon::now()) {
                $dischargeDate = Carbon::now();
            }
            
            // Select a random patient, bed, ward and consultant
            $patient = $patients->random();
            $ward = $wards->random();
            $availableBeds = $beds->where('ward_id', $ward->id);
            
            if ($availableBeds->isEmpty()) {
                continue; // Skip if no beds available in the randomly selected ward
            }
            
            $bed = $availableBeds->random();
            $consultant = $consultants->isNotEmpty() ? $consultants->random() : null;
            
            // Create the admission
            $admission = Admission::create([
                'patient_id' => $patient->id,
                'bed_id' => $bed->id,
                'ward_id' => $ward->id,
                'admission_date' => $admissionDate,
                'expected_discharge_date' => (clone $admissionDate)->addDays(rand(3, 21)),
                'actual_discharge_date' => $dischargeDate,
                'status' => 'discharged',
                'consultant_id' => $consultant ? $consultant->id : null,
                'diagnosis' => $this->getRandomDiagnosis(),
                'notes' => $this->getRandomNotes(),
            ]);
            
            $this->command->info("Created discharged admission for patient {$patient->first_name} {$patient->last_name}");
        }
        
        // Create active admissions - ensuring each patient only has one active admission
        $this->command->info('Creating active admissions - ensuring no patient has multiple active admissions...');
        
        // Track patients who already have active admissions
        $patientsWithActiveAdmissions = collect();
        
        // Create 15 current active admissions
        $attemptsCounter = 0;
        $createdActiveAdmissions = 0;
        
        while ($createdActiveAdmissions < 15 && $attemptsCounter < 50) {
            $attemptsCounter++;
            
            // Generate random dates for admission within the last month
            $admissionDate = Carbon::createFromTimestamp(
                rand(Carbon::now()->subMonth()->timestamp, Carbon::now()->timestamp)
            );
            
            // Select a random patient that doesn't already have an active admission
            $eligiblePatients = $patients->whereNotIn('id', $patientsWithActiveAdmissions);
            
            if ($eligiblePatients->isEmpty()) {
                $this->command->warning('All patients already have active admissions. Breaking loop.');
                break;
            }
            
            $patient = $eligiblePatients->random();
            $ward = $wards->random();
            $availableBeds = $beds->where('ward_id', $ward->id)->where('status', 'available');
            
            if ($availableBeds->isEmpty()) {
                continue; // Skip if no beds available in the randomly selected ward
            }
            
            $bed = $availableBeds->random();
            $consultant = $consultants->isNotEmpty() ? $consultants->random() : null;
            
            // Create the admission with active status
            $admission = Admission::create([
                'patient_id' => $patient->id,
                'bed_id' => $bed->id,
                'ward_id' => $ward->id,
                'admission_date' => $admissionDate,
                'expected_discharge_date' => (clone $admissionDate)->addDays(rand(3, 21)),
                'actual_discharge_date' => null,
                'status' => 'active',
                'consultant_id' => $consultant ? $consultant->id : null,
                'diagnosis' => $this->getRandomDiagnosis(),
                'notes' => $this->getRandomNotes(),
            ]);
            
            // Update bed status to occupied for active admissions
            $bed->update(['status' => 'occupied']);
            
            // Add this patient to our tracking collection
            $patientsWithActiveAdmissions->push($patient->id);
            
            $createdActiveAdmissions++;
            $this->command->info("Created active admission for patient {$patient->first_name} {$patient->last_name}");
        }
        
        $this->command->info("Created {$createdActiveAdmissions} active admissions successfully!");
    }
    
    /**
     * Get a random diagnosis.
     */
    private function getRandomDiagnosis(): string
    {
        $diagnoses = [
            'Pneumonia',
            'Acute Myocardial Infarction',
            'Fractured Femur',
            'Diabetes Mellitus Type 2',
            'Hypertension',
            'Congestive Heart Failure',
            'Appendicitis',
            'Gastroenteritis',
            'Urinary Tract Infection',
            'Stroke',
            'COPD Exacerbation',
            'Sepsis',
            'Asthma Attack',
            'Viral Meningitis',
            'Acute Kidney Injury',
            'Cholecystitis',
            'Deep Vein Thrombosis',
            'Pulmonary Embolism',
            'Cellulitis',
            'COVID-19'
        ];
        
        return $diagnoses[array_rand($diagnoses)];
    }
    
    /**
     * Get random notes.
     */
    private function getRandomNotes(): string
    {
        $notes = [
            'Patient responding well to treatment.',
            'Needs assistance with daily activities.',
            'Monitor oxygen saturation closely.',
            'Family visited today.',
            'Showing signs of improvement.',
            'Pain managed with oral medication.',
            'Diet tolerated well.',
            'Requires regular blood glucose monitoring.',
            'Patient ambulating with assistance.',
            'Scheduled for physical therapy tomorrow.',
            'Wound dressing changed daily.',
            'IV antibiotics administered as prescribed.',
            'Patient reports difficulty sleeping.',
            'Respiratory status improving.',
            'Requires oxygen supplementation.',
        ];
        
        $numberOfNotes = rand(1, 3);
        $selectedNotes = [];
        
        for ($i = 0; $i < $numberOfNotes; $i++) {
            $selectedNotes[] = $notes[array_rand($notes)];
        }
        
        return implode("\n", $selectedNotes);
    }
} 