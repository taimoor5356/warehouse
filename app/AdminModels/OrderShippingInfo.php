<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderShippingInfo extends Model
{
    protected $table = 'order_shipping_info';
    public $timestamps = false;
    protected $protected = [];
    protected $guarded = ['id'];

    
}
