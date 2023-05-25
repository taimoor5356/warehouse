<?php

namespace App\Models;

use App\AdminModels\Labels;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\Models\CustomerProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProduct extends Model
{
    use HasFactory;
    use Notifiable,SoftDeletes;
    protected $guarded = ['id'];

    public function product() {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }
    public function trashedproduct() {
        return $this->belongsTo(Products::class, 'product_id', 'id')->withTrashed();
    }
    public function customer_product() {
        return $this->belongsTo(CustomerProduct::class, 'product_id', 'product_id');
    }
    public function customer() {
        return $this->belongsTo(Customers::class, 'customer_id', 'id')->withTrashed();
    }
    public function brand() {
        return $this->hasMany(Labels::class, 'customer_id', 'customer_id');
    }
}
