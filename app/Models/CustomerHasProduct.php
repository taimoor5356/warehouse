<?php

namespace App\Models;

use App\AdminModels\Labels;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerHasProduct extends Model
{
    use HasFactory;
    use Notifiable,SoftDeletes;

    protected $guarded = ['id'];

    public function products() {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function brands() {
        return $this->belongsTo(Labels::class, 'brand_id', 'id');
    }

    public function getcustomers() {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function cutomers() {
        return $this->belongsTo(Customers::class);
    }

    public function skuproduct() {
        return $this->belongsTo(SkuProducts::class);
    }
    
}
