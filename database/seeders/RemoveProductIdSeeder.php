<?php

namespace Database\Seeders;

use App\Models\CustomerHasSku;
use App\Models\CustomerProduct;
use Illuminate\Database\Seeder;
use App\Models\CustomerHasProduct;

class RemoveProductIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // CustomerHasProduct::where('product_id', 2)->where('customer_id', 3)->where('brand_id', 3)->where('created_at', '2021-11-16 11:59:36')->forcedelete();
        CustomerHasProduct::where('customer_id', 0)->forcedelete();
        CustomerHasSku::where('customer_id', 0)->forcedelete();
        CustomerHasProduct::where('product_id', 0)->forcedelete();
        CustomerProduct::where('product_id', 0)->forcedelete();
    }
}
