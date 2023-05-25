<?php

namespace Database\Seeders;

use App\AdminModels\Customers;
use App\Models\ServiceCharges;
use Illuminate\Database\Seeder;

class CustomerCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customers::get();
        foreach ($customers as $key => $customer) {
            $charges = ServiceCharges::where('customer_id', $customer->id)->first();
            if(!$charges) {
                $s_charges = ServiceCharges::create([
                    'customer_id' => $customer->id,
                    'labels' => 0,
                    'pick' => 0,
                    'pack' => 0,
                    'mailer' => 0,
                    'postage' => 0,
                ]);
            }
        }
    }
}
