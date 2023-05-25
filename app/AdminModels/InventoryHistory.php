<?php

namespace App\AdminModels;

use App\AdminModels\Products;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryHistory extends Model
{
	use Notifiable,SoftDeletes;
    protected $table = 'inventory_history';
    protected $protected = [];
    protected $guarded = [];
    

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    
}
