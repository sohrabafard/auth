<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Create Roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

// Create Permissions
        $read = Permission::create(['name' => 'read']);
        $write = Permission::create(['name' => 'write']);

// Assign Permissions to Roles
        $admin->givePermissionTo($read);
        $admin->givePermissionTo($write);
        $user->givePermissionTo($read);
    }
}
