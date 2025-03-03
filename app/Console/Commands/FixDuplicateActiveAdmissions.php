<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Admission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateActiveAdmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admissions:fix-duplicates {--dry-run : Run without making any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and fix patients with multiple active admissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for patients with multiple active admissions...');
        
        // Find patients with multiple active admissions
        $patientsWithMultipleAdmissions = DB::table('admissions')
            ->select('patient_id')
            ->where('status', 'active')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
            
        if ($patientsWithMultipleAdmissions->isEmpty()) {
            $this->info('No patients with multiple active admissions found.');
            return Command::SUCCESS;
        }
        
        $this->info('Found ' . $patientsWithMultipleAdmissions->count() . ' patients with multiple active admissions.');
        
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN: No changes will be made.');
        }
        
        foreach ($patientsWithMultipleAdmissions as $patientData) {
            $patientId = $patientData->patient_id;
            $patient = Patient::find($patientId);
            
            if (!$patient) {
                $this->warn("Patient ID $patientId not found. Skipping.");
                continue;
            }
            
            $this->info("Processing patient: {$patient->full_name} (ID: {$patient->id})");
            
            // Get all active admissions for this patient, ordered by admission_date (newest first)
            $activeAdmissions = Admission::where('patient_id', $patientId)
                ->where('status', 'active')
                ->orderBy('admission_date', 'desc')
                ->get();
                
            $this->info("  Found " . $activeAdmissions->count() . " active admissions");
            
            // Keep the most recent active admission, discharge the others
            $keepAdmission = $activeAdmissions->first();
            
            foreach ($activeAdmissions as $index => $admission) {
                if ($index === 0) {
                    $this->info("  Keeping admission ID {$admission->id} from {$admission->admission_date->format('M d, Y H:i')} in ward '{$admission->ward->name}'");
                    continue;
                }
                
                $this->warn("  Discharging admission ID {$admission->id} from {$admission->admission_date->format('M d, Y H:i')} in ward '{$admission->ward->name}'");
                
                if (!$dryRun) {
                    DB::beginTransaction();
                    try {
                        // Discharge the admission
                        $admission->update([
                            'status' => 'discharged',
                            'actual_discharge_date' => now(),
                            'notes' => ($admission->notes ? $admission->notes . "\n\n" : '') . 
                                      "Auto-discharged by system due to duplicate active admissions. " .
                                      "Patient has another active admission (ID: {$keepAdmission->id})."
                        ]);
                        
                        // Update the bed status
                        if ($admission->bed) {
                            $admission->bed->update(['status' => 'cleaning']);
                        }
                        
                        DB::commit();
                        $this->info("  ✓ Admission discharged successfully");
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("  ✗ Failed to discharge admission: " . $e->getMessage());
                    }
                }
            }
        }
        
        if ($dryRun) {
            $this->info('Dry run completed. To fix the issues, run the command without --dry-run option.');
        } else {
            $this->info('Fixed all duplicate active admissions.');
        }
        
        return Command::SUCCESS;
    }
}
