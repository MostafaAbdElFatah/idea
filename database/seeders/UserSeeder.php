<?php

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
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('admin-password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        User::firstOrCreate([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('test-password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::firstOrCreate([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('jane-password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        User::factory(7)->create();
    }
}
