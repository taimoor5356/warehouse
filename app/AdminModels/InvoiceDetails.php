<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\AdminModels\Products;

class InvoiceDetails extends Model
{
    protected $table = 'invoice_details';
    public $timestamps = false;
    protected $protected = [];
    protected $guarded = [];

    public function Product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
