<?php

namespace Database\Seeders;

use App\Models\Sku;
use App\AdminModels\Labels;
use App\Models\SkuProducts;
use App\Models\CustomerHasSku;
use Illuminate\Database\Seeder;
use App\Models\CustomerHasProduct;

class SkuProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $skus = Sku::get();
        foreach ($skus as $_sku) {
            $service_charges = json_decode($_sku->service_charges_detail);
            if (isset($service_charges)) {
              foreach ($service_charges as $servicechargesdetail) {
                if ($servicechargesdetail != NULL || $servicechargesdetail != '') {
                    if (isset($servicechargesdetail->product_id)) {
                        $sku_prod = SkuProducts::where('sku_id', $_sku->id)->where('product_id', $servicechargesdetail->product_id)->first();
                        foreach ($servicechargesdetail->service_charges as $sCharges) {
                          if (isset($sCharges->slug)) {
                              if ($sCharges->slug == 'labels_price') {
                                  SkuProducts::where('sku_id', $_sku->id)->where('product_id', $servicechargesdetail->product_id)->update([
                                    'label' => $sCharges->price
                                  ]);
                              }
                              if($sCharges->slug == 'pick_price') {
                                  SkuProducts::where('sku_id', $_sku->id)->where('product_id', $servicechargesdetail->product_id)->update([
                                    'pick' => $sCharges->price
                                  ]);
                              }
                              if($sCharges->slug == 'pack_price') {
                                  SkuProducts::where('sku_id', $_sku->id)->where('product_id', $servicechargesdetail->product_id)->update([
                                    'pack' => $sCharges->price
                                  ]);
                              }
                          }
                        }
                    }
                }
              }
            }
            $brand = Labels::where('id', $_sku->brand_id)->first();
            if (CustomerHasSku::where('customer_id', $brand->customer_id)->where('brand_id', $brand->id)->where('sku_id', $_sku->id)->exists()) {
            } else {
                CustomerHasSku::create([
                    'customer_id' => $brand->customer_id,
                    'sku_id' => $_sku->id,
                    'brand_id' => $brand->id,
                    'selling_price' => $_sku->selling_cost
                ]);
            }
            $products = $_sku->sku_product;
            foreach ($products as $product) {
                if (CustomerHasProduct::where('customer_id', $brand->customer_id)->where('brand_id', $brand->id)->where('product_id', $product->product_id)->exists()) {
                } else {
                    CustomerHasProduct::where('customer_id', $brand->customer_id)->where('brand_id', $brand->id)->where('product_id', $product->product_id)->create([
                        'customer_id' => $brand->customer_id,
                        'product_id' => $product->product_id,
                        'brand_id' => $brand->id,
                        'label_cost' => '0.00'
                    ]);
                }
            }
        }
    }
}
