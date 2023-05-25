<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CreatePermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permissions = array('pin_code', 'setting_update');
        for($i = 0; $i < count($permissions); $i++) {
            Permission::create([
                'name' => $permissions[$i],
                'guard_name' => 'web',
                'slug' => $permissions[$i]
            ]);
        }
    }
}
