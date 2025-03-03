<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Surgeon;
use App\Models\Anesthetist;
use App\Models\StaffAvailability;
use App\Models\OTSchedule;
use App\Models\Patient;
use App\Models\OTRoom;

class OTSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if already seeded
        if (Surgeon::where('employee_id', 'SUR001')->exists()) {
            $this->command->info('OT System data already exists. Skipping seeder.');
            return;
        }

        // Create OT rooms
        $this->createOTRooms();
        
        // Create surgeons
        $this->createSurgeons();
        
        // Create anesthetists
        $this->createAnesthetists();
        
        // Create staff availabilities
        $this->createStaffAvailabilities();
        
        // Create sample OT schedules if we have patients
        if (Patient::count() > 0) {
            $this->createSampleSchedules();
        }
    }
    
    /**
     * Create Operation Theater rooms
     */
    private function createOTRooms()
    {
        $rooms = [
            [
                'room_number' => 'OT-101',
                'name' => 'Main Surgical Suite',
                'status' => 'available',
                'floor' => '1',
                'building' => 'Main Building',
                'type' => 'general',
                'capacity' => 8,
                'equipment' => 'Anesthesia machine, surgical table, surgical lights, monitors, ventilator',
                'notes' => 'Primary general surgery room',
                'is_active' => true,
            ],
            [
                'room_number' => 'OT-102',
                'name' => 'Cardiac Theater',
                'status' => 'available',
                'floor' => '1',
                'building' => 'Main Building',
                'type' => 'cardiac',
                'capacity' => 10,
                'equipment' => 'Cardiopulmonary bypass machine, transesophageal echocardiography, defibrillator, surgical lights',
                'notes' => 'Specialized room for cardiac procedures',
                'is_active' => true,
            ],
            [
                'room_number' => 'OT-201',
                'name' => 'Orthopedic Suite',
                'status' => 'available',
                'floor' => '2',
                'building' => 'East Wing',
                'type' => 'orthopedic',
                'capacity' => 6,
                'equipment' => 'C-arm fluoroscopy, arthroscopy tower, orthopedic table, power tools',
                'notes' => 'Equipped for joint replacements and fracture surgeries',
                'is_active' => true,
            ],
        ];
        
        // Create each room if it doesn't exist
        foreach ($rooms as $room) {
            if (!OTRoom::where('room_number', $room['room_number'])->exists()) {
                OTRoom::create($room);
            } else {
                $this->command->info("OT Room with room_number {$room['room_number']} already exists. Skipping.");
            }
        }
        
        $this->command->info('OT Rooms seeded successfully');
    }
    
    /**
     * Create sample surgeons
     */
    private function createSurgeons()
    {
        $surgeons = [
            [
                'name' => 'Dr. John Smith',
                'employee_id' => 'SUR001',
                'specialization' => 'Cardiothoracic Surgery',
                'contact_number' => '012-345 6789',
                'email' => 'john.smith@hospital.com',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'employee_id' => 'SUR002',
                'specialization' => 'Neurosurgery',
                'contact_number' => '019-876 5432',
                'email' => 'sarah.johnson@hospital.com',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Michael Wong',
                'employee_id' => 'SUR003',
                'specialization' => 'Orthopedic Surgery',
                'contact_number' => '011-2345 6789',
                'email' => 'michael.wong@hospital.com',
                'status' => 'active',
            ],
        ];
        
        foreach ($surgeons as $surgeon) {
            // Check if surgeon with this employee_id already exists
            if (!Surgeon::where('employee_id', $surgeon['employee_id'])->exists()) {
                Surgeon::create($surgeon);
            } else {
                // Optionally update existing record if needed
                // Surgeon::where('employee_id', $surgeon['employee_id'])->update([
                //     'name' => $surgeon['name'],
                //     'specialization' => $surgeon['specialization'],
                //     'contact_number' => $surgeon['contact_number'],
                //     'email' => $surgeon['email'],
                //     'status' => $surgeon['status'],
                // ]);
                $this->command->info("Surgeon with employee_id {$surgeon['employee_id']} already exists. Skipping.");
            }
        }
        
        $this->command->info('Surgeons seeded successfully');
    }
    
    /**
     * Create sample anesthetists
     */
    private function createAnesthetists()
    {
        $anesthetists = [
            [
                'name' => 'Dr. Emily Davis',
                'employee_id' => 'ANE001',
                'specialization' => 'General Anesthesia',
                'contact_number' => '013-987 6543',
                'email' => 'emily.davis@hospital.com',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Robert Chen',
                'employee_id' => 'ANE002',
                'specialization' => 'Pediatric Anesthesia',
                'contact_number' => '016-765 4321',
                'email' => 'robert.chen@hospital.com',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Lisa Martinez',
                'employee_id' => 'ANE003',
                'specialization' => 'Cardiac Anesthesia',
                'contact_number' => '014-567 8901',
                'email' => 'lisa.martinez@hospital.com',
                'status' => 'active',
            ],
        ];
        
        foreach ($anesthetists as $anesthetist) {
            // Check if anesthetist with this employee_id already exists
            if (!Anesthetist::where('employee_id', $anesthetist['employee_id'])->exists()) {
                Anesthetist::create($anesthetist);
            } else {
                $this->command->info("Anesthetist with employee_id {$anesthetist['employee_id']} already exists. Skipping.");
            }
        }
        
        $this->command->info('Anesthetists seeded successfully');
    }
    
    /**
     * Create sample staff availabilities
     */
    private function createStaffAvailabilities()
    {
        // Get surgeons and anesthetists
        $surgeons = Surgeon::all();
        $anesthetists = Anesthetist::all();
        
        // Current date
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        // Create availabilities for surgeons
        foreach ($surgeons as $surgeon) {
            // Today's availability
            $exists = StaffAvailability::where('staff_id', $surgeon->id)
                ->where('staff_type', 'surgeon')
                ->where('date', $today)
                ->exists();
                
            if (!$exists) {
                StaffAvailability::create([
                    'staff_id' => $surgeon->id,
                    'staff_type' => 'surgeon',
                    'date' => $today,
                    'start_time' => '08:00',
                    'end_time' => '14:00',
                    'is_available' => true,
                ]);
            }
            
            // Tomorrow's availability
            $exists = StaffAvailability::where('staff_id', $surgeon->id)
                ->where('staff_type', 'surgeon')
                ->where('date', $tomorrow)
                ->exists();
                
            if (!$exists) {
                StaffAvailability::create([
                    'staff_id' => $surgeon->id,
                    'staff_type' => 'surgeon',
                    'date' => $tomorrow,
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'is_available' => true,
                ]);
            }
        }
        
        // Create availabilities for anesthetists
        foreach ($anesthetists as $anesthetist) {
            // Today's availability
            $exists = StaffAvailability::where('staff_id', $anesthetist->id)
                ->where('staff_type', 'anesthetist')
                ->where('date', $today)
                ->exists();
                
            if (!$exists) {
                StaffAvailability::create([
                    'staff_id' => $anesthetist->id,
                    'staff_type' => 'anesthetist',
                    'date' => $today,
                    'start_time' => '08:00',
                    'end_time' => '14:00',
                    'is_available' => true,
                ]);
            }
            
            // Tomorrow's availability
            $exists = StaffAvailability::where('staff_id', $anesthetist->id)
                ->where('staff_type', 'anesthetist')
                ->where('date', $tomorrow)
                ->exists();
                
            if (!$exists) {
                StaffAvailability::create([
                    'staff_id' => $anesthetist->id,
                    'staff_type' => 'anesthetist',
                    'date' => $tomorrow,
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'is_available' => true,
                ]);
            }
        }
        
        $this->command->info('Staff availabilities seeded successfully');
    }
    
    /**
     * Create sample OT schedules
     */
    private function createSampleSchedules()
    {
        // Get some patients, surgeons, and anesthetists
        $patients = Patient::take(3)->get();
        $surgeons = Surgeon::all();
        $anesthetists = Anesthetist::all();
        $rooms = OTRoom::all();
        
        if ($patients->isEmpty() || $surgeons->isEmpty() || $anesthetists->isEmpty() || $rooms->isEmpty()) {
            $this->command->info('Cannot create OT schedules: missing required models');
            return;
        }
        
        // Check if we have enough models
        if ($patients->count() < 3 || $surgeons->count() < 3 || $anesthetists->count() < 3 || $rooms->count() < 3) {
            $this->command->info('Warning: Not enough models to create all sample schedules. Using available models.');
        }
        
        // Today's date
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        // Sample procedures
        $procedures = [
            'Appendectomy',
            'Coronary Bypass',
            'Knee Replacement',
            'Brain Tumor Removal',
            'Cataract Surgery',
            'Hernia Repair'
        ];
        
        // Create schedules with unique keys for identification
        $schedules = [];
        
        // Safely access models with fallbacks
        $patient1 = $patients->get(0) ?? $patients->first();
        $patient2 = $patients->get(1) ?? $patients->first();
        $patient3 = $patients->get(2) ?? $patients->first();
        
        $surgeon1 = $surgeons->get(0) ?? $surgeons->first();
        $surgeon2 = $surgeons->get(1) ?? $surgeons->first();
        $surgeon3 = $surgeons->get(2) ?? $surgeons->first();
        
        $anesthetist1 = $anesthetists->get(0) ?? $anesthetists->first();
        $anesthetist2 = $anesthetists->get(1) ?? $anesthetists->first();
        $anesthetist3 = $anesthetists->get(2) ?? $anesthetists->first();
        
        $room1 = $rooms->get(0) ?? $rooms->first();
        $room2 = $rooms->get(1) ?? $rooms->first();
        $room3 = $rooms->get(2) ?? $rooms->first();
        
        // Only add schedules if we have the necessary models
        if ($patient1 && $surgeon1 && $anesthetist1 && $room1) {
            $schedules[] = [
                'patient_id' => $patient1->id,
                'surgeon_id' => $surgeon1->id,
                'anesthetist_id' => $anesthetist1->id,
                'room_id' => $room1->id,
                'schedule_date' => $today,
                'start_time' => '09:00',
                'end_time' => '11:00',
                'procedure_type' => $procedures[0],
                'status' => 'scheduled',
                'notes' => 'Regular procedure'
            ];
        }
        
        if ($patient2 && $surgeon2 && $anesthetist2 && $room2) {
            $schedules[] = [
                'patient_id' => $patient2->id,
                'surgeon_id' => $surgeon2->id,
                'anesthetist_id' => $anesthetist2->id,
                'room_id' => $room2->id,
                'schedule_date' => $today,
                'start_time' => '11:30',
                'end_time' => '13:30',
                'procedure_type' => $procedures[1],
                'status' => 'in-progress',
                'notes' => 'Critical patient'
            ];
        }
        
        if ($patient3 && $surgeon3 && $anesthetist3 && $room3) {
            $schedules[] = [
                'patient_id' => $patient3->id,
                'surgeon_id' => $surgeon3->id,
                'anesthetist_id' => $anesthetist3->id,
                'room_id' => $room3->id,
                'schedule_date' => $today,
                'start_time' => '14:00',
                'end_time' => '16:00',
                'procedure_type' => $procedures[2],
                'status' => 'scheduled',
                'notes' => 'Elective surgery'
            ];
        }
        
        if ($patient1 && $surgeon2 && $anesthetist1 && $room1) {
            $schedules[] = [
                'patient_id' => $patient1->id,
                'surgeon_id' => $surgeon2->id,
                'anesthetist_id' => $anesthetist1->id,
                'room_id' => $room1->id,
                'schedule_date' => $tomorrow,
                'start_time' => '09:00',
                'end_time' => '11:00',
                'procedure_type' => $procedures[3],
                'status' => 'scheduled',
                'notes' => 'Follow-up procedure'
            ];
        }
        
        // Track created schedules
        $created = 0;
        
        // Create schedules, avoiding duplicates
        foreach ($schedules as $schedule) {
            try {
                // Check if a similar schedule exists
                $existing = OTSchedule::where('schedule_date', $schedule['schedule_date'])
                    ->where('start_time', $schedule['start_time'])
                    ->where('room_id', $schedule['room_id'])
                    ->first();
                    
                if (!$existing) {
                    OTSchedule::create($schedule);
                    $created++;
                    
                    // Update room status based on the booking status
                    $room = OTRoom::find($schedule['room_id']);
                    if ($room) {
                        if ($schedule['status'] == 'in-progress') {
                            $room->status = 'occupied';
                        } else if ($schedule['status'] == 'scheduled') {
                            $room->status = 'reserved';
                        }
                        $room->save();
                    }
                } else {
                    $this->command->info("Schedule already exists for room {$schedule['room_id']} on {$schedule['schedule_date']} at {$schedule['start_time']}. Skipping.");
                }
            } catch (\Exception $e) {
                $this->command->error("Error creating schedule: " . $e->getMessage());
            }
        }
        
        $this->command->info("Created {$created} OT schedules");
    }
}
