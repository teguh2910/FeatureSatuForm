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
        Schema::table('FORM.users', function (Blueprint $table): void {
            $table->string('level', 30)->default('staff')->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('FORM.users', function (Blueprint $table): void {
            $table->dropColumn('level');
        });
    }
};
