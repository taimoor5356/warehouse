<?php

namespace App\AdminModels;

use App\AdminModels\Invoices;
use App\AdminModels\Customers;
use App\AdminModels\OrderDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;
    protected $table = 'orders';
    protected $protected = [];
    protected $guarded = [];

    public function Customer()
    {
        return $this->hasOne(Customers::class, 'id', 'customer_id');
    }

    public function brand()
    {
        return $this->hasOne(Labels::class, 'id', 'brand_id');
    }

    public function Details()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function orders(){
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    public function invoice() {
        return $this->hasOne(Invoices::class, 'order_id', 'id');
    }
}
