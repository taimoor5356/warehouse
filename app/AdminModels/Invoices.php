<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\AdminModels\Customers;
use App\AdminModels\InvoiceDetails;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use SoftDeletes;
    protected $table = 'invoices';
    protected $protected = [];
    protected $guarded = [];

    public function Customer()
    {
        return $this->hasOne(Customers::class, 'id', 'customer_id')->withTrashed();
    }

    public function Details()
    {
        return $this->hasMany(InvoiceDetails::class, 'invoice_id', 'id');
    }

    public function orders(){
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    public function invoice_payment()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'id');
    }

    public function brand_data()
    {
        return $this->hasOneThrough(Labels::class, Orders::class, 'id', 'id', 'order_id', 'brand_id');
    }
}
