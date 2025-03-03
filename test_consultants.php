<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test database connection
try {
    $pdo = DB::connection()->getPdo();
    echo "Database connection successful! Database name: " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test consultant retrieval
try {
    $consultants = App\Models\Consultant::all();
    echo "Found " . $consultants->count() . " consultants:\n";
    
    foreach ($consultants as $consultant) {
        echo "- {$consultant->name} ({$consultant->specialty}): {$consultant->status}\n";
    }
} catch (\Exception $e) {
    echo "Error retrieving consultants: " . $e->getMessage() . "\n";
} 