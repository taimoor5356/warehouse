<?php

namespace App\Models;

use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\SkuProducts;
use App\AdminModels\OrderDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sku extends Model
{
    use HasFactory,SoftDeletes;
    protected $protected = [];
    protected $guarded = [];

    public function sku_product()
    {
        return $this->hasMany(SkuProducts::class)->orderBy('id', 'ASC');
    }

    public function brand(){
        return $this->belongsTo(Labels::class,'brand_id');
    }

    public function brand_detail(){
        return $this->belongsTo(Labels::class,'brand_id');
    }

    public function order() {
        return $this->hasMany(Orders::class, 'sku_id');
    }

    public function order_details() {
        return $this->hasMany(OrderDetails::class, 'sku_id');
    }
}
