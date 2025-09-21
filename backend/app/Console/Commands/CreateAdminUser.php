<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email} {password}';
    protected $description = 'Create an admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $this->info("Admin user created successfully: {$email}");
        return 0;
    }
}