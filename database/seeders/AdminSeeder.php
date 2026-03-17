<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('12345678')]
            );


                $role = Role::firstOrCreate(['name' => 'super', 'guard_name' => 'admin']);


                $perms = Permission::where('guard_name', 'admin')->get();
                if ($perms->isNotEmpty()) {
                    $role->syncPermissions($perms);
                }


                $admin->assignRole($role);

                app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
