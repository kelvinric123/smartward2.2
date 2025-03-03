<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalDevice;
use App\Models\Ward;
use Faker\Factory as Faker;

class MedicalDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $wards = Ward::all();
        
        $deviceTypes = [
            'Vital Signs Monitor',
            'ECG Machine',
            'Ventilator',
            'Infusion Pump',
            'Patient Monitor',
            'Pulse Oximeter',
            'Blood Pressure Monitor',
            'Glucometer',
            'Defibrillator',
            'Thermometer'
        ];
        
        $manufacturers = [
            'Philips',
            'GE Healthcare',
            'Medtronic',
            'Siemens Healthineers',
            'Dräger',
            'Omron',
            'Fresenius',
            'Abbott',
            'Mindray',
            'B. Braun'
        ];
        
        // Create 20 sample devices
        for ($i = 0; $i < 20; $i++) {
            $deviceType = $faker->randomElement($deviceTypes);
            $manufacturer = $faker->randomElement($manufacturers);
            $serialNumber = strtoupper($manufacturer[0] . $manufacturer[1]) . '-' . $faker->numberBetween(10000, 99999);
            
            MedicalDevice::create([
                'name' => $deviceType . ' ' . $faker->randomElement(['Pro', 'Plus', 'Advanced', 'Standard', 'Premier']),
                'serial_number' => $serialNumber,
                'model' => $manufacturer . ' ' . $faker->randomElement(['X', 'V', 'Z']) . $faker->numberBetween(100, 999),
                'manufacturer' => $manufacturer,
                'device_type' => $deviceType,
                'status' => $faker->randomElement(['active', 'active', 'active', 'maintenance', 'inactive']),
                'last_calibration_date' => $faker->dateTimeBetween('-1 year', '-1 month'),
                'next_calibration_date' => $faker->dateTimeBetween('+1 month', '+1 year'),
                'ip_address' => $faker->optional(0.7)->ipv4,
                'port' => $faker->optional(0.7)->numberBetween(1000, 9999),
                'connection_protocol' => $faker->randomElement(['TCP/IP', 'Bluetooth', 'Serial', 'HTTP', 'MQTT']),
                'api_key' => $faker->optional(0.5)->sha256,
                'connection_details' => $faker->optional(0.6)->randomElement([
                    json_encode(['username' => 'device_user', 'password' => 'secure_password']),
                    json_encode(['auth_token' => $faker->sha256]),
                    json_encode(['certificate' => 'device_cert.pem']),
                    null
                ]),
                'notes' => $faker->optional(0.4)->sentence(10),
                'ward_id' => $faker->randomElement($wards)->id,
            ]);
        }
    }
}
