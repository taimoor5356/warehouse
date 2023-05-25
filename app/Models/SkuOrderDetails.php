<?php

namespace App\Models;

use App\Models\SkuOrder;
use App\AdminModels\Products;
use App\Models\CustomerHasProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkuOrderDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function sku_detail()
    {
        return $this->belongsTo(SkuOrder::class, 'sku_order_id', 'id');
    }

    public function labelqty()
    {
        return $this->hasMany(CustomerHasProduct::class, 'product_id', 'product_id');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function customer_product()
    {
        return $this->belongsTo(CustomerHasProduct::class, 'customer_id', 'customer_id', 'brand_id', 'brand_id')->where('product_id', $this->product_id);
    }
    
    public function order() 
    {
        return $this->belongsTo(OrderDetails::class, 'order_id', 'order_id', 'sku_id', 'sku_id');
    }
}
