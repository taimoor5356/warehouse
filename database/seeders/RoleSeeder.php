<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\AdminModels\Userroles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Userroles::create(['name'=>'User','is_active'=>1]);
        // Userroles::create(['name'=>'Administrator','is_active'=>1]);
        // Userroles::create(['name'=>'Manager','is_active'=>1]);
        if (!(Role::where('name', 'admin')->exists())) {
            Role::create(['name' => 'admin', 'guard_name' => 'web', 'is_active' => 1]);
        }
        if (!(Role::where('name', 'user')->exists())) {
            Role::create(['name' => 'user', 'guard_name' => 'web', 'is_active' => 1]);
        }
        if (!(Role::where('name', 'administrator')->exists())) {
            Role::create(['name' => 'administrator', 'guard_name' => 'web', 'is_active' => 1]);
        }
        if (!(Role::where('name', 'manager')->exists())) {
            Role::create(['name' => 'manager', 'guard_name' => 'web', 'is_active' => 1]);
        }
        if (!(Role::where('name', 'customer')->exists())) {
            Role::create(['name' => 'customer', 'guard_name' => 'web', 'is_active' => 1]);
        }
    }
}
