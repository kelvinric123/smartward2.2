<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Consultant;
use App\Models\Nurse;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Call seeders in correct order to respect database relationships
        $this->call([
            // First seed users
            UserSeeder::class,
            
            // Malaysian doctors
            MalaysianDoctorSeeder::class,
            
            // Then wards
            WardSeeder::class,
            
            // Then beds which depend on wards
            BedSeeder::class,
            
            // Then patients
            PatientSeeder::class,
            
            // Then devices which depend on wards
            MedicalDeviceSeeder::class,
            
            // Finally seed admissions (depends on patients, wards, beds, and doctors)
            AdmissionSeeder::class,
            
            // Seed vital signs data for specific patients
            VitalSignsSeeder::class,
            
            // Seed OT System data (rooms, surgeons, anesthetists, schedules)
            OTSystemSeeder::class,
            
            // Then admissions and vital signs will be added in their own seeders if needed
        ]);

        // Sample Consultants
        $consultants = [
            [
                'name' => 'Dr. John Smith',
                'specialty' => 'Cardiology',
                'contact_number' => '012-345 6789',
                'status' => 'Available',
                'email' => 'john.smith@hospital.com',
                'office_location' => 'Building A, Room 101',
                'notes' => 'Senior Cardiologist with 15 years of experience',
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'specialty' => 'Neurology',
                'contact_number' => '019-876 5432',
                'status' => 'On Call',
                'email' => 'sarah.johnson@hospital.com',
                'office_location' => 'Building B, Room 205',
                'notes' => 'Specializes in stroke treatment',
            ],
            [
                'name' => 'Dr. Michael Williams',
                'specialty' => 'Orthopedics',
                'contact_number' => '011-2345 6789',
                'status' => 'In Surgery',
                'email' => 'michael.williams@hospital.com',
                'office_location' => 'Building C, Room 310',
                'notes' => 'Expert in knee and hip replacements',
            ],
            [
                'name' => 'Dr. Emily Brown',
                'specialty' => 'Pediatrics',
                'contact_number' => '013-456 7890',
                'status' => 'Available',
                'email' => 'emily.brown@hospital.com',
                'office_location' => 'Building A, Room 150',
                'notes' => 'Child development specialist',
            ],
            [
                'name' => 'Dr. Robert Chen',
                'specialty' => 'Oncology',
                'contact_number' => '016-789 0123',
                'status' => 'On Leave',
                'email' => 'robert.chen@hospital.com',
                'office_location' => 'Building D, Room 420',
                'notes' => 'Leading research on breast cancer treatments',
            ],
        ];
        
        foreach ($consultants as $consultant) {
            Consultant::create($consultant);
        }
        
        // Sample Nurses
        $nurses = [
            [
                'name' => 'Emma Wilson',
                'position' => 'Head Nurse',
                'ward_assignment' => 'Cardiology',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '017-123 4567',
                'email' => 'emma.wilson@hospital.com',
                'employment_date' => '2018-05-10',
                'notes' => 'Excellent leadership skills',
            ],
            [
                'name' => 'Robert Taylor',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'Pediatrics',
                'shift' => 'Evening',
                'status' => 'Break',
                'contact_number' => '014-234 5678',
                'email' => 'robert.taylor@hospital.com',
                'employment_date' => '2020-03-15',
                'notes' => 'Great with children',
            ],
            [
                'name' => 'Sophie Martin',
                'position' => 'Registered Nurse',
                'ward_assignment' => 'Emergency',
                'shift' => 'Night',
                'status' => 'On Duty',
                'contact_number' => '019-345 6789',
                'email' => 'sophie.martin@hospital.com',
                'employment_date' => '2019-11-20',
                'notes' => 'Handles pressure well',
            ],
            [
                'name' => 'James Anderson',
                'position' => 'Specialized Nurse',
                'ward_assignment' => 'Orthopedics',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '018-456 7890',
                'email' => 'james.anderson@hospital.com',
                'employment_date' => '2017-08-05',
                'notes' => 'Rehabilitation specialist',
            ],
            [
                'name' => 'Olivia Brown',
                'position' => 'Junior Nurse',
                'ward_assignment' => 'Pediatrics',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '013-567 8901',
                'email' => 'olivia.brown@hospital.com',
                'employment_date' => '2022-01-10',
                'notes' => 'Fast learner',
            ],
            [
                'name' => 'Emily Clark',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'Neurology',
                'shift' => 'Evening',
                'status' => 'On Duty',
                'contact_number' => '014-678 9012',
                'email' => 'emily.clark@hospital.com',
                'employment_date' => '2019-06-15',
                'notes' => 'Detailed oriented',
            ],
            [
                'name' => 'Daniel Lewis',
                'position' => 'Charge Nurse',
                'ward_assignment' => 'Emergency',
                'shift' => 'Evening',
                'status' => 'On Duty',
                'contact_number' => '017-789 0123',
                'email' => 'daniel.lewis@hospital.com',
                'employment_date' => '2016-09-20',
                'notes' => 'Excellent in emergency situations',
            ],
            [
                'name' => 'William Turner',
                'position' => 'Senior Nurse',
                'ward_assignment' => 'ICU',
                'shift' => 'Night',
                'status' => 'On Duty',
                'contact_number' => '018-890 1234',
                'email' => 'william.turner@hospital.com',
                'employment_date' => '2015-04-10',
                'notes' => 'Critical care specialist',
            ],
            [
                'name' => 'Charlotte White',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'General',
                'shift' => 'Night',
                'status' => 'Off Duty',
                'contact_number' => '013-901 2345',
                'email' => 'charlotte.white@hospital.com',
                'employment_date' => '2020-07-01',
                'notes' => 'Versatile and adaptable',
            ],
        ];
        
        foreach ($nurses as $nurse) {
            Nurse::create($nurse);
        }
    }
}
