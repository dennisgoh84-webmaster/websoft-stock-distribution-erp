<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Admin', 'Manager', 'Staff'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
