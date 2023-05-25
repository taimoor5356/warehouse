<?php

namespace App\Jobs;

use App\Models\Sku;
use App\Models\SkuOrder;
use App\AdminModels\Products;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateSkuWeight implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $skus = Sku::with('sku_product:sku_id,product_id')->get();
        foreach ($skus as $key => $sku) {
            if (isset($sku)) {
                $skuWeight = 0;
                $skuProducts = $sku->sku_product;
                foreach ($skuProducts as $skey => $skuProduct) {
                    if (isset($skuProduct)) {
                        $product = Products::where('id', $skuProduct->product_id)->select(['weight'])->first();
                        if (isset($product)) {
                            $skuWeight += $product->weight;
                        }
                    }
                }
                $sku->weight = $skuWeight;
                $sku->save();
            }
        }
    }
}
