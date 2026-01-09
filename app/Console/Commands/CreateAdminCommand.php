<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'bitmews:create-admin {name} {email} {password}';
    protected $description = 'Create a super admin user';

    public function handle()
    {
        try {
            // Check if user already exists
            if (User::where('email', $this->argument('email'))->exists()) {
                $this->error('User with this email already exists!');
                return 1;
            }

            // Create user
            $user = User::create([
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
                'password' => Hash::make($this->argument('password')),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_verified' => true,
            ]);

            // Get or create super admin role
            $role = Role::firstOrCreate(
                ['slug' => 'super-admin'],
                [
                    'name' => 'Super Admin',
                    'description' => 'Full system access',
                    'level' => 100,
                ]
            );

            // Attach role to user
            $user->roles()->attach($role->id);

            $this->info('Super admin created successfully!');
            $this->info('Email: ' . $user->email);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed to create admin: ' . $e->getMessage());
            return 1;
        }
    }
}
