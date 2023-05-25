<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdminModels\Products;


class Inventory extends Model
{
	use Notifiable,SoftDeletes;

    protected $table = 'inventory';
    // public $timestamps = false;
    protected $guarded = [];
    protected $protected = [];
    
    public function Products()
    {
        return $this->hasOne(Products::class, 'id', 'product_id' );
    }

    
}
