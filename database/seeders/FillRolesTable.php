<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class FillRolesTable extends Seeder
{
    public function run(): void
    {
        $guard = 'admin';

        $roles = [
            [
                'name'         => 'super',
                'display_name' => 'Super Admin',
            ],
            [
                'name'         => 'admin',
                'display_name' => 'Admin',
            ],


        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(
                ['guard_name' => $guard, 'name' => $data['name']],
                ['display_name' => $data['display_name']]
            );
        }
    }
}
