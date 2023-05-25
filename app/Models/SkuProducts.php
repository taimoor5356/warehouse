<?php

namespace App\Models;

use App\Models\Sku;
use App\AdminModels\Orders;
use App\AdminModels\Products;
use App\AdminModels\OrderDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SkuProducts extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function labelqty()
    {
        return $this->HasMany(CustomerHasProduct::class, 'product_id', 'product_id');
    }

    public function customer_product()
    {
        return $this->belongsTo(CustomerProduct::class, 'product_id', 'product_id');
    }

    public function order_details() {
        return $this->hasMany(OrderDetails::class, 'sku_id', 'sku_id');
    }

    // public function order() {
    //     return $this->hasMany(Orders::class, 'sku_id', 'sku_id');
    // }
}
