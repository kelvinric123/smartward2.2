<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ward;
use App\Models\Bed;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all wards
        $wards = Ward::all();

        foreach ($wards as $ward) {
            // Create beds based on ward capacity
            for ($i = 1; $i <= $ward->capacity; $i++) {
                // Determine bed type based on ward type
                switch ($ward->type) {
                    case 'intensive_care':
                        $bedType = 'intensive_care';
                        break;
                    case 'pediatric':
                        $bedType = 'pediatric';
                        break;
                    case 'maternity':
                        $bedType = rand(0, 1) ? 'electric' : 'standard';
                        break;
                    default:
                        // Random mix of standard and electric beds
                        $bedTypes = ['standard', 'electric', 'standard', 'standard', 'electric'];
                        $bedType = $bedTypes[array_rand($bedTypes)];
                }

                // Create some beds in different states
                $status = 'available';

                Bed::create([
                    'bed_number' => sprintf('%02d', $i),
                    'ward_id' => $ward->id,
                    'status' => $status,
                    'type' => $bedType,
                    'notes' => null,
                ]);
            }
        }
    }
}
