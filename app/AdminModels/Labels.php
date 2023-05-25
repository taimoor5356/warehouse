<?php

namespace App\AdminModels;

use App\Models\Sku;
use App\AdminModels\Customers;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labels extends Model
{
	use Notifiable,SoftDeletes;
    protected $table = 'labels';
    public $timestamps = false;
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customers::class);
    }

    public function sku()
    {
        return $this->hasMany(Sku::class, 'brand_id');
    }

    public function cust_has_prod() {
        return $this->hasMany(CustomerHasProduct::class, 'brand_id', 'id');
    }
    
}
