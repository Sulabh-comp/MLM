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
        // Add code column to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });

        // Add code column to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });

        // Add code column to agencies table
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });

        // Add code column to managers table
        Schema::table('managers', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });

        // Add code column to family-members table
        Schema::table('family-members', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove code column from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        // Remove code column from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        // Remove code column from agencies table
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        // Remove code column from managers table
        Schema::table('managers', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        // Remove code column from family-members table
        Schema::table('family-members', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
