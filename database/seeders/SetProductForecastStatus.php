<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\AdminModels\Products;
use Illuminate\Database\Seeder;

class SetProductForecastStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $products = Products::get();
        $setting = Setting::where('id', 1)->first();
        foreach ($products as $product) {
            if ($product->forecast_status == NULL || $product->forecast_status == '' || $product->forecast_status == 0) {
                $product->forecast_status = 0;
                $product->automated_status = 1;
                $product->manual_threshold = NULL;
                $product->forecast_days = $setting->forecast_days;
                $product->threshold_val = $setting->threshold_val;
                $product->save();
            }
        }
    }
}
