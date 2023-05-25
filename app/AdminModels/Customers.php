<?php

namespace App\AdminModels;

use App\Models\Sku;
use App\Models\CustomerHasSku;
use App\Models\ServiceCharges;
use App\Models\CustomerProduct;
use App\Models\CustomerHasProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    public $timestamps = false;
    protected $protected = [];
    protected $guarded = [];

    public function service_charges()
    {
        return $this->hasOne(ServiceCharges::class, 'customer_id');
    }

    public function brands()
    {
        return $this->hasMany(Labels::class, 'customer_id');
    }

    public function product()
    {
        return $this->hasMany(CustomerHasProduct::class, 'id', 'customer_id');
    }

    public function customer_product()
    {
        return $this->hasMany(CustomerProduct::class, 'customer_id');
    }

    public function sku()
    {
        return $this->hasMany(CustomerHasSku::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'customer_id', 'id');
    }
    
}
