<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ServiceCharges;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        $settings = Setting::where('id', 1)->first();
        return view('admin.settings.edit', compact('settings'));
    }

    public function settingsState(Request $request)
    {
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->first();
            $state = $setting->state;
            return response()->json($state);
        }
    }

    public function getReturnOrderState(Request $request)
    {
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->first();
            $state = $setting->return_order_state;
            return response()->json($state);
        }
    }

    public function getCreateReturnOrderState(Request $request)
    {
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->first();
            $state = $setting->create_return_order_state;
            return response()->json($state);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->update([
                'state' => $request->state
            ]);
        } else {
            if (Setting::where('id', 1)->exists()) {
                Setting::where('id', 1)->update([
                    'forecast_days' => $request->forecast_days,
                    'threshold_val' => $request->threshold_val,
                    'labels' => $request->labels,
                    'pick' => $request->pick,
                    'pack' => $request->pack,
                    'mailer' => $request->mailer,
                    'postage_cost' => $request->postage_cost,
                    'postage_cost_lt5' => $request->postage_cost_lt5,
                    'postage_cost_lt9' => $request->postage_cost_lt9,
                    'postage_cost_lt13' => $request->postage_cost_lt13,
                    'postage_cost_gte13' => $request->postage_cost_gte13,
                    'lbs1_1_99' => $request->lbs1_1_99,
                    'lbs1_1_2' => $request->lbs1_1_2,
                    'lbs2_1_3' => $request->lbs2_1_3,
                    'lbs3_1_4' => $request->lbs3_1_4,
                    //
                    'discounted_postage_cost' => $request->discounted_postage_cost,
                    'discounted_postage_cost_lt5' => $request->discounted_postage_cost_lt5,
                    'discounted_postage_cost_lt9' => $request->discounted_postage_cost_lt9,
                    'discounted_postage_cost_lt13' => $request->discounted_postage_cost_lt13,
                    'discounted_postage_cost_gte13' => $request->discounted_postage_cost_gte13,
                    'discounted_lbs1_1_99' => $request->discounted_lbs1_1_99,
                    'discounted_lbs1_1_2' => $request->discounted_lbs1_1_2,
                    'discounted_lbs2_1_3' => $request->discounted_lbs2_1_3,
                    'discounted_lbs3_1_4' => $request->discounted_lbs3_1_4,
                    //
                    'pick_pack_flat' => $request->pick_pack_flat,
                    'return_service_charges' => $request->return_service_charges,
                    'company_name' => $request->company_name,
                    'company_number' => $request->company_number,
                    'company_address' => $request->company_address
                ]);
                $settings = Setting::where('id', 1)->first();
                $customers_service_charges = ServiceCharges::where('default_service_charges', 1)->get();
                foreach ($customers_service_charges as $customer_serviceCharges) {
                    if (isset($customer_serviceCharges)) {
                        $customer_serviceCharges->labels = $settings->labels;
                        $customer_serviceCharges->pick = $settings->pick;
                        $customer_serviceCharges->pack = $settings->pack;
                        $customer_serviceCharges->mailer = $settings->mailer;
                        $customer_serviceCharges->return_service_charges = $settings->return_service_charges;
                        $customer_serviceCharges->save();
                    }
                }
                $customers_postage_charges = ServiceCharges::where('default_postage_charges', 1)->get();
                foreach ($customers_postage_charges as $customer_postageCharges) {
                    if (isset($customer_postageCharges)) {
                        $customer_postageCharges->postage_cost_lt5 = $settings->postage_cost_lt5;
                        $customer_postageCharges->postage_cost_lt9 = $settings->postage_cost_lt9;
                        $customer_postageCharges->postage_cost_lt13 = $settings->postage_cost_lt13;
                        $customer_postageCharges->postage_cost_gte13 = $settings->postage_cost_gte13;
                        $customer_postageCharges->lbs1_1_99 = $settings->lbs1_1_99;
                        $customer_postageCharges->lbs1_1_2 = $settings->lbs1_1_2;
                        $customer_postageCharges->lbs2_1_3 = $settings->lbs2_1_3;
                        $customer_postageCharges->lbs3_1_4 = $settings->lbs3_1_4;
                        $customer_postageCharges->postage_cost = $settings->postage_cost;
                        //
                        
                        $customer_postageCharges->discounted_postage_cost_lt5 = $settings->discounted_postage_cost_lt5;
                        $customer_postageCharges->discounted_postage_cost_lt9 = $settings->discounted_postage_cost_lt9;
                        $customer_postageCharges->discounted_postage_cost_lt13 = $settings->discounted_postage_cost_lt13;
                        $customer_postageCharges->discounted_postage_cost_gte13 = $settings->discounted_postage_cost_gte13;
                        $customer_postageCharges->discounted_lbs1_1_99 = $settings->discounted_lbs1_1_99;
                        $customer_postageCharges->discounted_lbs1_1_2 = $settings->discounted_lbs1_1_2;
                        $customer_postageCharges->discounted_lbs2_1_3 = $settings->discounted_lbs2_1_3;
                        $customer_postageCharges->discounted_lbs3_1_4 = $settings->discounted_lbs3_1_4;
                        $customer_postageCharges->discounted_postage_cost = $settings->discounted_postage_cost;

                        //
                        $customer_postageCharges->save();
                    }
                }
            } else {
                $settings = Setting::create([
                    'forecast_days' => $request->forecast_days,
                    'threshold_val' => $request->threshold_val,
                    'labels' => $request->labels,
                    'pick' => $request->pick,
                    'pack' => $request->pack,
                    'mailer' => $request->mailer,
                    'postage_cost' => $request->postage_cost,
                    'postage_cost_lt5' => $request->postage_cost_lt5,
                    'postage_cost_lt9' => $request->postage_cost_lt9,
                    'postage_cost_lt13' => $request->postage_cost_lt13,
                    'postage_cost_gte13' => $request->postage_cost_gte13,
                    'lbs1_1_9' => $request->lbs1_1_99,
                    'lbs1_1_2' => $request->lbs1_1_2,
                    'lbs2_1_3' => $request->lbs2_1_3,
                    'lbs3_1_4' => $request->lbs3_1_4,
                    //
                    'discounted_postage_cost' => $request->discounted_postage_cost,
                    'discounted_postage_cost_lt5' => $request->discounted_postage_cost_lt5,
                    'discounted_postage_cost_lt9' => $request->discounted_postage_cost_lt9,
                    'discounted_postage_cost_lt13' => $request->discounted_postage_cost_lt13,
                    'discounted_postage_cost_gte13' => $request->discounted_postage_cost_gte13,
                    'discounted_lbs1_1_99' => $request->discounted_lbs1_1_99,
                    'discounted_lbs1_1_2' => $request->discounted_lbs1_1_2,
                    'discounted_lbs2_1_3' => $request->discounted_lbs2_1_3,
                    'discounted_lbs3_1_4' => $request->discounted_lbs3_1_4,
                    //
                    'pick_pack_flat' => $request->pick_pack_flat,
                    'return_service_charges' => $request->return_service_charges,
                    'company_name' => $request->company_name,
                    'company_number' => $request->company_number,
                    'company_address' => $request->company_address
                ]);
                $customers_service_charges = ServiceCharges::where('default_service_charges', 1)->get();
                $customers_postage_charges = ServiceCharges::where('default_postage_charges', 1)->get();
                foreach ($customers_service_charges as $customer_serviceCharges) {
                    if (isset($customer_serviceCharges)) {
                        $customer_serviceCharges->labels = $settings->labels;
                        $customer_serviceCharges->pick = $settings->pick;
                        $customer_serviceCharges->pack = $settings->pack;
                        $customer_serviceCharges->mailer = $settings->mailer;
                        $customer_serviceCharges->return_service_charges = $settings->return_service_charges;
                        $customer_serviceCharges->save();
                    }
                }
                foreach ($customers_postage_charges as $customer_postageCharges) {
                    if (isset($customer_postageCharges)) {
                        $customer_postageCharges->postage_cost_lt5 = $settings->postage_cost_lt5;
                        $customer_postageCharges->postage_cost_lt9 = $settings->postage_cost_lt9;
                        $customer_postageCharges->postage_cost_lt13 = $settings->postage_cost_lt13;
                        $customer_postageCharges->postage_cost_gte13 = $settings->postage_cost_gte13;
                        $customer_postageCharges->lbs1_1_9 = $settings->lbs1_1_9;
                        $customer_postageCharges->lbs1_1_2 = $settings->lbs1_1_2;
                        $customer_postageCharges->lbs2_1_3 = $settings->lbs2_1_3;
                        $customer_postageCharges->lbs3_1_4 = $settings->lbs3_1_4;
                        $customer_postageCharges->postage_cost = $settings->postage_cost;
                        //
                        
                        $customer_postageCharges->discounted_postage_cost_lt5 = $settings->discounted_postage_cost_lt5;
                        $customer_postageCharges->discounted_postage_cost_lt9 = $settings->discounted_postage_cost_lt9;
                        $customer_postageCharges->discounted_postage_cost_lt13 = $settings->discounted_postage_cost_lt13;
                        $customer_postageCharges->discounted_postage_cost_gte13 = $settings->discounted_postage_cost_gte13;
                        $customer_postageCharges->discounted_lbs1_1_99 = $settings->discounted_lbs1_1_99;
                        $customer_postageCharges->discounted_lbs1_1_2 = $settings->discounted_lbs1_1_2;
                        $customer_postageCharges->discounted_lbs2_1_3 = $settings->discounted_lbs2_1_3;
                        $customer_postageCharges->discounted_lbs3_1_4 = $settings->discounted_lbs3_1_4;
                        $customer_postageCharges->discounted_postage_cost = $settings->discounted_postage_cost;

                        //
                        $customer_postageCharges->save();
                    }
                }
            }
            return redirect('/settings')->withSuccess('Updated Successfully');
        }
    }

    public function returnOrderState(Request $request)
    {
        //
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->update([
                'return_order_state' => $request->return_order_state
            ]);
        }
    }

    public function saveReturnOrderState(Request $request)
    {
        if ($request->ajax()) {
            $setting = Setting::where('id', 1)->update([
                'create_return_order_state' => $request->create_return_order_state
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
