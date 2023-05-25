<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\AdminModels\Products;
use App\AdminModels\Inventory;
use Illuminate\Database\Seeder;

class ProductInventory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Products::get();
        foreach ($products as $product) {
            $inventory = Inventory::where('product_id', $product->id)->first();
            if(!$inventory) {
                Inventory::create([
                    'product_id' => $product->id,
                    'date' => Carbon::now(),
                    'qty' => '0'
                ]);
            }
        }
    }
}
