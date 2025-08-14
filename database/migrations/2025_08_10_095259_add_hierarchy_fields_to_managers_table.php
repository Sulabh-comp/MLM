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
        Schema::table('managers', function (Blueprint $table) {
            // Parent-child relationship for hierarchy
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('managers')->onDelete('set null');
            
            // Manager level/position name (Regional Manager, Area Manager, etc.)
            $table->string('level_name')->default('Manager')->after('designation');
            
            // Hierarchy depth (0 = top level, 1 = second level, etc.)
            $table->integer('depth')->default(0)->after('level_name');
            
            // Materialized path for efficient querying (e.g., "/1/3/7/")
            $table->string('hierarchy_path')->nullable()->after('depth');
            
            // Index for better performance
            $table->index(['parent_id']);
            $table->index(['depth']);
            $table->index(['hierarchy_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['depth']);
            $table->dropIndex(['hierarchy_path']);
            $table->dropColumn(['parent_id', 'level_name', 'depth', 'hierarchy_path']);
        });
    }
};
