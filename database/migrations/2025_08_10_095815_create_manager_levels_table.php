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
        Schema::create('manager_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Regional Manager", "Area Manager"
            $table->string('code')->unique(); // e.g., "RM", "AM", "ZM"
            $table->text('description')->nullable();
            $table->integer('hierarchy_level')->default(1); // 1 = highest, 2 = second, etc.
            $table->boolean('is_predefined')->default(false); // System predefined levels
            $table->boolean('status')->default(true);
            $table->json('permissions')->nullable(); // Future use for level-specific permissions
            $table->timestamps();
            
            // Indexes
            $table->index(['status']);
            $table->index(['hierarchy_level']);
            $table->index(['is_predefined']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_levels');
    }
};
