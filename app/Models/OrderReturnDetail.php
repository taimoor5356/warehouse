<?php

namespace App\Models;

use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\OrderReturn;
use App\AdminModels\Products;
use App\AdminModels\OrderDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturnDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    
    public function orderReturn()
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id', 'id')->withTrashed();
    }
    
    public function brand()
    {
        return $this->belongsTo(Labels::class, 'brand_id', 'id')->withTrashed();
    }
    
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id')->withTrashed();
    }
}
