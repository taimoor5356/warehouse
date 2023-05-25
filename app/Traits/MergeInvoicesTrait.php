<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\SkuOrder;
use Carbon\CarbonPeriod;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Jobs\ProductReport;
use Illuminate\Http\Request;
use App\AdminModels\Invoices;
use App\AdminModels\Products;
use App\Models\MergedInvoice;
use App\AdminModels\Inventory;
use App\Models\InvoicesMerged;
use App\Models\CustomerProduct;
use App\Models\OrderReturnDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

trait MergeInvoicesTrait
{
    public function merge_invoice($request)
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('optimize');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '2048M');
            if ((count(array_unique($request['customerIds'])) === 1)) {
              DB::beginTransaction();
              try {
                $data = $request['customerIds'];
                $brandName = '';
                $totalCost = 0;
                $labelQty = 0;
                $pickQty = 0;
                $packQty = 0;
                $pickPackFlatQty = 0;
                $mailerQty = 0;
                $postageQty = 0;
                $mailer_Charges = 0;
                $postage_Charges = 0;
                $mergedInvoices = array();
                $keys = array(
                  'invoice_id',
                  'inv_no',
                  'order_id',
                  'invoice_number',
                  'customer_id',
                  'brand_name',
                  'total_cost',
                  'label_qty',
                  'pick_qty',
                  'pack_qty',
                  'pick_pack_flat_qty',
                  'mailer_qty',
                  'postage_qty',
                  'mailer_charges',
                  'postage_charges',
                  'invoice_ordered_detail',
                  'label_unit_cost',
                  'pick_unit_cost',
                  'pack_unit_cost',
                  'pick_pack_flat_unit_cost',
                  'mailer_unit_cost',
                  'return_charges',
                  'return_qty',
                  'returned_product_total'
                );
                $labelUnitCharges = '';
                $pickUnitCharges = '';
                $packUnitCharges = '';
                $pickPackFlatUnitCharges = '';
                $mailerUnitCharges = '';
                $label_Qty = 0;
                $pick_Qty = 0;
                $pack_Qty = 0;
                $mailer_Qty = 0;
                $postage_Qty = 0;
                $pickPackFlat_Qty = 0;
                if (count($data) > 52) {
                  return response()->json(['message' => false, 'data' => 'Only 52 invoices are allowed to merge at once']);
                  dd('Only 50 invoices are allowed');
                }
                for ($i=0; $i < count($data); $i++) {
                  $order = Orders::where('merged', 0)->orderBy('id', 'ASC')->with('Details.sku_order.sku_product')->where('id', $request['orderIds'][$i])->where('status', '!=', 4)->first();
                  $orderCustomerServiceCharges = json_decode($order->customer_service_charges);
                  $getCustomerID = $order->customer_id;
                  $is_active = 1;
                    $checkIterations = array();
                    $checkIterationskeys = array('unit', 'iteration', 'cost');
                    if (isset($order)) {
                      $brand = Labels::where('id', $order->brand_id)->first();
                      if (isset($brand)) {
                        $brandName = $brand->brand;
                      }
                      $totalCost = $order->total_cost;
                      $labelQty = $order->labelqty;
                      $pickQty = $order->pickqty;
                      $packQty = $order->packqty;
                      $mailerQty = $order->mailerqty;
                      $postageQty = $order->postageqty;
                      $pickPackFlatQty = $order->pick_pack_flat_qty;
                      // Order Details
                      $orderDetails = $order->Details;
                      $orderDetailArray = array();
                      $orderDetailKeys = array(
                        'sku_id',
                        'sku_order_qty',
                        'sku_purchasing_cost',
                        'sku_selling_cost',
                        'label_charges',
                        'pick_charges',
                        'pack_charges',
                        'pick_pack_flat_charges',
                        // 'mailer_charges',
                        // 'postage_charges',
                        'products'
                      );
                      foreach ($order->Details as $detail) {
                        $skuOrderedQty = 0;
                        $skuPurchasingCost = 0;
                        $skuSellingCost = 0;
                        $labelPrice = '0.00';
                        $pickPrice = '0.00';
                        $packPrice = '0.00';
                        $pickPackFlatPrice = '0.00';
                        if (isset($detail)) {
                          if ($detail->qty > 0) {
                            $skuOrderedQty = $detail->qty;
                            $skuPurchasingCost = $detail->sku_purchasing_cost;
                            $skuSellingCost = $detail->sku_selling_cost;
                            $serviceCharges = json_decode($detail->service_charges_detail);
                            $mailerPrice = 0;
                            $postagePrice = 0;
                            foreach ($serviceCharges as $charge) {
                                if ($charge->slug == 'labels_price') {
                                  $labelPrice = $charge->price;
                                }
                                if ($charge->slug == 'pick_price') {
                                  $pickPrice = $charge->price;
                                }
                                if ($charge->slug == 'pack_price') {
                                  $packPrice = $charge->price;
                                }
                                if ($charge->slug == 'mailer_price') {
                                  $mailerPrice = $charge->price;
                                }
                                if ($charge->slug == 'postage_price') {
                                  $postagePrice = $charge->price;
                                }
                            }
                            $pickPackFlatPrice = $order->pick_pack_flat_price;
                          }
                          $products = array();
                          $productKeys = array('product_id', 'product_name', 'product_qty', 'seller_cost_status', 'selling_cost', 'product_price', 'invoice_id', 'order_id', 'invoice_number', 'label_unit_cost', 'pick_unit_cost', 'pack_unit_cost', 'pick_pack_flat_unit_cost', 'mailer_unit_cost', 'mailer_unit_qty', 'postage_unit_qty', 'product_return_qty', 'product_return_cost');
                          
                          $productQty = 0;
                          $prodSellingRate = 0;
                          $skuOrder = SkuOrder::with('sku_product')->where('sku_id', $detail->sku_id)->where('order_id', $detail->order_id)->first();
                          $sellerCostStatus = 0;
                          foreach ($skuOrder->sku_product as $skuProduct) {
                            $getCustomerProduct = CustomerProduct::where('customer_id', $getCustomerID)->where('product_id', $skuProduct->product_id)->first();
                            if (isset($getCustomerProduct)) {
                              $is_active = $getCustomerProduct->is_active;
                              $sellerCostStatus = $skuProduct->seller_cost_status;
                            }
                            $productName = '';
                            $product = Products::where('id', $skuProduct->product_id)->first();
                            if (isset($product)) {
                              $productName = $product->name;
                            }
                            $productQty = $skuOrderedQty * $skuProduct->quantity;
                            $prodSellingRate = $skuProduct->selling_cost;
        
                            $unitLabel = 0;
                            $unitPick = 0;
                            $unitPack = 0;
                            $unitPickPackFlat = 0;
                            $unitMailer = 0;
                            $unitMailerQty = 0;
                            $unitPostageQty = 0;
        
                            if ($detail->qty > 0) {
                              if ($is_active == 0) {
                                $unitLabel = $skuProduct->label * $detail->qty;
                              } else {
                                $unitLabel = 0.00;
                              }
                              $unitPick = $skuProduct->pick * $detail->qty;
                              $unitPack = $skuProduct->pack * $detail->qty;
                              $unitPickPackFlat = $order->pick_pack_flat_price;
                              $unitMailer = $skuOrder->mailer_cost;
                              $unitMailerQty = $order->mailerqty;
                              $unitPostageQty = $order->postageqty;
                            }
                            $returnOrders = DB::table('order_returns')
                                            ->where('customer_id', $order->customer_id)
                                            ->where('brand_id', $order->brand_id)
                                            ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
                                            ->where('status', '!=', 2)
                                            ->where('status', '!=', 3)
                                            ->get();
                            $productReturnQty = 0;
                            $productReturnCost = 0;
                            if (count($returnOrders) > 0) {
                              foreach ($returnOrders as $ROkey => $returnOrder) {
                                if (isset($returnOrder)) {
                                  $returnOrderDetails = DB::table('order_return_details')->where('merged', '=', 0)->where('order_return_id', $returnOrder->id)->get();
                                  if (count($returnOrderDetails) > 0) {
                                    foreach ($returnOrderDetails as $RODkey => $returnOrderDetail) {
                                      if (isset($returnOrderDetail)) {
                                        if ($detail->qty > 0) {
                                          if ($returnOrderDetail->product_id == $skuProduct->product_id) {
                                            $productReturnQty += $returnOrderDetail->qty;
                                            $productReturnCost += $returnOrderDetail->selling_cost;
                                            OrderReturnDetail::where('merged', 0)->where('order_return_id', $returnOrder->id)->where('product_id', $skuProduct->product_id)->update([
                                              'merged' => 1
                                            ]);
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                            array_push($products, array_combine($productKeys, [
                              $skuProduct->product_id,
                              $productName,
                              $productQty,
                              $sellerCostStatus,
                              $prodSellingRate,
                              ($skuProduct->selling_cost) * ($productQty),
                              $request['invoiceIds'][$i],
                              $request['orderIds'][$i],
                              $request['invoiceNumbers'][$i],
                              $unitLabel,
                              $unitPick,
                              $unitPack,
                              !is_null($unitPickPackFlat) ? $unitPickPackFlat : 0,
                              $unitMailer,
                              $unitMailerQty,
                              $unitPostageQty,
                              $productReturnQty,
                              $productReturnCost,
                            ]));
                          }
                        }
                        array_push($orderDetailArray, array_combine($orderDetailKeys, [
                          $detail->sku_id,
                          $skuOrderedQty,
                          $skuPurchasingCost,
                          $skuSellingCost,
                          $labelPrice,
                          $pickPrice,
                          $packPrice,
                          !is_null($pickPackFlatPrice) ? $pickPackFlatPrice : 0,
                          // $mailerPrice,
                          // $postagePrice,
                          $products,
                        ]));
                      }
                      $label_Qty += $order->labelqty;
                      $pick_Qty += $order->pickqty;
                      $pack_Qty += $order->packqty;
                      $pickPackFlat_Qty += $order->pick_pack_flat_qty;
                      $mailer_Qty += $order->mailerqty;
                      $postage_Qty += $order->postageqty;
                    }
                    $returnOrders = DB::table('order_returns')
                                    ->where('customer_id', $order->customer_id)
                                    ->where('brand_id', $order->brand_id)
                                    ->whereDate('created_at', $order->created_at)
                                    ->where('status', '!=', 2)
                                    ->where('status', '!=', 3);
                    $checkReturnOrders = $returnOrders->first();
                  array_push($mergedInvoices, array_combine($keys, [
                    $request['invoiceIds'][$i],
                    $request['invNumbers'][$i],
                    $request['orderIds'][$i],
                    $request['invoiceNumbers'][$i],
                    $request['customerIds'][$i],
                    $brandName,
                    $totalCost,
                    $labelQty,
                    $pickQty,
                    $packQty,
                    $pickPackFlatQty,
                    $mailerQty,
                    $postageQty,
                    $mailerPrice,
                    $postagePrice,
                    $orderDetailArray,
                    $orderCustomerServiceCharges->labels,
                    $orderCustomerServiceCharges->pick,
                    $orderCustomerServiceCharges->pack,
                    $orderCustomerServiceCharges->pick_pack_flat,
                    $orderCustomerServiceCharges->mailer,
                    isset($checkReturnOrders) ? $checkReturnOrders->cust_return_charges : '0',
                    $returnOrders->count(),
                    $returnOrders->sum('total_selling_cost')
                  ]));
                  $getOrder = Orders::orderBy('id', 'ASC')->where('merged', 0)->where('id', $request['orderIds'][$i])->where('status', '!=', 4)->first();
                  if (isset($getOrder)) {
                    $unitCharges = json_decode($order->customer_service_charges);
                    if ($i == (count($data)-1)) {
                      $labelUnitCharges .= $unitCharges->labels;
                      $pickUnitCharges .= $unitCharges->pick;
                      $packUnitCharges .= $unitCharges->pack;
                      $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat;
                      $mailerUnitCharges .= $unitCharges->mailer;
                    } else {
                      $labelUnitCharges .= $unitCharges->labels.',';
                      $pickUnitCharges .= $unitCharges->pick.',';
                      $packUnitCharges .= $unitCharges->pack.',';
                      $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat.',';
                      $mailerUnitCharges .= $unitCharges->mailer.',';
                    }
                  }
                  Orders::where('merged', 0)->where('id', $request['orderIds'][$i])->update(['merged' => 1]);
                }
                $customer_ID = 0;
                $total_Cost = 0;
                
                $label_Charges = 0;
                $pick_Charges = 0;
                $pack_Charges = 0;
                $pick_pack_flat_Charges = 0;
                $product_Name = '';
                $product_Qty = 0;
                $returnServiceCharges = 0;
                $totalReturnQty = 0;
                $getLastInvNoOfCustomer = MergedInvoice::orderBy('created_at', 'DESC')->where('customer_id', $mergedInvoices[0]['customer_id'])->first();
                $custInvNo = 1;
                if (isset($getLastInvNoOfCustomer)) {
                  $custInvNo = $getLastInvNoOfCustomer->inv_nos + 1;
                }
                for ($j=0; $j < count($mergedInvoices); $j++) {
                  $customer_ID = $mergedInvoices[0]['customer_id'];
                  $total_Cost = $total_Cost + $mergedInvoices[$j]['total_cost'];
                  $mailer_Charges = $mailer_Charges + $mergedInvoices[$j]['mailer_charges'];
                  $postage_Charges = $postage_Charges + $mergedInvoices[$j]['postage_charges'];
                  $returnServiceCharges = $mergedInvoices[$j]['return_charges'];
                  $totalReturnQty = $mergedInvoices[$j]['return_qty'];
                  $returnedProductTotal = $mergedInvoices[$j]['returned_product_total'];
                  for ($k=0; $k < count($mergedInvoices[$j]['invoice_ordered_detail']); $k++) { 
                    $label_Charges = $label_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['label_charges'];
                    $pick_Charges = $pick_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pick_charges'];
                    $pack_Charges = $pack_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pack_charges'];
                    $pick_pack_flat_Charges = $pick_pack_flat_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pick_pack_flat_charges'];
                    for ($m=0; $m < count($mergedInvoices[$j]['invoice_ordered_detail'][$k]['products']); $m++) { 
                      if ($mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_qty'] > 0) {
                        $invoicesMerged = InvoicesMerged::create([
                          'product_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_id'],
                          'product_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_qty'],
                          'selling_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['selling_cost'],
                          'product_price' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['seller_cost_status'] == 1 ? $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_price'] : 0.00,
                          'invoice_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['invoice_id'],
                          'inv_no' => $custInvNo,
                          'order_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['order_id'],
                          'invoice_number' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['invoice_number'],
                          //
                          'label_unit_cost' =>$mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['seller_cost_status'] == 1 ? $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['label_unit_cost'] : 0.00,
                          'pick_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pick_unit_cost'],
                          'pack_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pack_unit_cost'],
                          'pick_pack_flat_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pick_pack_flat_unit_cost'],
                          'mailer_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['mailer_unit_cost'],
                          'mailer_unit_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['mailer_unit_qty'],
                          'product_return_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_return_qty'],
                          'product_return_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_return_cost'],
                          //
                        ]);
                      }
                    }
                  }
                }
                $mergedInvoice = MergedInvoice::create([
                  'invoice_ids' => implode(',', $request['orderIds']),
                  'customer_id' => $customer_ID,
                  'total_cost' => $total_Cost,
                  'label_qty' => $label_Qty,
                  'pick_qty' => $pick_Qty,
                  'pack_qty' => $pack_Qty,
                  'flat_pick_pack_qty' => $pickPackFlat_Qty,
                  'mailer_qty' => $mailer_Qty,
                  'postage_qty' => $postage_Qty,
                  'label_charges' => $label_Charges,
                  'pick_charges' => $pick_Charges,
                  'pack_charges' => $pack_Charges,
                  'pick_pack_flat_charges' => $pick_pack_flat_Charges,
                  //
                  'mailer_charges' => $mailer_Charges,
                  'postage_charges' => $postage_Charges,
                  //
                  'label_unit_cost' => $labelUnitCharges,
                  'pick_unit_cost' => $pickUnitCharges,
                  'pack_unit_cost' => $packUnitCharges,
                  'pick_pack_flat_unit_cost' => $pickPackFlatUnitCharges,
                  'mailer_unit_cost' => $mailerUnitCharges,
                  'return_charges' => $returnServiceCharges,
                  'return_qty' => $totalReturnQty,
                  'returned_product_total' => $returnedProductTotal
                  
                ]);
                $mergedInvoice->inv_nos = $custInvNo;
                $mergedInvoice->save();
                $productMerged = InvoicesMerged::where('merged_invoice_id', '=', NULL)->update([
                  'merged_invoice_id' => $mergedInvoice->id,
                  'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                ]);
                DB::commit();
                return response()->json(['message' => true, 'data' => 'Merged Successfully']);
              }
              catch(\Exception $e)
              {
                dd($e);
                DB::rollback();
                return view('admin.server_error');
                return response()->json(['message' => false, 'data' => 'Something went wrong']);
              }
            } else {
              return response()->json(['message' => false, 'data' => 'Customer should be same']);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function merge_customer_all_invoices($request)
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('optimize');
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        $customer_id = $request['customer_id'];
        $customerOrders = $request['all_customer_orders'];
        if (!empty($customer_id)) {
            DB::beginTransaction();
            try {
                if (count($customerOrders) > 0) {
                    $brandName = '';
                    $totalCost = 0;
                    $labelQty = 0;
                    $pickQty = 0;
                    $packQty = 0;
                    $pickPackFlatQty = 0;
                    $mailerQty = 0;
                    $postageQty = 0;
                    $mailer_Charges = 0;
                    $postage_Charges = 0;
                    $mergedInvoices = array();
                    $keys = array(
                        'invoice_id',
                        'inv_no',
                        'order_id',
                        'invoice_number',
                        'customer_id',
                        'brand_name',
                        'total_cost',
                        'label_qty',
                        'pick_qty',
                        'pack_qty',
                        'pick_pack_flat_qty',
                        'mailer_qty',
                        'postage_qty',
                        'mailer_charges',
                        'postage_charges',
                        'invoice_ordered_detail',
                        'label_unit_cost',
                        'pick_unit_cost',
                        'pack_unit_cost',
                        'pick_pack_flat_unit_cost',
                        'mailer_unit_cost',
                        'return_charges',
                        'return_qty',
                        'returned_product_total'
                    );
                    $labelUnitCharges = '';
                    $pickUnitCharges = '';
                    $packUnitCharges = '';
                    $pickPackFlatUnitCharges = '';
                    $mailerUnitCharges = '';
                    $label_Qty = 0;
                    $pick_Qty = 0;
                    $pack_Qty = 0;
                    $pickPackFlat_Qty = 0;
                    $mailer_Qty = 0;
                    $postage_Qty = 0;
                    for ($i=0; $i < count($customerOrders); $i++) {
                        $getInvoiceData = Invoices::where('order_id', $customerOrders[$i])->first();
                        // if (isset($getInvoiceData)) {
                        $order = Orders::where('merged', 0)->orderBy('id', 'ASC')->with('Details.sku_order.sku_product')->where('status', '!=', 4)->where('id', $customerOrders[$i])->first();
                        $orderCustomerServiceCharges = json_decode($order->customer_service_charges);
                        $getCustomerID = $order->customer_id;
                        $is_active = 1;
                        // foreach ($orders as $order) {
                            $checkIterations = array();
                            $checkIterationskeys = array('unit', 'iteration', 'cost');
                            if (isset($order)) {
                            $brand = Labels::where('id', $order->brand_id)->first();
                            if (isset($brand)) {
                                $brandName = $brand->brand;
                            }
                            $totalCost = $order->total_cost;
                            $labelQty = $order->labelqty;
                            $pickQty = $order->pickqty;
                            $packQty = $order->packqty;
                            $pickPackFlatQty = $order->pick_pack_flat_qty;
                            $mailerQty = $order->mailerqty;
                            $postageQty = $order->postageqty;
                            // Order Details
                            $orderDetails = $order->Details;
                            $orderDetailArray = array();
                            $orderDetailKeys = array(
                                'sku_id',
                                'sku_order_qty',
                                'sku_purchasing_cost',
                                'sku_selling_cost',
                                'label_charges',
                                'pick_charges',
                                'pack_charges',
                                'pick_pack_flat_charges',
                                // 'mailer_charges',
                                // 'postage_charges',
                                'products'
                            );
                            foreach ($order->Details as $detail) {
                                $skuOrderedQty = 0;
                                $skuPurchasingCost = 0;
                                $skuSellingCost = 0;
                                $labelPrice = '0.00';
                                $pickPrice = '0.00';
                                $packPrice = '0.00';
                                $pickPackFlatPrice = '0.00';
                                if (isset($detail)) {
                                  if ($detail->qty > 0) {
                                      $skuOrderedQty = $detail->qty;
                                      $skuPurchasingCost = $detail->sku_purchasing_cost;
                                      $skuSellingCost = $detail->sku_selling_cost;
                                      $serviceCharges = json_decode($detail->service_charges_detail);
                                      $mailerPrice = 0;
                                      $postagePrice = 0;
                                      foreach ($serviceCharges as $charge) {
                                          if ($charge->slug == 'labels_price') {
                                          $labelPrice = $charge->price;
                                          }
                                          if ($charge->slug == 'pick_price') {
                                          $pickPrice = $charge->price;
                                          }
                                          if ($charge->slug == 'pack_price') {
                                          $packPrice = $charge->price;
                                          }
                                          if ($charge->slug == 'mailer_price') {
                                          $mailerPrice = $charge->price;
                                          }
                                          if ($charge->slug == 'postage_price') {
                                          $postagePrice = $charge->price;
                                          }
                                      }
                                      $pickPackFlatPrice = $order->pick_pack_flat_price;
                                  }
                                  $products = array();
                                  $productKeys = array('product_id', 'product_name', 'product_qty', 'seller_cost_status', 'selling_cost', 'product_price', 'invoice_id', 'order_id', 'invoice_number', 'label_unit_cost', 'pick_unit_cost', 'pack_unit_cost', 'pick_pack_flat_unit_cost', 'mailer_unit_cost', 'mailer_unit_qty', 'postage_unit_qty', 'product_return_qty', 'product_return_cost');
                                  
                                  $productQty = 0;
                                  $prodSellingRate = 0;
                                  $skuOrder = SkuOrder::with('sku_product')->where('sku_id', $detail->sku_id)->where('order_id', $detail->order_id)->first();
                                  $sellerCostStatus = 0;
                                  foreach ($skuOrder->sku_product as $skuProduct) {
                                    $getCustomerProduct = CustomerProduct::where('customer_id', $getCustomerID)->where('product_id', $skuProduct->product_id)->first();
                                    if (isset($getCustomerProduct)) {
                                    $is_active = $getCustomerProduct->is_active;
                                    $sellerCostStatus = $skuProduct->seller_cost_status;
                                    }
                                    $productName = '';
                                    $product = Products::where('id', $skuProduct->product_id)->first();
                                    if (isset($product)) {
                                    $productName = $product->name;
                                    }
                                    $productQty = $skuOrderedQty * $skuProduct->quantity;
                                    $prodSellingRate = $skuProduct->selling_cost;
                
                                    $unitLabel = 0;
                                    $unitPick = 0;
                                    $unitPack = 0;
                                    $unitPickPackFlat = 0;
                                    $unitMailer = 0;
                                    $unitMailerQty = 0;
                                    $unitPostageQty = 0;
                
                                    if ($detail->qty > 0) {
                                      if ($is_active == 0) {
                                          $unitLabel = $orderCustomerServiceCharges->labels * $detail->qty;
                                      } else {
                                          $unitLabel = 0.00;
                                      }
                                      $unitPick = $skuProduct->pick * $detail->qty;
                                      $unitPack = $skuProduct->pack * $detail->qty;
                                      $unitPickPackFlat = $order->pick_pack_flat_price;
                                      $unitMailer = $skuOrder->mailer_cost;
                                      $unitMailerQty = $order->mailerqty;
                                      $unitPostageQty = $order->postageqty;
                                    }
                                    $returnOrders = DB::table('order_returns')
                                                    ->where('customer_id', $order->customer_id)
                                                    ->where('brand_id', $order->brand_id)
                                                    ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
                                                    ->where('status', '!=', 2)
                                                    ->where('status', '!=', 3)
                                                    ->get();
                                    $productReturnQty = 0;
                                    $productReturnCost = 0;
                                    if (count($returnOrders) > 0) {
                                      foreach ($returnOrders as $ROkey => $returnOrder) {
                                        if (isset($returnOrder)) {
                                          $returnOrderDetails = DB::table('order_return_details')->where('merged', '=', 0)->where('order_return_id', $returnOrder->id)->get();
                                          if (count($returnOrderDetails) > 0) {
                                            foreach ($returnOrderDetails as $RODkey => $returnOrderDetail) {
                                              if (isset($returnOrderDetail)) {
                                                if ($detail->qty > 0) {
                                                  if ($returnOrderDetail->product_id == $skuProduct->product_id) {
                                                    $productReturnQty += $returnOrderDetail->qty;
                                                    $productReturnCost += $returnOrderDetail->selling_cost;
                                                    OrderReturnDetail::where('merged', 0)->where('order_return_id', $returnOrder->id)->where('product_id', $skuProduct->product_id)->update([
                                                      'merged' => 1
                                                    ]);
                                                  }
                                                }
                                              }
                                            }
                                          }
                                        }
                                      }
                                    }
                                    array_push($products, array_combine($productKeys, [
                                      $skuProduct->product_id,
                                      $productName,
                                      $productQty,
                                      $sellerCostStatus,
                                      $prodSellingRate,
                                      ($skuProduct->selling_cost) * ($productQty),
                                      $getInvoiceData->id,
                                      $order->id,
                                      $getInvoiceData->invoice_number,
                                      $unitLabel,
                                      $unitPick,
                                      $unitPack,
                                      !is_null($unitPickPackFlat) ? $unitPickPackFlat : 0,
                                      $unitMailer,
                                      $unitMailerQty,
                                      $unitPostageQty,
                                      $productReturnQty,
                                      $productReturnCost,
                                    ]));
                                  }
                                }
                                array_push($orderDetailArray, array_combine($orderDetailKeys, [
                                $detail->sku_id,
                                $skuOrderedQty,
                                $skuPurchasingCost,
                                $skuSellingCost,
                                $labelPrice,
                                $pickPrice,
                                $packPrice,
                                !is_null($pickPackFlatPrice) ? $pickPackFlatPrice : 0,
                                // $mailerPrice,
                                // $postagePrice,
                                $products,
                                ]));
                            }
                            $label_Qty += $order->labelqty;
                            $pick_Qty += $order->pickqty;
                            $pack_Qty += $order->packqty;
                            $mailer_Qty += $order->mailerqty;
                            $postage_Qty += $order->postageqty;
                            $pickPackFlat_Qty += $order->pick_pack_flat_qty;
                            }
                        // }
                        $returnOrders = DB::table('order_returns')
                                        ->where('customer_id', $order->customer_id)
                                        ->where('brand_id', $order->brand_id)
                                        ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
                                        ->where('status', '!=', 2)
                                        ->where('status', '!=', 3);
                        $checkReturnOrders = $returnOrders->first();
                        array_push($mergedInvoices, array_combine($keys, [
                            $getInvoiceData->id,
                            $getInvoiceData->inv_no,
                            $order->id,
                            $getInvoiceData->invoice_number,
                            $getCustomerID,
                            $brandName,
                            $totalCost,
                            $labelQty,
                            $pickQty,
                            $packQty,
                            $pickPackFlatQty,
                            $mailerQty,
                            $postageQty,
                            $mailerPrice,
                            $postagePrice,
                            $orderDetailArray,
                            $orderCustomerServiceCharges->labels,
                            $orderCustomerServiceCharges->pick,
                            $orderCustomerServiceCharges->pack,
                            $orderCustomerServiceCharges->pick_pack_flat,
                            $orderCustomerServiceCharges->mailer,
                            isset($checkReturnOrders) ? $checkReturnOrders->cust_return_charges : '0',
                            $returnOrders->count(),
                            $returnOrders->sum('total_selling_cost')
                        ]));
                        $getOrder = Orders::where('merged', 0)->orderBy('id', 'ASC')->where('id', $customerOrders[$i])->where('status', '!=', 4)->first();
                        if (isset($getOrder)) {
                            $unitCharges = json_decode($order->customer_service_charges);
                            if ($i == (count($customerOrders)-1)) {
                            $labelUnitCharges .= $unitCharges->labels;
                            $pickUnitCharges .= $unitCharges->pick;
                            $packUnitCharges .= $unitCharges->pack;
                            $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat;
                            $mailerUnitCharges .= $unitCharges->mailer;
                            } else {
                            $labelUnitCharges .= $unitCharges->labels.',';
                            $pickUnitCharges .= $unitCharges->pick.',';
                            $packUnitCharges .= $unitCharges->pack.',';
                            $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat.',';
                            $mailerUnitCharges .= $unitCharges->mailer.',';
                            }
                        }
                        // }
                        Orders::where('merged', 0)->where('id', $customerOrders[$i])->update(['merged' => 1]);
                    }
                    $customer_ID = 0;
                    $total_Cost = 0;
                    
                    $label_Charges = 0;
                    $pick_Charges = 0;
                    $pack_Charges = 0;
                    $pick_pack_flat_Charges = 0;
                    $product_Name = '';
                    $product_Qty = 0;
                    $returnServiceCharges = 0;
                    $totalReturnQty = 0;
                    $getLastInvNoOfCustomer = MergedInvoice::orderBy('created_at', 'DESC')->where('customer_id', $mergedInvoices[0]['customer_id'])->first();
                    $custInvNo = 1;
                    if (isset($getLastInvNoOfCustomer)) {
                        $custInvNo = $getLastInvNoOfCustomer->inv_nos + 1;
                    }
                    for ($j=0; $j < count($mergedInvoices); $j++) {
                      $customer_ID = $mergedInvoices[0]['customer_id'];
                      $total_Cost = $total_Cost + $mergedInvoices[$j]['total_cost'];
                      $mailer_Charges = $mailer_Charges + $mergedInvoices[$j]['mailer_charges'];
                      $postage_Charges = $postage_Charges + $mergedInvoices[$j]['postage_charges'];
                      $returnServiceCharges = $mergedInvoices[$j]['return_charges'];
                      $totalReturnQty = $mergedInvoices[$j]['return_qty'];
                      $returnedProductTotal = $mergedInvoices[$j]['returned_product_total'];
                      for ($k=0; $k < count($mergedInvoices[$j]['invoice_ordered_detail']); $k++) { 
                        $label_Charges = $label_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['label_charges'];
                        $pick_Charges = $pick_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pick_charges'];
                        $pack_Charges = $pack_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pack_charges'];
                        $pick_pack_flat_Charges = $pick_pack_flat_Charges + $mergedInvoices[$j]['invoice_ordered_detail'][$k]['pick_pack_flat_charges'];
                        for ($m=0; $m < count($mergedInvoices[$j]['invoice_ordered_detail'][$k]['products']); $m++) { 
                            if ($mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_qty'] > 0) {
                            $invoicesMerged = InvoicesMerged::create([
                                'product_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_id'],
                                'product_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_qty'],
                                'selling_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['selling_cost'],
                                'product_price' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['seller_cost_status'] == 1 ? $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_price'] : 0.00,
                                'invoice_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['invoice_id'],
                                'inv_no' => $custInvNo,
                                'order_id' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['order_id'],
                                'invoice_number' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['invoice_number'],
                                //
                                'label_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['seller_cost_status'] == 1 ? $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['label_unit_cost'] : 0.00,
                                'pick_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pick_unit_cost'],
                                'pack_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pack_unit_cost'],
                                'pick_pack_flat_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['pick_pack_flat_unit_cost'],
                                'mailer_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['mailer_unit_cost'],
                                'mailer_unit_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['mailer_unit_qty'],
                                'product_return_qty' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_return_qty'],
                                'product_return_cost' => $mergedInvoices[$j]['invoice_ordered_detail'][$k]['products'][$m]['product_return_cost'],
                                //
                            ]);
                            }
                        }
                      }
                    }
                    $newPickPackFlatQty = 2;
                    $newPickPackFlatQty = $pickPackFlat_Qty;
                    $mergedInvoice = MergedInvoice::create([
                        'invoice_ids' => implode(',', $customerOrders),
                        'customer_id' => $customer_ID,
                        'total_cost' => $total_Cost,
                        'label_qty' => $label_Qty,
                        'pick_qty' => $pick_Qty,
                        'pack_qty' => $pack_Qty,
                        'flat_pick_pack_qty' => $pickPackFlat_Qty,
                        'mailer_qty' => $mailer_Qty,
                        'postage_qty' => $postage_Qty,
                        'label_charges' => $label_Charges,
                        'pick_charges' => $pick_Charges,
                        'pack_charges' => $pack_Charges,
                        'pick_pack_flat_charges' => $pick_pack_flat_Charges,
                        //
                        'mailer_charges' => $mailer_Charges,
                        'postage_charges' => $postage_Charges,
                        //
                        'label_unit_cost' => $labelUnitCharges,
                        'pick_unit_cost' => $pickUnitCharges,
                        'pack_unit_cost' => $packUnitCharges,
                        'pick_pack_flat_unit_cost' => $pickPackFlatUnitCharges,
                        'mailer_unit_cost' => $mailerUnitCharges,
                        'return_charges' => $returnServiceCharges,
                        'return_qty' => $totalReturnQty,
                        'returned_product_total' => $returnedProductTotal
                    ]);
                    $mergedInvoice->inv_nos = $custInvNo;
                    $mergedInvoice->save();
                    $productMerged = InvoicesMerged::where('merged_invoice_id', '=', NULL)->update([
                        'merged_invoice_id' => $mergedInvoice->id,
                        'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                    ]);
                    DB::commit();
                    return response()->json(['message' => true, 'data' => 'Merged Successfully']);
                } else {
                  DB::rollback();
                  return response()->json(['message' => false, 'data' => 'No invoice available']);
                }
            }
        catch(\Exception $e)
        {
          dd($e);
            DB::rollback();
            return view('admin.server_error');
            return response()->json(['message' => false, 'data' => 'Something went wrong']);
        }
        } else {
            return response()->json(['message' => false, 'data' => 'Customer should be same']);
        }
    }
}
