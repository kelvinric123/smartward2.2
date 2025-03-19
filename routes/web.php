<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BedManagementController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicalProfessionalController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\VitalSignController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\RosterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard Route
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// All other authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ward Transfer API Routes (accessible to all authenticated users)
    Route::get('/patients/available-wards', [PatientController::class, 'getAvailableWards'])->name('patients.available-wards');
    Route::get('/patients/available-beds/{ward}', [PatientController::class, 'getAvailableBeds'])->name('patients.available-beds');
    
    // Consultant Management Routes
    Route::resource('consultants', ConsultantController::class);
    Route::patch('/consultants/{consultant}/deactivate', [ConsultantController::class, 'deactivate'])->name('consultants.deactivate');
    
    // Nurse Management Routes
    Route::resource('nurses', NurseController::class);
    Route::patch('/nurses/{nurse}/deactivate', [NurseController::class, 'deactivate'])->name('nurses.deactivate');

    // Nurse Roster Management Routes
    Route::get('/roster', [RosterController::class, 'index'])->name('roster.index');
    Route::get('/roster/{nurse}/edit', [RosterController::class, 'edit'])->name('roster.edit');
    Route::patch('/roster/{nurse}', [RosterController::class, 'update'])->name('roster.update');
    Route::get('/roster/ward/{wardName}', [RosterController::class, 'wardRoster'])->name('roster.ward');
    
    // Medical Professional Routes - Accessible to doctor, admin, and superadmin roles
    Route::middleware('role:doctor')->group(function() {
        Route::get('/medical-professional/consultants', [MedicalProfessionalController::class, 'consultants'])->name('medical-professional.consultants');
        Route::get('/medical-professional/nurses', [MedicalProfessionalController::class, 'nurses'])->name('medical-professional.nurses');
        Route::get('/medical-professional/roster', [MedicalProfessionalController::class, 'roster'])->name('medical-professional.roster');
        
        // OT Scheduling Routes
        Route::get('/ot-scheduling/dashboard', [App\Http\Controllers\OTSchedulingController::class, 'dashboard'])->name('ot-scheduling.dashboard');
        Route::get('/ot-scheduling/bookings', [App\Http\Controllers\OTSchedulingController::class, 'bookings'])->name('ot-scheduling.bookings');
        Route::get('/ot-scheduling/bookings/create', [App\Http\Controllers\OTSchedulingController::class, 'createBooking'])->name('ot-scheduling.create-booking');
        Route::post('/ot-scheduling/bookings', [App\Http\Controllers\OTSchedulingController::class, 'storeBooking'])->name('ot-scheduling.store-booking');
        Route::get('/ot-scheduling/bookings/{booking}', [App\Http\Controllers\OTSchedulingController::class, 'showBooking'])->name('ot-scheduling.show-booking');
        Route::get('/ot-scheduling/bookings/{booking}/edit', [App\Http\Controllers\OTSchedulingController::class, 'editBooking'])->name('ot-scheduling.edit-booking');
        Route::patch('/ot-scheduling/bookings/{booking}', [App\Http\Controllers\OTSchedulingController::class, 'updateBooking'])->name('ot-scheduling.update-booking');
        Route::delete('/ot-scheduling/bookings/{booking}', [App\Http\Controllers\OTSchedulingController::class, 'destroyBooking'])->name('ot-scheduling.destroy-booking');
        Route::patch('/ot-scheduling/bookings/{booking}/status', [App\Http\Controllers\OTSchedulingController::class, 'updateBookingStatus'])->name('ot-scheduling.update-booking-status');
        Route::patch('/ot-scheduling/bookings/{booking}/operation-details', [App\Http\Controllers\OTSchedulingController::class, 'updateOperationDetails'])->name('ot-scheduling.update-operation-details');
        Route::get('/ot-scheduling/staff-availability', [App\Http\Controllers\OTSchedulingController::class, 'staffAvailability'])->name('ot-scheduling.staff-availability');
        
        // OT Room Management Routes
        Route::get('/ot-scheduling/rooms', [App\Http\Controllers\OTSchedulingController::class, 'rooms'])->name('ot-scheduling.rooms');
        Route::get('/ot-scheduling/rooms/create', [App\Http\Controllers\OTSchedulingController::class, 'createRoom'])->name('ot-scheduling.create-room');
        Route::post('/ot-scheduling/rooms', [App\Http\Controllers\OTSchedulingController::class, 'storeRoom'])->name('ot-scheduling.store-room');
        Route::get('/ot-scheduling/rooms/{room}', [App\Http\Controllers\OTSchedulingController::class, 'showRoom'])->name('ot-scheduling.show-room');
        Route::get('/ot-scheduling/rooms/{room}/edit', [App\Http\Controllers\OTSchedulingController::class, 'editRoom'])->name('ot-scheduling.edit-room');
        Route::patch('/ot-scheduling/rooms/{room}', [App\Http\Controllers\OTSchedulingController::class, 'updateRoom'])->name('ot-scheduling.update-room');
        Route::delete('/ot-scheduling/rooms/{room}', [App\Http\Controllers\OTSchedulingController::class, 'destroyRoom'])->name('ot-scheduling.destroy-room');
        
        // OT Display Routes
        Route::get('/ot-scheduling/display', [App\Http\Controllers\OTSchedulingController::class, 'otDisplay'])->name('ot-scheduling.display');
        Route::get('/ot-scheduling/display/{room}', [App\Http\Controllers\OTSchedulingController::class, 'otDisplayRoom'])->name('ot-scheduling.display-room');
    });
    
    // Bed Management Routes - Accessible to nurse, doctor, admin, and superadmin roles
    Route::middleware('role:nurse')->group(function() {
        Route::get('/bed-management/dashboard', [BedManagementController::class, 'dashboard'])->name('bed-management.dashboard');
        Route::get('/bed-management/bed-map', [BedManagementController::class, 'bedMap'])->name('bed-management.bed-map');
        Route::get('/bed-management/find-bed', [BedManagementController::class, 'findBed'])->name('bed-management.find-bed');
        Route::get('/bed-management/statistics', [BedManagementController::class, 'statistics'])->name('bed-management.statistics');
        
        // Individual Bed Routes
        Route::get('/beds/{bed}', [BedManagementController::class, 'showBed'])->name('beds.show');
        Route::get('/beds/{bed}/edit', [BedManagementController::class, 'editBed'])->name('beds.edit');
        Route::patch('/beds/{bed}', [BedManagementController::class, 'updateBed'])->name('beds.update');
        Route::patch('/beds/{bed}/mark-available', [BedManagementController::class, 'markAvailable'])->name('beds.mark-available');
        Route::patch('/beds/{bed}/mark-cleaning-complete', [BedManagementController::class, 'markCleaningComplete'])->name('beds.mark-cleaning-complete');
        
        // Admission Routes
        Route::get('/beds/{bed}/admit', [BedManagementController::class, 'admitForm'])->name('beds.admit-form');
        Route::post('/beds/{bed}/admit', [BedManagementController::class, 'admitToBed'])->name('beds.admit');
        Route::get('/admissions/{admission}', [BedManagementController::class, 'showAdmission'])->name('admissions.show');
    });
    
    // Patient Routes - Accessible to nurse, doctor, admin, and superadmin roles
    Route::middleware('role:nurse')->group(function() {
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::patch('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        
        // Keep the transfer action in the middleware group
        Route::post('/patients/{patient}/transfer-ward', [PatientController::class, 'transferWard'])->name('patients.transfer-ward');
        
        // Vital Signs Routes
        Route::get('/patients/{patient}/vital-signs', [VitalSignController::class, 'index'])->name('vital-signs.index');
        Route::get('/vital-signs/create', [VitalSignController::class, 'create'])->name('vital-signs.create');
        Route::post('/vital-signs', [VitalSignController::class, 'store'])->name('vital-signs.store');
        Route::get('/vital-signs/{vitalSign}', [VitalSignController::class, 'show'])->name('vital-signs.show');
        Route::get('/vital-signs/{vitalSign}/edit', [VitalSignController::class, 'edit'])->name('vital-signs.edit');
        Route::patch('/vital-signs/{vitalSign}', [VitalSignController::class, 'update'])->name('vital-signs.update');
        Route::delete('/vital-signs/{vitalSign}', [VitalSignController::class, 'destroy'])->name('vital-signs.destroy');
        Route::get('/admissions/{admission}/vital-signs/create', [VitalSignController::class, 'createForAdmission'])->name('vital-signs.create-for-admission');
        
        // Discharge Checklist Routes
        Route::get('/discharge-checklists', [App\Http\Controllers\DischargeChecklistController::class, 'index'])->name('discharge-checklist.index');
        Route::get('/discharge-checklists/{dischargeChecklist}', [App\Http\Controllers\DischargeChecklistController::class, 'show'])->name('discharge-checklist.show');
        Route::get('/discharge-checklists/{dischargeChecklist}/edit', [App\Http\Controllers\DischargeChecklistController::class, 'edit'])->name('discharge-checklist.edit');
        Route::patch('/discharge-checklists/{dischargeChecklist}', [App\Http\Controllers\DischargeChecklistController::class, 'update'])->name('discharge-checklist.update');
        Route::post('/discharge-checklists/{dischargeChecklist}/complete', [App\Http\Controllers\DischargeChecklistController::class, 'completeDischarge'])->name('discharge-checklist.complete');
        Route::post('/discharge-checklists/{dischargeChecklist}/complete-item/{item}', [App\Http\Controllers\DischargeChecklistController::class, 'completeItem'])->name('discharge-checklist.complete-item');
        Route::post('/admissions/{admission}/start-discharge', [App\Http\Controllers\DischargeChecklistController::class, 'startDischargeProcess'])->name('admissions.start-discharge');
    });
});

// Debug route - no auth required
Route::get('/debug/consultants', function() {
    $consultants = App\Models\Consultant::all();
    return response()->json([
        'count' => $consultants->count(),
        'consultants' => $consultants->toArray()
    ]);
});

// Debug route to show current user's role
Route::middleware('auth')->get('/debug/user-role', function() {
    $user = auth()->user();
    return response()->json([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'is_superadmin' => $user->isSuperAdmin(),
    ]);
});

require __DIR__.'/auth.php';
