<?php

namespace App\Models;

use App\AdminModels\Invoices;
use App\AdminModels\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicesMerged extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function merged_invoice()
    {
        return $this->belongsTo(MergedInvoice::class, 'merged_invoice_id', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'invoice_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }
    public function sku_order_detail()
    {
        return $this->belongsTo(SkuOrderDetails::class, 'order_id', 'order_id', 'product_id', 'product_id');
    }
}
