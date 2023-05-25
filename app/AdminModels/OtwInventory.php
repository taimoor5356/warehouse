<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdminModels\Products;


class OtwInventory extends Model
{
    protected $table = 'otw_inventory';
    protected $casts = [
        'date'  => 'date:Y-m-d',
    ];
    protected $guarded = ['id'];
    public function Products()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
