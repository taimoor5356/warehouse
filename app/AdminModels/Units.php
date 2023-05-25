<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Units extends Model
{
    protected $table = 'product_units';
    public $timestamps = false;
    protected $fillable = ['id', 'name'];


}
