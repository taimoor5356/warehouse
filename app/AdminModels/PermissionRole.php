<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class PermissionRole extends Model
{
    protected $table = 'permission_role';
    public $timestamps = false;
    protected $protected = [];

    public function permissions()
    {
        return $this->hasOne(Permissions::class, "id", "permission_id");
    }
}
