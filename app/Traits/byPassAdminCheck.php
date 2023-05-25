<?php 

namespace App\Traits;

use App\Models\User;

trait ByPassAdminCheck{

    public function before($user)
    {
        if ( $user->hasRole([User::IS_ADMIN]) ){
            return true;
        }
    }

}