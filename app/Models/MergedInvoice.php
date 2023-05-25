<?php

namespace App\Models;

use App\AdminModels\Customers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MergedInvoice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // protected $fillable = [
    //     'id',
    //     'invoice_ids',
    //     'order_first_date',
    //     'order_last_date',
    //     'inv_nos',
    //     'customer_id',
    //     'total_cost',
    //     'label_qty',
    //     'pick_qty',
    //     'pack_qty',
    //     'pack_pack_flat_qty',
    //     'mailer_qty',
    //     'postage_qty',
    //     'label_charges',
    //     'pick_charges',
    //     'pack_charges',
    //     'pick_pack_flat_charges',
    //     //
    //     'mailer_charges',
    //     'postage_charges',
    //     //
    //     'label_unit_cost',
    //     'pick_unit_cost',
    //     'pack_unit_cost',
    //     'pick_pack_flat_unit_cost',
    //     'mailer_unit_cost',
    // ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
}
