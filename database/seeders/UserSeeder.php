<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user
        User::create([
            'name' => 'Dr. Tai',
            'email' => 'drtai@qmed.asia',
            'role' => 'superadmin',
            'password' => Hash::make('888888'),
        ]);

        // Create a regular admin user
        User::create([
            'name' => 'Ward Administrator',
            'email' => 'admin@qmed.asia',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Create a regular nurse user
        User::create([
            'name' => 'Siti Aminah binti Hassan',
            'email' => 'nurse@qmed.asia',
            'role' => 'nurse',
            'password' => Hash::make('password'),
        ]);
        
        // Create three Malaysian doctors
        User::create([
            'name' => 'Dr. Ahmad bin Ismail',
            'email' => 'dr.ahmad@hospital.gov.my',
            'role' => 'doctor',
            'password' => Hash::make('password'),
        ]);
        
        User::create([
            'name' => 'Dr. Tan Wei Ming',
            'email' => 'dr.tanwm@hospital.gov.my',
            'role' => 'doctor',
            'password' => Hash::make('password'),
        ]);
        
        User::create([
            'name' => 'Dr. Priya Krishnan',
            'email' => 'dr.priya@hospital.gov.my',
            'role' => 'doctor',
            'password' => Hash::make('password'),
        ]);
    }
}
