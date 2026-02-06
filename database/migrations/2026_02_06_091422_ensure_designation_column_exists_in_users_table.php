<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if designation column exists before adding it
        if (!Schema::hasColumn('users', 'designation')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('designation')->nullable()->after('department_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if column exists
        if (Schema::hasColumn('users', 'designation')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('designation');
            });
        }
    }
};
