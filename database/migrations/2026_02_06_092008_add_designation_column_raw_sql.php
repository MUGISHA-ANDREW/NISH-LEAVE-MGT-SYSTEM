<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to ensure the column is added
        try {
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'mysql' || $driver === 'mariadb') {
                // For MySQL/MariaDB - check if column exists first
                $columnExists = DB::select("
                    SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'designation'
                ");
                
                if ($columnExists[0]->count == 0) {
                    DB::statement("
                        ALTER TABLE users 
                        ADD COLUMN designation VARCHAR(255) NULL 
                        AFTER department_id
                    ");
                }
            } elseif ($driver === 'pgsql') {
                // For PostgreSQL
                DB::statement("
                    ALTER TABLE users 
                    ADD COLUMN IF NOT EXISTS designation VARCHAR(255) NULL
                ");
            }
        } catch (\Exception $e) {
            // Log but don't fail if column already exists
            \Log::warning('Designation column migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS designation");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS designation");
            }
        } catch (\Exception $e) {
            // Ignore errors if column doesn't exist
        }
    }
};
