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
        Schema::table('employees', function (Blueprint $table) {
            // Add manager_id column first
            $table->unsignedBigInteger('manager_id')->nullable()->after('designation');
            
            // Create foreign key for manager_id
            $table->foreign('manager_id')->references('id')->on('managers')->onDelete('set null');
            
            // Drop foreign key constraint for region_id first
            $table->dropForeign('employees_region_id_foreign');
            
            // Then drop the region_id column
            $table->dropColumn('region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add back region_id column
            $table->unsignedBigInteger('region_id')->nullable()->after('designation');
            
            // Create foreign key for region_id
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            
            // Drop foreign key constraint for manager_id
            $table->dropForeign(['manager_id']);
            
            // Drop manager_id column
            $table->dropColumn('manager_id');
        });
    }
};
