<?php 

namespace App\Traits;

trait HasPermission{

    /**
     * @var int|array $permissions
     */
    public function assignPermissions($permissions)
    {
        return $this->permissions()->attach($permissions);
    }

    /**
     * @var int|array $permissions
     */
    public function removePermissions($permissions)
    {
        return $this->permissions()->detach($permissions);
    }

     /**
     * @param string $permission
     */
    public function hasPermission($permission)
    {
        return $this->permissions()->where('slug',$permission)->count();
    }
}