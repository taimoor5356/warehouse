<?php

namespace App\Models;

use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\AdminModels\OrderDetails;
use App\Models\OrderReturnDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime:m-d-Y',
    ];
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id')->withTrashed();
    }
    public function brand()
    {
        return $this->belongsTo(Labels::class, 'brand_id', 'id')->withTrashed();
    }
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id')->withTrashed();
    }
    public function orderDetails()
    {
        return $this->belongsTo(OrderDetails::class, 'order_id', 'order_id')->withTrashed();
    }
    public function orderReturnDetails()
    {
        return $this->hasMany(OrderReturnDetail::class, 'order_return_id', 'id')->withTrashed();
    }
    public function orderReturnedDetails()
    {
        return $this->belongsTo(OrderReturnDetail::class, 'order_return_id', 'id')->withTrashed();
    }
    public function order_return_details_with_trashed()
    {
        return $this->hasMany(OrderReturnDetail::class, 'order_return_id', 'id')->withTrashed();
    }
    public function order_return_details()
    {
        return $this->hasMany(OrderReturnDetail::class, 'order_return_id', 'id')->where('deleted_at', NULL)->orderBy('id', 'ASC')->groupBy('product_id');
    }
}
