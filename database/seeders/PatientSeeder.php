<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Malaysian first names
        $malayMaleNames = ['Ahmad', 'Mohamed', 'Muhammad', 'Ismail', 'Ibrahim', 'Yusof', 'Abdullah', 'Aziz', 'Rahman', 'Hafiz', 'Zulkifli', 'Saiful', 'Nazrin', 'Farid', 'Kamal'];
        $malayFemaleNames = ['Siti', 'Nurul', 'Nur', 'Farah', 'Fatimah', 'Aminah', 'Aishah', 'Zainab', 'Noraini', 'Sharifah', 'Nazliah', 'Hanis', 'Nadia', 'Izni', 'Farhanah'];
        
        $chineseMaleNames = ['Wei', 'Jin', 'Liang', 'Chong', 'Hong', 'Jian', 'Cheng', 'Teck', 'Yong', 'Ming', 'Zhi Wei', 'Jia Hao', 'Jun Jie', 'Yi Feng', 'Tian Long'];
        $chineseFemaleNames = ['Mei', 'Li', 'Hui', 'Ying', 'Xin', 'Hui Lin', 'Shu Fen', 'Wei Ling', 'Siew Mei', 'Siew Ling', 'Pei Ying', 'Jia Xin', 'Zhi Ling', 'Hui Min', 'Yee Ling'];
        
        $chineseSurnames = ['Tan', 'Wong', 'Lim', 'Lee', 'Ng', 'Cheong', 'Ong', 'Koh', 'Goh', 'Chan', 'Teo', 'Yeo', 'Choo', 'Lau', 'Sim', 'Chin'];
        
        $indianMaleNames = ['Raj', 'Kumar', 'Vijay', 'Suresh', 'Ganesh', 'Ramesh', 'Rajesh', 'Mahesh', 'Prakash', 'Siva', 'Gopal', 'Dhiren', 'Arun', 'Vikram', 'Naveen'];
        $indianFemaleNames = ['Priya', 'Rani', 'Lakshmi', 'Devi', 'Meena', 'Kavitha', 'Saranya', 'Deepa', 'Anita', 'Sunita', 'Lalitha', 'Geetha', 'Sangeetha', 'Vimala', 'Jayanthi'];
        
        $indianSurnames = ['Patel', 'Singh', 'Raj', 'Sharma', 'Gopal', 'Murthy', 'Krishnan', 'Nathan', 'Muthu', 'Samy', 'Raju', 'Balakrishnan', 'Pillai', 'Nair', 'Selvam'];
        
        // Malaysian last names
        $malayLastNames = ['bin Abdullah', 'bin Mohamed', 'bin Ahmad', 'bin Ismail', 'bin Ibrahim', 'bin Rahman', 'binti Abdullah', 'binti Mohamed', 'binti Ahmad', 'binti Ismail', 'binti Ibrahim', 'binti Rahman', 'bin Hassan', 'bin Razali', 'bin Zainal', 'binti Hassan', 'binti Razali', 'binti Zainal'];

        // Malaysian phone prefixes
        $phonePrefixes = ['011', '012', '013', '014', '015', '016', '017', '018', '019'];

        // Create 50 sample patients
        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $ethnicity = $faker->randomElement(['malay', 'chinese', 'indian']);
            
            // Generate name based on ethnicity and gender
            if ($ethnicity === 'malay') {
                if ($gender === 'male') {
                    $firstName = $faker->randomElement($malayMaleNames);
                    $lastName = str_replace('binti', 'bin', $faker->randomElement($malayLastNames));
                } else {
                    $firstName = $faker->randomElement($malayFemaleNames);
                    $lastName = str_replace('bin', 'binti', $faker->randomElement($malayLastNames));
                }
            } elseif ($ethnicity === 'chinese') {
                $lastName = $faker->randomElement($chineseSurnames);
                if ($gender === 'male') {
                    $firstName = $faker->randomElement($chineseMaleNames);
                } else {
                    $firstName = $faker->randomElement($chineseFemaleNames);
                }
            } else { // indian
                if ($gender === 'male') {
                    $firstName = $faker->randomElement($indianMaleNames);
                    $lastName = $faker->randomElement($indianSurnames);
                    $fullName = $firstName . ' a/l ' . $lastName;
                } else {
                    $firstName = $faker->randomElement($indianFemaleNames);
                    $lastName = $faker->randomElement($indianSurnames);
                    $fullName = $firstName . ' a/p ' . $lastName;
                }
            }
            
            // Generate Malaysian phone number
            $prefix = $faker->randomElement($phonePrefixes);
            $phoneNumber = '+60' . $prefix . '-' . $faker->numberBetween(100, 999) . ' ' . $faker->numberBetween(1000, 9999);
            
            // Generate emergency contact phone number
            $emergencyPrefix = $faker->randomElement($phonePrefixes);
            $emergencyPhone = '+60' . $emergencyPrefix . '-' . $faker->numberBetween(100, 999) . ' ' . $faker->numberBetween(1000, 9999);
            
            // Format name for Chinese ethnicity
            if ($ethnicity === 'chinese') {
                $fullName = $lastName . ' ' . $firstName;
            } elseif ($ethnicity === 'malay') {
                $fullName = $firstName . ' ' . $lastName;
            }
            
            // Split the name for database storage
            $nameParts = explode(' ', $fullName, 2);
            $dbFirstName = $nameParts[0];
            $dbLastName = isset($nameParts[1]) ? $nameParts[1] : '';
            
            // Generate medical history as a comma-separated string instead of an array
            $medicalConditions = ['Hypertension', 'Diabetes', 'Asthma', 'Eczema', 'Heart Disease', 'Stroke', 'Cancer', 'Thyroid Disease', 'Arthritis', 'GERD'];
            $medicalHistory = $faker->boolean(80) ? implode(', ', $faker->randomElements($medicalConditions, $faker->numberBetween(0, 3))) : null;
            
            Patient::create([
                'mrn' => 'MRN-' . time() . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'first_name' => $dbFirstName,
                'last_name' => $dbLastName,
                'date_of_birth' => $faker->dateTimeBetween('-80 years', '-1 years'),
                'gender' => $gender,
                'contact_number' => $phoneNumber,
                'email' => strtolower(str_replace(' ', '.', $fullName)) . '@' . $faker->randomElement(['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com']),
                'address' => $faker->streetAddress . ', ' . $faker->randomElement(['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Subang Jaya', 'Ipoh', 'Penang', 'Johor Bahru', 'Melaka', 'Kota Kinabalu', 'Kuching']) . ', ' . $faker->postcode . ', Malaysia',
                'emergency_contact_name' => $ethnicity === 'malay' ? $faker->randomElement(array_merge($malayMaleNames, $malayFemaleNames)) . ' ' . $faker->randomElement($malayLastNames) : 
                                          ($ethnicity === 'chinese' ? $faker->randomElement($chineseSurnames) . ' ' . $faker->randomElement(array_merge($chineseMaleNames, $chineseFemaleNames)) : 
                                          (function() use ($faker, $indianMaleNames, $indianFemaleNames, $indianSurnames) {
                                              $eGender = $faker->randomElement(['male', 'female']);
                                              $eName = $eGender === 'male' ? 
                                                  $faker->randomElement($indianMaleNames) . ' a/l ' . $faker->randomElement($indianSurnames) :
                                                  $faker->randomElement($indianFemaleNames) . ' a/p ' . $faker->randomElement($indianSurnames);
                                              return $eName;
                                          })()),
                'emergency_contact_number' => $emergencyPhone,
                'allergies' => $faker->optional(0.3)->randomElement(['Penicillin', 'Peanuts', 'Latex', 'Aspirin', 'Sulfa', 'Shellfish', 'Eggs', 'Wheat', 'None known']),
                'medical_history' => $medicalHistory,
            ]);
        }
    }
}
