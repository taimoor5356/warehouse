<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkuOrder extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function sku_product()
    {
        return $this->hasMany(SkuOrderDetails::class, 'sku_order_id', 'id');
    }
}
