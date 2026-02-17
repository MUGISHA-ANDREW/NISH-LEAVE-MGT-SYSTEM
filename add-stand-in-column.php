<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    if (!Schema::hasColumn('leave_requests', 'stand_in_employee_id')) {
        DB::statement('ALTER TABLE leave_requests ADD COLUMN stand_in_employee_id BIGINT UNSIGNED NULL AFTER status');
        DB::statement('ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_stand_in_employee_id_foreign FOREIGN KEY (stand_in_employee_id) REFERENCES users(id) ON DELETE SET NULL');
        echo "SUCCESS: stand_in_employee_id column added!\n";
    } else {
        echo "Column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
