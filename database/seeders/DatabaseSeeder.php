<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's default users for development only.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('DatabaseSeeder is for development only and refuses to run in production.');
        }

        $this->command?->info('Seeding default development users...');

        User::updateOrCreate(
            ['email' => 'admin@donorlink.test'],
            [
                'name' => 'UBTS Administrator',
                'phone' => '+256756788112',
                'role' => Role::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@donorlink.test'],
            [
                'name' => 'UBTS Staff Nurse',
                'phone' => '+256700000002',
                'role' => Role::Staff,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
    }
}
