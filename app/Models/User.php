<?php

namespace App\Models;

use App\AdminModels\Permissions;
use App\AdminModels\Permissionrole;
use App\AdminModels\Userroles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'customer_status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function role()
    {
        return $this->belongsTo(Userroles::class);
    }
    public function hasPermission($permissionIdOrSlug)
    {
        // dd($permissionIdOrSlug);
        // return true;
        return Permissionrole::where('role_id', $this->role_id)->whereHas("permissions", function($query) use($permissionIdOrSlug){
            return $query->where('id',$permissionIdOrSlug)
                ->orWhere('slug',$permissionIdOrSlug);
        })->first();
        // return $this->role->permissions()->where(function($query) use($permissionIdOrSlug){
        //     return $query->where('id',$permissionIdOrSlug)
        //         ->orWhere('slug',$permissionIdOrSlug);
        // })->first();

    }
}
