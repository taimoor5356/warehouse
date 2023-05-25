<?php

namespace App\Traits;

use App\Models\MergedBrandProduct;

trait LabelQty {
    public function getLabelQty($customer_id, $brand_id, $product_id)
    {
        $label_qty = null;
        if (MergedBrandProduct::where('customer_id', $customer_id)->where('merged_brand', $brand_id)->where('product_id', $product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $customer_id)->where('merged_brand', $brand_id)->where('product_id', $product_id)->first();
            if (isset($mergedProduct)) {
              $label_qty = MergedBrandProduct::where('customer_id', $customer_id)
                ->where('merged_brand', $brand_id)
                ->where('product_id', $product_id)
                ->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $customer_id)->where('selected_brand', $brand_id)->where('product_id', $product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $customer_id)->where('selected_brand', $brand_id)->where('product_id', $product_id)->first();
            if (isset($mergedProduct)) {
              $label_qty = MergedBrandProduct::where('customer_id', $customer_id)
                ->where('selected_brand', $brand_id)
                ->where('product_id', $product_id)
                ->sum('merged_qty');
            }
          }
          return $label_qty;
    }
}