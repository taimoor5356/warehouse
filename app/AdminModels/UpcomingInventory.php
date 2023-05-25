<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdminModels\Products;


class UpcomingInventory extends Model
{
    protected $table = 'upcoming_inventory';
    protected $guarded = ['id'];

    public function Products()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
