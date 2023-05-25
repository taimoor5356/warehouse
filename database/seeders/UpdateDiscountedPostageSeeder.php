<?php

namespace Database\Seeders;

use App\AdminModels\Orders;
use Illuminate\Database\Seeder;

class UpdateDiscountedPostageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $orders = Orders::with('Details.sku_detail')->where('customer_id', 91)
            ->select('id', 'customer_service_charges', 'discounted_postage_status', 'all_postage_charges', 'discounted_charges')->orderBy('id', 'DESC')->get();
        foreach ($orders as $orderkey => $order) {
            if (isset($order)) {
                $orderDetails = $order->Details;
                $customerServiceCharges = json_decode($order->customer_service_charges);

                $one_to_four_ounces = 0;
                $five_to_eight_ounces = 0;
                $nine_to_twelve_ounces = 0;
                $thirteen_to_fifteen_ounces = 0;
                $one_lbs = 0;
                $one_to_two_lbs = 0;
                $two_to_three_lbs = 0;
                $three_to_four_lbs = 0;

                $discounted_one_to_four_ounces = 0;
                $discounted_five_to_eight_ounces = 0;
                $discounted_nine_to_twelve_ounces = 0;
                $discounted_thirteen_to_fifteen_ounces = 0;
                $discounted_one_lbs = 0;
                $discounted_one_to_two_lbs = 0;
                $discounted_two_to_three_lbs = 0;
                $discounted_three_to_four_lbs = 0;

                $totalEachPostageCharges = [];
                $totalEachDiscountedPostageValue = [];

                if (!is_null($orderDetails)) {
                    foreach ($orderDetails as $odkey => $detail) {
                        if (isset($detail)) {
                            if ($detail->qty > 0) {
                                $sku = $detail->sku_detail;
                                if (!is_null($sku)) {
                                    if ($order->discounted_postage_status == 1) {
                                        if ($sku->weight <= 4) {
                                            $one_to_four_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt5);
                                            $discounted_one_to_four_ounces = $detail->qty * ($customerServiceCharges->discounted_postage_cost_lt5);
                                        } else if ($sku->weight >= 5 && $sku->weight <= 8) {
                                            $five_to_eight_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt9);
                                            $discounted_five_to_eight_ounces = $detail->qty * ($customerServiceCharges->discounted_postage_cost_lt9);
                                        } else if ($sku->weight >= 9 && $sku->weight <= 12) {
                                            $nine_to_twelve_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt13);
                                            $discounted_nine_to_twelve_ounces = $detail->qty * ($customerServiceCharges->discounted_postage_cost_lt13);
                                        } else if ($sku->weight >= 13 && $sku->weight < 16) {
                                            $thirteen_to_fifteen_ounces = $detail->qty * ($customerServiceCharges->postage_cost_gte13);
                                            $discounted_thirteen_to_fifteen_ounces = $detail->qty * ($customerServiceCharges->discounted_postage_cost_gte13);
                                        } else if ($sku->weight = 16) {
                                            $one_lbs = $detail->qty * ($customerServiceCharges->lbs1_1_99);
                                            $discounted_one_lbs = $detail->qty * ($customerServiceCharges->discounted_lbs1_1_99);
                                        } else if ($sku->weight > 16 && $sku->weight <= 32) {
                                            $one_to_two_lbs = $detail->qty * ($customerServiceCharges->lbs1_1_2);
                                            $discounted_one_to_two_lbs = $detail->qty * ($customerServiceCharges->discounted_lbs1_1_2);
                                        } else if ($sku->weight > 32 && $sku->weight <= 48) {
                                            $two_to_three_lbs = $detail->qty * ($customerServiceCharges->lbs2_1_3);
                                            $discounted_two_to_three_lbs = $detail->qty * ($customerServiceCharges->discounted_lbs2_1_3);
                                        } else if ($sku->weight > 48 && $sku->weight <= 64) {
                                            $three_to_four_lbs = $detail->qty * ($customerServiceCharges->lbs3_1_4);
                                            $discounted_three_to_four_lbs = $detail->qty * ($customerServiceCharges->discounted_lbs3_1_4);
                                        }
                                    } else {
                                        if ($sku->weight <= 4) {
                                            $one_to_four_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt5);
                                        } else if ($sku->weight >= 5 && $sku->weight <= 8) {
                                            $five_to_eight_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt9);
                                        } else if ($sku->weight >= 9 && $sku->weight <= 12) {
                                            $nine_to_twelve_ounces = $detail->qty * ($customerServiceCharges->postage_cost_lt13);
                                        } else if ($sku->weight >= 13 && $sku->weight < 16) {
                                            $thirteen_to_fifteen_ounces = $detail->qty * ($customerServiceCharges->postage_cost_gte13);
                                        } else if ($sku->weight = 16) {
                                            $one_lbs = $detail->qty * ($customerServiceCharges->lbs1_1_99);
                                        } else if ($sku->weight > 16 && $sku->weight <= 32) {
                                            $one_to_two_lbs = $detail->qty * ($customerServiceCharges->lbs1_1_2);
                                        } else if ($sku->weight > 32 && $sku->weight <= 48) {
                                            $two_to_three_lbs = $detail->qty * ($customerServiceCharges->lbs2_1_3);
                                        } else if ($sku->weight > 48 && $sku->weight <= 64) {
                                            $three_to_four_lbs = $detail->qty * ($customerServiceCharges->lbs3_1_4);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                array_push($totalEachPostageCharges, ['slug' => 'one_to_four_ounces', 'name' => 'one to four', 'price' => $one_to_four_ounces]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_to_four_ounces', 'name' => 'discounted one to four', 'price' => $discounted_one_to_four_ounces]);
                array_push($totalEachPostageCharges, ['slug' => 'five_to_eight_ounces', 'name' => 'five to eight', 'price' => $five_to_eight_ounces]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_five_to_eight_ounces', 'name' => 'discounted five to eight', 'price' => $discounted_five_to_eight_ounces]);
                array_push($totalEachPostageCharges, ['slug' => 'nine_to_twelve_ounces', 'name' => 'nine to twelve', 'price' => $nine_to_twelve_ounces]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_nine_to_twelve_ounces', 'name' => 'discounted nine to twelve', 'price' => $discounted_nine_to_twelve_ounces]);
                array_push($totalEachPostageCharges, ['slug' => 'thirteen_to_fifteen_ounces', 'name' => 'thirteen to fifteen', 'price' => $thirteen_to_fifteen_ounces]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_thirteen_to_fifteen_ounces', 'name' => 'discounted thirteen to fifteen', 'price' => $discounted_thirteen_to_fifteen_ounces]);
                array_push($totalEachPostageCharges, ['slug' => 'one_lbs', 'name' => 'one lbs', 'price' => $one_lbs]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_lbs', 'name' => 'discounted one lbs', 'price' => $discounted_one_lbs]);
                array_push($totalEachPostageCharges, ['slug' => 'one_to_two_lbs', 'name' => 'one to two lbs', 'price' => $one_to_two_lbs]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_to_two_lbs', 'name' => 'discounted one to two lbs', 'price' => $discounted_one_to_two_lbs]);
                array_push($totalEachPostageCharges, ['slug' => 'two_to_three_lbs', 'name' => 'two to three', 'price' => $two_to_three_lbs]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_two_to_three_lbs', 'name' => 'discounted two to three', 'price' => $discounted_two_to_three_lbs]);
                array_push($totalEachPostageCharges, ['slug' => 'three_to_four_lbs', 'name' => 'three to four', 'price' => $three_to_four_lbs]);
                array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_three_to_four_lbs', 'name' => 'discounted three to four', 'price' => $discounted_three_to_four_lbs]);
                $order->all_postage_charges = json_encode($totalEachPostageCharges);
                $order->discounted_charges = json_encode($totalEachDiscountedPostageValue);
                $order->save();
            }
        }
    }
}
