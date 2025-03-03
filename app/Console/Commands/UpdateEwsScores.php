<?php

namespace App\Console\Commands;

use App\Models\VitalSign;
use Illuminate\Console\Command;

class UpdateEwsScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vital-signs:update-ews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing vital sign records with Early Warning Scores (EWS)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting EWS update for all vital sign records...');
        
        $totalRecords = VitalSign::count();
        $this->info("Found $totalRecords vital sign records to process.");
        
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();
        
        // Process in chunks to avoid memory issues with large databases
        VitalSign::chunk(100, function ($vitalSigns) use ($bar) {
            foreach ($vitalSigns as $vitalSign) {
                // Calculate EWS score
                $vitalSign->ews_score = $vitalSign->calculateEWS();
                
                // Save without triggering events
                $vitalSign->saveQuietly();
                
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        $this->info('All vital sign records have been updated with EWS scores.');
        
        return Command::SUCCESS;
    }
}
