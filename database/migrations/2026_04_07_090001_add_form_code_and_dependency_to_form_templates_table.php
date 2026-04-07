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
        Schema::table('form_templates', function (Blueprint $table): void {
            $table->string('form_code', 50)->nullable()->unique()->after('id');
            $table->string('dependency_form_code', 50)->nullable()->after('approval_flow_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_templates', function (Blueprint $table): void {
            $table->dropUnique(['form_code']);
            $table->dropColumn(['form_code', 'dependency_form_code']);
        });
    }
};