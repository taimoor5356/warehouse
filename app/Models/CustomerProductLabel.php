<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProductLabel extends Model
{
    use HasFactory;
    use Notifiable,SoftDeletes;
    protected $guarded = ['id'];
}
