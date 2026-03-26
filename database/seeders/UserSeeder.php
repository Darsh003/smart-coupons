<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'premium@test.com'],
            [
                'name'       => 'Premium User',
                'password'   => Hash::make('password'),
                'is_premium' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'regular@test.com'],
            [
                'name'       => 'Regular User',
                'password'   => Hash::make('password'),
                'is_premium' => false,
            ]
        );
    }
}
