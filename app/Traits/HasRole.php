<?php 

namespace App\Traits;

trait HasRole{

    /**
     * @param array $name
     */
    public function hasRole(array $name=[])
    {
        return $this->role()->whereIn('name',$name)->count();
    }

    /**
     * @param int|array $roleIds
     */
    public function assignRole($roleIds)
    {
        return $this->role()->attach($roleIds);
    }

    /**
     * @param int|array $roleId
     */
    public function removeRole($roleIds)
    {
        return $this->role()->detach($roleIds);
    }

     /**
     * @param string $permission
     */
    public function hasPermissionTo($permission)
    {
        return $this->role()->whereHas('permissions',function($query) use($permission){
            return $query->where('slug',$permission);
        })
        ->count();
    }

}