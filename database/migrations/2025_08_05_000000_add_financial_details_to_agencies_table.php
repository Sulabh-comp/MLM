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
        Schema::table('agencies', function (Blueprint $table) {
            // Bank Details
            $table->string('bank_name')->nullable()->after('status');
            $table->string('account_holder_name')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('account_holder_name');
            $table->string('ifsc_code')->nullable()->after('account_number');
            $table->string('branch_name')->nullable()->after('ifsc_code');
            
            // Identity Documents
            $table->string('aadhar_number')->nullable()->after('branch_name');
            $table->string('pan_number')->nullable()->after('aadhar_number');
            
            // Document verification status
            $table->tinyInteger('documents_verified')->default(0)->after('pan_number');
            $table->timestamp('documents_submitted_at')->nullable()->after('documents_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_holder_name', 
                'account_number',
                'ifsc_code',
                'branch_name',
                'aadhar_number',
                'pan_number',
                'documents_verified',
                'documents_submitted_at'
            ]);
        });
    }
};
