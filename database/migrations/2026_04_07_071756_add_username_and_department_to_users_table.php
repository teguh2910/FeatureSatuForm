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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->enum('department', ['HR', 'FIN', 'IT', 'OPS'])->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    // SQL Server requires dropping the unique index before the column.
                    $table->dropUnique('users_username_unique');
                } catch (\Throwable $e) {
                    // Ignore when index is already missing.
                }
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }

        if (Schema::hasColumn('users', 'department')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }
};
