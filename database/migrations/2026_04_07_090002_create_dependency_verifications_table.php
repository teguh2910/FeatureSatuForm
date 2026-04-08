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
        Schema::create('FORM.dependency_verifications', function (Blueprint $table): void {
            $table->id();
            $table->string('tracking_id', 50)->unique();
            $table->foreignId('form_template_id')->constrained('FORM.form_templates')->cascadeOnDelete();
            $table->foreignId('dependency_form_template_id')->constrained('FORM.form_templates')->noActionOnDelete();
            $table->foreignId('submission_id')->nullable()->constrained('FORM.submissions')->nullOnDelete();
            $table->string('form_code', 50);
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FORM.dependency_verifications');
    }
};