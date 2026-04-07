<?php

namespace Teguh\FeatureSatuForm\Database\Seeders;

use Illuminate\Database\Seeder;
use Teguh\FeatureSatuForm\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsername = (string) env('SUPER_ADMIN_USERNAME', 'admin');
        $adminEmail = (string) env('SUPER_ADMIN_EMAIL', 'admin@feature-satu-form.local');
        $adminPassword = (string) env('SUPER_ADMIN_PASSWORD', 'admin');

        User::query()->updateOrCreate(
            ['username' => $adminUsername],
            [
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'department' => 'IT',
                'level' => 'director',
                'password' => $adminPassword,
            ]
        );

        User::query()->updateOrCreate(
            ['username' => 'guest.demo'],
            [
                'name' => 'Guest Demo',
                'email' => 'guest.demo@feature-satu-form.local',
                'department' => 'HR',
                'level' => 'guest',
                'password' => 'password123',
            ]
        );
    }
}
