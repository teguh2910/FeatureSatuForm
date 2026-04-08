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
        Schema::table('FORM.form_templates', function (Blueprint $table): void {
            $table->json('approval_flow_config')->nullable()->after('fields_config');
        });

        Schema::table('FORM.submissions', function (Blueprint $table): void {
            $table->unsignedInteger('current_approval_step')->default(0)->after('status');
            $table->json('approval_flow_snapshot')->nullable()->after('current_approval_step');
            $table->json('approval_history')->nullable()->after('approval_flow_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('FORM.form_templates', function (Blueprint $table): void {
            $table->dropColumn('approval_flow_config');
        });

        Schema::table('FORM.submissions', function (Blueprint $table): void {
            $table->dropColumn(['current_approval_step', 'approval_flow_snapshot', 'approval_history']);
        });
    }
};
