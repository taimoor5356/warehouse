<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MakeAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $role = Role::create([
        //     'name' => 'admin',
        //     'guard_name' => 'web',
        //     'is_active' => '1'
        // ]);
        $user = User::find(1);
        $role = Role::find(1);
        $user->assignRole('admin');
        $permissions = Permission::get();
        $role->syncPermissions($permissions);
    }
}
