<?php

namespace Database\Seeders;

use App\Models\PinCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PinCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $pin_code = '123';
        $hashed = Hash::make($pin_code);
        // PinCode::create([
        //     'pin_code' => $hashed
        // ]);
        $pincodes = PinCode::get();
        foreach ($pincodes as $pincode) {
            $pincode->pin_code = $hashed;
            $pincode->save();
        }
    }
}
