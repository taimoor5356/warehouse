<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerUser;
use App\AdminModels\Customers;
use Illuminate\Database\Seeder;

class CustomerAsUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $customers = Customers::get();
        foreach ($customers as $key => $customer) {
            if (!(User::where('email', $customer->email)->exists())) {
                $user = User::create([
                    'name' => $customer->customer_name,
                    'email' => $customer->email,
                    'customer_status' => '1',
                    'password' => $customer->password,
                    'role_id' => '0'
                ]);
                $user->assignRole('customer');
            }
        }
        foreach ($customers as $key => $customer) {
            if (isset($customer)) {
                if (User::where('name', $customer->customer_name)->where('email', $customer->email)->where('password', $customer->password)->exists()) {
                    $userId = User::where('name', $customer->customer_name)->where('email', $customer->email)->where('password', $customer->password)->first();
                    if (!(CustomerUser::where('customer_id', $customer->id)->exists())) {
                        CustomerUser::create([
                            'customer_id' => $customer->id,
                            'user_id' => $userId->id
                        ]);
                    }
                }
            }
        }
    }
}
