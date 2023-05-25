<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UpdatePermissionNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permissions = Permission::get();
        foreach ($permissions as $key => $permission) {
            $name = str_replace('_', ' ', $permission->slug);
            $permission->slug = ucwords($name);
            $permission->save();
        }
    }
}
