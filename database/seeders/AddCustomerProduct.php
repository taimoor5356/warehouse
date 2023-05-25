<?php

namespace Database\Seeders;

use App\Models\CustomerProduct;
use Illuminate\Database\Seeder;
use App\Models\CustomerHasProduct;

class AddCustomerProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $customer_has_products = CustomerHasProduct::get();
        foreach ($customer_has_products as $c_prod) {
            CustomerProduct::create([
                'customer_id' => $c_prod->customer_id,
                'product_id' => $c_prod->product_id,
                'label_qty' => $c_prod->label_qty,
                'label_cost' => $c_prod->label_cost,
                'selling_price' => '0.00',
                'is_active' => $c_prod->is_active,
                'deleted_at' => $c_prod->deleted_at
            ]);
        }
    }
}
