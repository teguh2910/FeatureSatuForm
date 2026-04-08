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
            $table->json('fields_config')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('FORM.form_templates', function (Blueprint $table): void {
            $table->dropColumn('fields_config');
        });
    }
};
