<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MalaysianDoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Ahmad bin Ismail',
                'email' => 'dr.ahmad@hospital.gov.my',
                'role' => 'doctor',
            ],
            [
                'name' => 'Dr. Tan Wei Ming',
                'email' => 'dr.tanwm@hospital.gov.my',
                'role' => 'doctor',
            ],
            [
                'name' => 'Dr. Priya Krishnan',
                'email' => 'dr.priya@hospital.gov.my',
                'role' => 'doctor',
            ]
        ];
        
        foreach ($doctors as $doctor) {
            // Check if email already exists
            if (!User::where('email', $doctor['email'])->exists()) {
                User::create([
                    'name' => $doctor['name'],
                    'email' => $doctor['email'],
                    'role' => $doctor['role'],
                    'password' => Hash::make('password'),
                ]);
                echo "Created doctor: {$doctor['name']}\n";
            } else {
                echo "Doctor with email {$doctor['email']} already exists, skipping.\n";
            }
        }
    }
} 