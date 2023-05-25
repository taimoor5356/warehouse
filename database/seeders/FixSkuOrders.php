<?php

namespace Database\Seeders;

use App\Models\SkuOrder;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\ServiceCharges;
use App\Models\SkuOrderDetails;
use Illuminate\Database\Seeder;
use App\AdminModels\OrderDetails;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;

class FixSkuOrders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $orders = Orders::orderBy('id', 'ASC')->withTrashed()->whereIn('id', ['18833', '19318'])->get();
        foreach ($orders as $orderskey => $order) {
            $totalLabelQty = 0;
            $totalPickQty = 0;
            $totalPackQty = 0;
            $totalPickPackFlatQty = 0;
            $totalMailerQty = 0;
            $totalPostageQty = 0;
            if (isset($order)) {
                $serviceCharges = ServiceCharges::where('customer_id', $order->customer_id)->first();
                $orderDetails = OrderDetails::withTrashed()->where('order_id', $order->id)->get();
                $customerBrandCharges = Labels::where('id', $order->brand_id)->where('customer_id', $order->customer_id)->first();
                $skuOrder = SkuOrder::where('order_id', $order->id)->where('customer_id', $order->customer_id)->where('brand_id', $order->brand_id);
                $customerBrandMailerCost = 0;
                if (isset($customerBrandCharges)) {
                    if ($customerBrandCharges->mailer_cost != 0) {
                        $customerBrandMailerCost = $customerBrandCharges->mailer_cost;
                    }
                }
                $updateSkuOrder = $skuOrder->update([
                    'mailer_cost' => (float)($customerBrandMailerCost)
                ]);
                $totalMailerCost = $customerBrandMailerCost * $order->mailerqty;
                $totalCost = 0;
                $orderDetailTotalCharges = 0;
                $skuOrder2 = $skuOrder->get();
                $getPostageValues = json_decode($order->customer_service_charges);
                $totalWeightValue = 0;
                foreach ($skuOrder2 as $skuOrderkey2 => $sku_Order2) {
                    $getOrderDetailQty = OrderDetails::where('sku_id', $sku_Order2->sku_id)->where('order_id', $sku_Order2->order_id)->first();
                    $weight = $sku_Order2->weight;
                    if ($weight < 5 && $weight > 0) {
                        $weightValue = $getPostageValues->postage_cost_lt5 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 5 && $weight < 9) {
                        $weightValue = $getPostageValues->postage_cost_lt9 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 9 && $weight < 13) {
                        $weightValue = $getPostageValues->postage_cost_lt13 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 13 && $weight < 16) {
                        $weightValue = $getPostageValues->postage_cost_gte13 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 16 && $weight < 16.16) { // LBS rates
                        $weightValue = $getPostageValues->lbs1_1_99 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 16.16 && $weight < 32) {
                        $weightValue = $getPostageValues->lbs1_1_2 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 32.16 && $weight < 48) {
                        $weightValue = $getPostageValues->lbs2_1_3 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else if($weight >= 48.16) {
                        $weightValue = $getPostageValues->lbs3_1_4 * $getOrderDetailQty->qty;
                        if ($getOrderDetailQty->qty == 0) {
                            $weightValue = 0;
                        }
                    } else {
                        $weightValue = 0;
                    }
                    $totalWeightValue = (float)($totalWeightValue + $weightValue);
                }
                foreach ($orderDetails as $orderDetailkey => $orderDetail) {
                    if (isset($orderDetail)) {
                        if ($orderDetail->qty > 0) {
                            $skuOrderData = SkuOrder::where('sku_id', $orderDetail->sku_id)->where('order_id', $orderDetail->order_id)->first();
                            if (isset($skuOrderData)) {
                                $orderDetail->sku_selling_cost = $skuOrderData->selling_cost * $orderDetail->qty;
                                $orderDetail->save();
                            }
                        }
                        if ($orderDetail->sku_selling_cost == 1) {
                            $orderDetailTotalCharges += (float)($orderDetail->sku_selling_cost);
                        } else {
                            $orderDetailTotalCharges += 0;
                        }
                        $skuOrderDetails = SkuOrderDetails::where('order_id', $orderDetail->order_id)
                                            ->where('sku_id', $orderDetail->sku_id)
                                            ->where('brand_id', $order->brand_id)
                                            ->where('customer_id', $order->customer_id)
                                            ->get();
                        $totalLabelCharges = 0;
                        $totalPickCharges = 0;
                        $totalPackCharges = 0;
                        foreach ($skuOrderDetails as $skuOrderDetailkey => $skuOrderDetail) {
                            if (isset($skuOrderDetail)) {
                                $customerHasProduct = CustomerHasProduct::where('customer_id', $skuOrderDetail->customer_id)->where('brand_id', $skuOrderDetail->brand_id)->where('product_id', $skuOrderDetail->product_id)->first();
                                if (isset($customerHasProduct)) {
                                    if ($customerHasProduct->is_active == 0 && $skuOrderDetail->seller_cost_status == 1) {
                                        $labelCharges = $customerHasProduct->label_cost;
                                        if ($labelCharges == 0) {
                                            $labelCharges = $serviceCharges->labels;
                                        }
                                    } else {
                                        $labelCharges = 0;
                                    }
                                } else {
                                    $labelCharges = 0;
                                }
                                $skuOrderDetail->label = $labelCharges;
                                $skuOrderDetail->save();
                                $totalLabelCharges += $labelCharges * $orderDetail->qty;
                                $totalPickCharges += $skuOrderDetail->pick * $orderDetail->qty;
                                $totalPackCharges += $skuOrderDetail->pack * $orderDetail->qty;
                                if ($skuOrderDetail->label != 0 || $skuOrderDetail->label != NULL) {
                                    if ($skuOrderDetail->seller_cost_status == 1) {
                                        $totalLabelQty += $orderDetail->qty;
                                    } else {
                                        $totalLabelQty = 0;
                                    }
                                }
                                if ($skuOrderDetail->pick != 0 || $skuOrderDetail->pick != NULL) {
                                    $totalPickQty += $orderDetail->qty;
                                }
                                if ($skuOrderDetail->pack != 0 || $skuOrderDetail->pack != NULL) {
                                    $totalPackQty += $orderDetail->qty;
                                }
                                if ($skuOrderDetail->pick_pack_flat_status == 1) {
                                    $totalPickPackFlatQty = $orderDetail->qty;
                                }
                            }
                        }
                        $orderDetailServiceCharges = json_decode($orderDetail->service_charges_detail);
                        $orderDetailServiceCharges[0]->price = (round($totalLabelCharges, 2));   // label
                        $orderDetailServiceCharges[1]->price = (round($totalPickCharges, 2));   // pick
                        $orderDetailServiceCharges[2]->price = (round($totalPackCharges, 2));   // pack
                        $orderDetailServiceCharges[3]->price = (round($totalMailerCost, 2));   // mailer
                        $orderDetailServiceCharges[4]->price = (round($totalWeightValue, 2));  // postage
                        $orderDetail->service_charges_detail = $orderDetailServiceCharges;
                        $orderDetail->save();
                        foreach ($orderDetailServiceCharges as $orderDetailServiceChargeskey => $orderDetailServiceCharge) {
                            if ($orderDetailServiceCharge->slug == 'labels_price') {
                                $orderDetailTotalCharges += (float)($orderDetailServiceCharge->price);
                            }
                            if ($orderDetailServiceCharge->slug == 'pick_price') {
                                $orderDetailTotalCharges += (float)($orderDetailServiceCharge->price);
                            }
                            if ($orderDetailServiceCharge->slug == 'pack_price') {
                                $orderDetailTotalCharges += (float)($orderDetailServiceCharge->price);
                            }
                        }
                    }
                    $totalQtyForCharges = $orderDetail->qty;
                    $totalMailerQty += $totalQtyForCharges;
                    $totalPostageQty += $totalQtyForCharges;
                }
                $orderDetailTotalCharges += (float)($totalMailerCost) + (float)($totalWeightValue) + (float)($order->pick_pack_flat_price);
                $order->labelqty = $totalLabelQty;
                $order->pickqty = $totalPickQty;
                $order->packqty = $totalPackQty;
                $order->pick_pack_flat_qty = $totalPickPackFlatQty;
                $order->mailerqty = $totalMailerQty;
                $order->postageqty = $totalPostageQty;
                $order->total_cost = (float)($orderDetailTotalCharges);
                $order->save();
                DB::table('invoices')->where('order_id', $order->id)->update([
                    'grand_total' => $order->total_cost
                ]);
                echo $order->id.",";
            }
        }
    }
}
