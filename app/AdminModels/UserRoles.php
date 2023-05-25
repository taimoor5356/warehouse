<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class UserRoles extends Model
{
    protected $table = 'user_roles';
    protected $protected = [];

    public function permissions()
    {
        return $this->belongsToMany(Permissionrole::class);
    }
}
