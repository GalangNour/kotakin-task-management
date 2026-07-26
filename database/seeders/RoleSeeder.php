<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->firstOrCreate(
            ['name' => 'Administrator'],
            ['is_active' => true, 'permissions' => ['*']]
        );

        Role::query()->firstOrCreate(
            ['name' => 'Member'],
            ['is_active' => true, 'permissions' => ['task.view', 'task.update']]
        );
    }
}
