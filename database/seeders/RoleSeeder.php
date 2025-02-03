<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Role::create([
            'name' => 'Teacher',
            'slug' => 'teacher',
            'permissions' => [],
        ]);

        Role::create([
            'name' => 'Student',
            'slug' => 'student',
            'permissions' => [],
        ]);
    }
}
