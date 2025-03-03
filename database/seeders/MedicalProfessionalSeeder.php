<?php

namespace Database\Seeders;

use App\Models\Consultant;
use App\Models\Nurse;
use Illuminate\Database\Seeder;

class MedicalProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Consultants with Malaysian names
        $consultants = [
            [
                'name' => 'Dr. Ahmad bin Razali',
                'specialty' => 'Cardiology',
                'contact_number' => '+6012-345 6789',
                'status' => 'Available',
                'email' => 'ahmad.razali@hospital.com',
                'office_location' => 'Building A, Room 101',
                'notes' => 'Senior Cardiologist with 15 years of experience',
            ],
            [
                'name' => 'Dr. Siti Nurul binti Hassan',
                'specialty' => 'Neurology',
                'contact_number' => '+6019-876 5432',
                'status' => 'Available',
                'email' => 'siti.nurul@hospital.com',
                'office_location' => 'Building B, Room 205',
                'notes' => 'Specializes in stroke treatment',
            ],
            [
                'name' => 'Dr. Tan Wei Ming',
                'specialty' => 'Orthopedics',
                'contact_number' => '+6017-222 3344',
                'status' => 'Available',
                'email' => 'tanweiming@hospital.com',
                'office_location' => 'Building C, Room 310',
                'notes' => 'Expert in knee and hip replacements',
            ],
            [
                'name' => 'Dr. Wong Mei Ling',
                'specialty' => 'Pediatrics',
                'contact_number' => '+6016-555 7788',
                'status' => 'Available',
                'email' => 'wongmeiling@hospital.com',
                'office_location' => 'Building A, Room 150',
                'notes' => 'Child development specialist',
            ],
            [
                'name' => 'Dr. Rajesh a/l Krishnan',
                'specialty' => 'Oncology',
                'contact_number' => '+6013-987 6543',
                'status' => 'Available',
                'email' => 'rajesh.krishnan@hospital.com',
                'office_location' => 'Building D, Room 420',
                'notes' => 'Leading research on breast cancer treatments',
            ],
            [
                'name' => 'Dr. Priya a/p Muthu',
                'specialty' => 'Dermatology',
                'contact_number' => '+6011-1234 5678',
                'status' => 'Available',
                'email' => 'priya.muthu@hospital.com',
                'office_location' => 'Building B, Room 315',
                'notes' => 'Specializes in skin disorders and treatments',
            ],
            [
                'name' => 'Dr. Mohamed Faizal bin Abdullah',
                'specialty' => 'Gastroenterology',
                'contact_number' => '+6014-876 5432',
                'status' => 'Available',
                'email' => 'mohamed.faizal@hospital.com',
                'office_location' => 'Building C, Room 210',
                'notes' => 'Digestive health specialist',
            ],
            [
                'name' => 'Dr. Lim Chee Keat',
                'specialty' => 'Ophthalmology',
                'contact_number' => '+6018-765 4321',
                'status' => 'Available',
                'email' => 'limcheekeat@hospital.com',
                'office_location' => 'Building A, Room 220',
                'notes' => 'Eye surgery specialist',
            ],
        ];
        
        foreach ($consultants as $consultant) {
            Consultant::create($consultant);
        }
        
        // Sample Nurses
        $nurses = [
            [
                'name' => 'Nor Azizah binti Kamal',
                'position' => 'Head Nurse',
                'ward_assignment' => 'Cardiology',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '+6012-345 6789',
                'email' => 'nor.azizah@hospital.com',
                'employment_date' => '2018-05-10',
                'notes' => 'Excellent leadership skills',
            ],
            [
                'name' => 'Mohd Farid bin Yusof',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'Pediatrics',
                'shift' => 'Evening',
                'status' => 'Break',
                'contact_number' => '+6019-876 5432',
                'email' => 'mohd.farid@hospital.com',
                'employment_date' => '2020-03-15',
                'notes' => 'Great with children',
            ],
            [
                'name' => 'Lim Siew Ling',
                'position' => 'Registered Nurse',
                'ward_assignment' => 'Emergency',
                'shift' => 'Night',
                'status' => 'On Duty',
                'contact_number' => '+6017-222 5678',
                'email' => 'lim.siewling@hospital.com',
                'employment_date' => '2019-11-20',
                'notes' => 'Handles pressure well',
            ],
            [
                'name' => 'Raj Kumar a/l Gopal',
                'position' => 'Specialized Nurse',
                'ward_assignment' => 'Orthopedics',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '+6016-456 7890',
                'email' => 'raj.kumar@hospital.com',
                'employment_date' => '2017-08-05',
                'notes' => 'Rehabilitation specialist',
            ],
            [
                'name' => 'Nurul Huda binti Abdullah',
                'position' => 'Junior Nurse',
                'ward_assignment' => 'Pediatrics',
                'shift' => 'Morning',
                'status' => 'On Duty',
                'contact_number' => '+6013-567 8901',
                'email' => 'nurul.huda@hospital.com',
                'employment_date' => '2022-01-10',
                'notes' => 'Fast learner',
            ],
            [
                'name' => 'Wong Mei Yee',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'Neurology',
                'shift' => 'Evening',
                'status' => 'On Duty',
                'contact_number' => '+6011-678 9012',
                'email' => 'wong.meiyee@hospital.com',
                'employment_date' => '2019-06-15',
                'notes' => 'Detailed oriented',
            ],
            [
                'name' => 'Muhammad Hafiz bin Rahman',
                'position' => 'Charge Nurse',
                'ward_assignment' => 'Emergency',
                'shift' => 'Evening',
                'status' => 'On Duty',
                'contact_number' => '+6014-789 0123',
                'email' => 'muhammad.hafiz@hospital.com',
                'employment_date' => '2016-09-20',
                'notes' => 'Excellent in emergency situations',
            ],
            [
                'name' => 'Lee Chong Wei',
                'position' => 'Senior Nurse',
                'ward_assignment' => 'ICU',
                'shift' => 'Night',
                'status' => 'On Duty',
                'contact_number' => '+6018-890 1234',
                'email' => 'lee.chongwei@hospital.com',
                'employment_date' => '2015-04-10',
                'notes' => 'Critical care specialist',
            ],
            [
                'name' => 'Lakshmi a/p Murthy',
                'position' => 'Staff Nurse',
                'ward_assignment' => 'General',
                'shift' => 'Night',
                'status' => 'Off Duty',
                'contact_number' => '+6015-901 2345',
                'email' => 'lakshmi.murthy@hospital.com',
                'employment_date' => '2020-07-01',
                'notes' => 'Versatile and adaptable',
            ],
        ];
        
        foreach ($nurses as $nurse) {
            Nurse::create($nurse);
        }
    }
} 