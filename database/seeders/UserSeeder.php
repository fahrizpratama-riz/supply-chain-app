<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin User',
                'email'     => 'admin@supplychain.com',
                'password'  => Hash::make('password123'),
                'role'      => 'admin',
                'is_active' => true,
            ],
            [
                'name'      => 'Ahmad Analyst',
                'email'     => 'analyst@supplychain.com',
                'password'  => Hash::make('password123'),
                'role'      => 'analyst',
                'is_active' => true,
            ],
            [
                'name'      => 'Budi Santoso',
                'email'     => 'user@supplychain.com',
                'password'  => Hash::make('password123'),
                'role'      => 'user',
                'is_active' => true,
            ],
            [
                'name'      => 'Maria Santos',
                'email'     => 'maria@supplychain.com',
                'password'  => Hash::make('password123'),
                'role'      => 'user',
                'is_active' => true,
            ],
            [
                'name'      => 'Wei Chen',
                'email'     => 'weichen@supplychain.com',
                'password'  => Hash::make('password123'),
                'role'      => 'analyst',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Users seeded: admin, analyst, 3 users');
    }
}
