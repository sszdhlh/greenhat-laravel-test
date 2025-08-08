<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->command->info('Admin user created: admin@example.com / password');
    }
}
