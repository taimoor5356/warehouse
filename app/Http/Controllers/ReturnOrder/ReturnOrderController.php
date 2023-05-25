<?php

namespace App\Http\Controllers\ReturnOrder;

use App\AdminModels\Customers;
use App\AdminModels\Labels;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\OrderReturnDetail;

class ReturnOrderController extends Controller
{
    public function returnInvoicePdf($id, $download = null)
    {
        $orderData = OrderReturn::where('id', $id)->first()->toArray();
        $customerName = Customers::where('id', $orderData['customer_id'])->first('customer_name')->toArray();
        $brandName = Labels::where('id', $orderData['brand_id'])->first('brand')->toArray();
        $orderProducts = OrderReturnDetail::with('product:id,name')->where('order_return_id', $orderData['id'])->get()->toArray();
        $data = [
            'customerName' => $customerName['customer_name'],
            'brandName' => $brandName['brand'],
            'orderProducts' => $orderProducts,
            'orderData' => $orderData
        ];
        $pdf = PDF::loadView('admin.orders.return_invoice_pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        if ($download == 'download') {
            return $pdf->download($customerName['customer_name'] . '_' . $orderData['order_number'] . '_' . $orderData['created_at'] . '.pdf');
        } else {
            return $pdf->stream($customerName['customer_name'] . '_' . $orderData['order_number'] . '_' . $orderData['created_at'] . '.pdf');
        }
    }
}
