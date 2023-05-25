<?php

namespace Database\Seeders;

use App\AdminModels\Orders;
use App\Models\SkuProducts;
use App\Models\SkuOrderDetails;
use Illuminate\Database\Seeder;
use App\AdminModels\OrderDetails;
use App\Models\ProductLabelOrder;
use App\Models\CustomerHasProduct;

class ProductLabelOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        try {
            $query = ProductLabelOrder::truncate();
            $batches = Orders::withTrashed()->orderBy('id', 'ASC')->select(['id', 'customer_id', 'brand_id', 'created_at'])->get();
            foreach ($batches as $bkey => $batch) {
                $orderDetails = OrderDetails::withTrashed()->where('order_id', $batch->id)->where('qty', '>', 0)->select(['sku_id', 'qty', 'order_id', 'service_charges_detail'])->get();
                foreach ($orderDetails as $okey => $detail) {
                    $skuProducts = SkuProducts::withTrashed()->where('sku_id', $detail->sku_id)->select(['sku_id', 'product_id'])->get();
                    foreach ($skuProducts as $skey => $skuProduct) {
                        $customerHasProduct = CustomerHasProduct::withTrashed()->where('customer_id', $batch->customer_id)->where('brand_id', $batch->brand_id)->where('product_id', $skuProduct->product_id)->select(['is_active'])->first();
                        $labelStatus = 1;
                        if (isset($customerHasProduct)) {
                            $labelStatus = $customerHasProduct->is_active;
                        }
                        $checkPreviousLabelStatus = SkuOrderDetails::where('order_id', $batch->id)->where('customer_id', $batch->customer_id)->where('brand_id', $batch->brand_id)->where('product_id', $skuProduct->product_id)->select(['is_active'])->first();
                        if (isset($checkPreviousLabelStatus)) {
                            if ($labelStatus == 0) { // if ON
                                if ($checkPreviousLabelStatus->is_active == 0) { // if previously ON
                                    ProductLabelOrder::create([
                                        'customer_id' => $batch->customer_id,
                                        'product_id' => $skuProduct->product_id,
                                        'brand_id' => $batch->brand_id,
                                        'label_deduction' => $detail->qty,
                                        'order_id' => $batch->id,
                                        'sku_id' => $skuProduct->sku_id,
                                        'created_at' => $batch->created_at,
                                        'updated_at' => $batch->created_at
                                    ]);
                                }
                            } else { // if now off
                                if ($checkPreviousLabelStatus->is_active == 0) { // if previously ON
                                    ProductLabelOrder::create([
                                        'customer_id' => $batch->customer_id,
                                        'product_id' => $skuProduct->product_id,
                                        'brand_id' => $batch->brand_id,
                                        'label_deduction' => $detail->qty,
                                        'order_id' => $batch->id,
                                        'sku_id' => $skuProduct->sku_id,
                                        'created_at' => $batch->created_at,
                                        'updated_at' => $batch->created_at
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            return 'DONE';
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
