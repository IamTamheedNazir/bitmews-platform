<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access',
                'level' => 100,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access',
                'level' => 80,
            ],
            [
                'name' => 'Organization',
                'slug' => 'organization',
                'description' => 'Organization account',
                'level' => 50,
            ],
            [
                'name' => 'Creator',
                'slug' => 'creator',
                'description' => 'Content creator',
                'level' => 30,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user',
                'level' => 10,
                'is_default' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Create permissions
        $permissions = [
            // User management
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users'],
            
            // Token management
            ['name' => 'View Tokens', 'slug' => 'tokens.view', 'group' => 'tokens'],
            ['name' => 'Create Tokens', 'slug' => 'tokens.create', 'group' => 'tokens'],
            ['name' => 'Edit Tokens', 'slug' => 'tokens.edit', 'group' => 'tokens'],
            ['name' => 'Delete Tokens', 'slug' => 'tokens.delete', 'group' => 'tokens'],
            
            // Content management
            ['name' => 'View Posts', 'slug' => 'posts.view', 'group' => 'content'],
            ['name' => 'Create Posts', 'slug' => 'posts.create', 'group' => 'content'],
            ['name' => 'Edit Posts', 'slug' => 'posts.edit', 'group' => 'content'],
            ['name' => 'Delete Posts', 'slug' => 'posts.delete', 'group' => 'content'],
            ['name' => 'Moderate Posts', 'slug' => 'posts.moderate', 'group' => 'content'],
            
            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'group' => 'settings'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }
    }
}
