<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ward;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wards = [
            [
                'name' => 'General Medicine',
                'code' => 'GEN-01',
                'description' => 'General medicine ward for adult patients',
                'floor' => 2,
                'capacity' => 20,
                'type' => 'general',
                'status' => 'active',
            ],
            [
                'name' => 'Intensive Care',
                'code' => 'ICU-01',
                'description' => 'Intensive care unit for critically ill patients',
                'floor' => 3,
                'capacity' => 10,
                'type' => 'intensive_care',
                'status' => 'active',
            ],
            [
                'name' => 'Pediatrics',
                'code' => 'PED-01',
                'description' => 'Ward for children and adolescents',
                'floor' => 4,
                'capacity' => 15,
                'type' => 'pediatric',
                'status' => 'active',
            ],
            [
                'name' => 'Maternity',
                'code' => 'MAT-01',
                'description' => 'Ward for pregnant women and new mothers',
                'floor' => 5,
                'capacity' => 12,
                'type' => 'maternity',
                'status' => 'active',
            ],
            [
                'name' => 'Surgical',
                'code' => 'SUR-01',
                'description' => 'Ward for pre and post-surgery patients',
                'floor' => 6,
                'capacity' => 18,
                'type' => 'surgery',
                'status' => 'active',
            ],
        ];

        foreach ($wards as $ward) {
            Ward::create($ward);
        }
    }
}
