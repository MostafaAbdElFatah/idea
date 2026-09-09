<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referenceUsers = [
            ['email' => 'admin@example.com', 'first_name' => 'Admin', 'last_name' => 'User', 'password' => 'admin-password'],
            ['email' => 'test@example.com', 'first_name' => 'Test', 'last_name' => 'User', 'password' => 'test-password'],
            ['email' => 'jane@example.com', 'first_name' => 'Jane', 'last_name' => 'Doe', 'password' => 'jane-password'],
        ];

        foreach ($referenceUsers as $referenceUser) {
            User::firstOrCreate(
                ['email' => $referenceUser['email']],
                [
                    'first_name' => $referenceUser['first_name'],
                    'last_name' => $referenceUser['last_name'],
                    'password' => Hash::make($referenceUser['password']),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
        }

        if (User::query()->count() < 10) {
            User::factory(10 - User::query()->count())->create();
        }
    }
}
