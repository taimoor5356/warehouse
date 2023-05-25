<?php

namespace App\AdminModels;

use App\Models\Sku;
use App\Models\SkuProducts;
use App\AdminModels\Products;
use App\Models\SkuOrder;
use App\Models\SkuOrderDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetails extends Model
{
    use SoftDeletes;
    protected $table = 'order_details';
    protected $protected = [];
    protected $guarded = [];

    public function Product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id')->withTrashed();
    }
    public function sku_detail()
    {
        return $this->belongsTo(Sku::class, 'sku_id', 'id')->withTrashed();
    }
    public function sku_order()
    {
        return $this->belongsTo(SkuOrder::class, 'sku_id', 'sku_id', 'order_id', 'order_id');
    }
    public function skuproduct() 
    {
        return $this->hasMany(SkuProducts::class, 'sku_id', 'sku_id')->withTrashed();
    }
    
    public function sku_order_detail() 
    {
        return $this->hasMany(SkuOrderDetails::class, 'order_id', 'order_id', 'sku_id', 'sku_id');
    }
    public function orders() 
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }
    
}
