<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset auto-increment to start from 1 if there are no records
        // Or set it to the max ID + 1 if there are records
        DB::statement('ALTER TABLE employees AUTO_INCREMENT = 1');
        
        // If there are existing records, set auto-increment to max ID + 1
        $maxId = DB::table('employees')->max('id');
        if ($maxId) {
            DB::statement('ALTER TABLE employees AUTO_INCREMENT = ' . ($maxId + 1));
        }
        
        // Ensure the id column is properly set as auto-increment primary key
        Schema::table('employees', function (Blueprint $table) {
            // Make sure id is unsigned big integer and auto-increment
            $table->id()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse - this is a fix migration
    }
};
