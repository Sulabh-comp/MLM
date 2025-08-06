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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('address');
            $table->foreignId('employee_id')->nullable();
            $table->timestamp('last_notification_read_at')->default(now());
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies');
            $table->string('sponcer_code')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('religion')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('adhar_number')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });

        Schema::create('family-members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('occupation')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('monthly_income')->nullable();

            // Health status
            $table->tinyInteger('health_status')->nullable();
            $table->string('disease_name')->nullable();
            $table->string('medicine_expenses')->nullable();
            $table->string('medicine_name')->nullable();
            $table->string('doctor_name')->nullable();

            // Skill Information
            $table->tinyInteger('skill_knowledge')->nullable();
            $table->string('skill_name')->nullable();
            $table->string('institute_certified')->nullable();
            $table->string('year_of_passing')->nullable();
            $table->string('degree_course')->nullable();
            $table->string('professional_courses')->nullable();
            $table->string('course_name')->nullable();
            $table->string('institute_name')->nullable();
            $table->string('work_city')->nullable();
            $table->tinyInteger('looking_for_opportunity')->nullable();
            $table->tinyInteger('mlm')->nullable();
            $table->tinyInteger('sales_marketing')->nullable();
            $table->tinyInteger('partner_commission_work')->nullable();
            $table->tinyInteger('manufacturing_work')->nullable();
            $table->tinyInteger('commission_work')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');

        Schema::dropIfExists('customers');

        Schema::dropIfExists('family-members');
    }
};
