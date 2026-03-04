<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with branch management access',
            ],
            [
                'name' => 'Sales',
                'slug' => 'sales',
                'description' => 'Sales staff with order and inventory access',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
