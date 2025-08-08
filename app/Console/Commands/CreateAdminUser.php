<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for Filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Admin email', 'admin@example.com');
        $password = $this->secret('Admin password') ?: 'password';
        $firstName = $this->ask('First name', 'Admin');
        $lastName = $this->ask('Last name', 'User');

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Admin user created successfully!");
        $this->info("Email: {$email}");
        $this->info("You can now login to /admin");
        
        return 0;
    }
}
