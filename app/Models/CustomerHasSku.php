<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerHasSku extends Model
{
    use HasFactory;
    use Notifiable,SoftDeletes;
    protected $guarded = ['id'];

    public function skus() {
        return $this->belongsTo(Sku::class);
    }

    public function skuproduct() {
        return $this->belongsTo(SkuProducts::class, 'sku_id', 'sku_id');
    }

    public function brands() {
        return $this->belongsTo(Labels::class);
    }

    public function cutomers() {
        return $this->belongsTo(Customers::class);
    }

}

// @foreach($period as $pk => $p)
// @php
//     $dt = $p->toDateTimeString();
//     $invenhistory = App\AdminModels\InventoryHistory::orderBy('date', 'ASC')->where('product_id', $productId)->get();
// @endphp
// @foreach ($invenhistory as $prod)
//     @php
//         $total += $prod->qty;   
//     @endphp
//     <tr>
//         @php
//         $date = '';
//         $name = '';
//         $qty = '';
//             $product = App\AdminModels\Products::where('id', $prod->product_id)->first();
//             $date = $dt;
//             $qty = $prod->qty;
//             if (isset($product)) {
//                 $name = $product->name;
//             }
//         @endphp
//         <td>{{ $date }}</td>
//         <td>{{ $name }}</td>
//         <td>{{ $qty }}</td>
//         @foreach($customers as $customer)
//             @php $sum = 0; @endphp
//             <td>
//                 @php
//                     if (App\Models\CustomerProduct::where('customer_id', $customer->id)->where('product_id', $prod->product_id)->exists()) {
//                         $customerhassku = App\Models\CustomerHasSku::where('customer_id', $customer->id)->get();
//                         foreach ($customerhassku as $cskukey => $chassku) {
//                             $skuproduct = App\Models\SkuProducts::where('sku_id', $chassku->sku_id)->where('product_id', $prod->product_id)->first();
//                             if (isset($skuproduct)) {
//                                 $customerOrder = App\AdminModels\OrderDetails::where('sku_id', $skuproduct->sku_id)->whereDate('created_at', '=', date('Y-m-d', strtotime($prod->created_at)))->get();
//                                 // dd($customerOrder);
//                                 foreach ($customerOrder as $cust_order) {
//                                     $sum += $cust_order->qty;
//                                 }
//                             }
//                         }
                        
//                     }   
//                 @endphp
//                 {{ $sum }}
//             </td>
//         @endforeach
//         <td>{{ $total }}</td>
//     </tr>
// @endforeach
// @endforeach