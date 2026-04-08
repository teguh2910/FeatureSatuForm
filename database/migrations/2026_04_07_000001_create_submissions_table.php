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
        Schema::create('FORM.submissions', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->string('employee_name');
            $table->string('employee_email');
            $table->string('department', 10);
            $table->string('form_type');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('in_review');
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FORM.submissions');
    }
};
