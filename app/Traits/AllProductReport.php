<?php

namespace App\Traits;

use App\AdminModels\Inventory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\AdminModels\Orders;
use App\Jobs\ProductReport;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

trait AllProductReport
{
    public function generateReport($request)
    {
        try {
            $query = Products::with('product_order')->where('forecast_status', 0)->orderBy('name', 'ASC');
            $headers = Products::orderBy('name', 'ASC')->where('forecast_status', 0);
            $from = Carbon::now()->subDays(6)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            if (!is_null($request['time_duration'])) {
                if ($request['time_duration'] == 'yesterday') {
                    $from = Carbon::now()->subDays(1)->format('Y-m-d');
                } else if ($request['time_duration'] == 'this_week') {
                    $from = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $to = Carbon::now()->format('Y-m-d');
                } else if ($request['time_duration'] == 'last_week') {
                    $previousWeek = strtotime('-1 week +1 day');
                    $start_week = strtotime("last monday midnight",$previousWeek);
                    $end_week = strtotime("next sunday",$start_week);
                    $start_week = date("Y-m-d",$start_week);
                    $end_week = date("Y-m-d",$end_week);
                    $from = $start_week;
                    $to = $end_week;
                } else if ($request['time_duration'] == 'this_month') {
                    $from = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $to = Carbon::now()->format('Y-m-d');
                } else if ($request['time_duration'] == 'last_month') {
                    $previousMonth = strtotime('-1 month +1 day');
                    $start_month = strtotime("first day of this month",$previousMonth);
                    $end_month = strtotime("last day of this month",$start_month);
                    $start_month = date("Y-m-d",$start_month);
                    $end_month = date("Y-m-d",$end_month);
                    $from = $start_month;
                    $to = $end_month;
                    // dd($from, $to);
                } else if ($request['time_duration'] == 'last_six_months') {
                    $from = Carbon::now()->subDays(180)->format('Y-m-d');
                    $to = Carbon::now()->format('Y-m-d');
                } else if ($request['time_duration'] == 'this_year') {
                    $from = Carbon::now()->startOfYear()->format('Y-m-d');
                    $to = Carbon::now()->format('Y-m-d');
                } else if ($request['time_duration'] == 'last_year') {
                    $currentYear = Carbon::now()->format('Y');
                    $start_year = Carbon::parse(1)->setYear($currentYear)->subYear(1)->firstOfMonth()->format('Y-m-d');
                    $end_year = date('Y-m-d', strtotime('last day of December this year -1 years'));
                    $from = $start_year;
                    $to = $end_year;
                    // dd($from, $to);
                }
            } else if (!is_null($request['min'])) {
                $from = Carbon::parse($request['min'])->format('Y-m-d');
                if (!empty($request['max'])) {
                    $to = Carbon::parse($request['max'])->format('Y-m-d');
                }
            }
            if (!is_null($request['category_id'])) {
                $query = $query->where('category_id', $request['category_id']);
                $headers = $headers->where('category_id', $request['category_id']);
            }
            if (!empty($request['product'])) {
                $query = $query->where('id', $request['product']);
            }
            $products = $query->get();
            $carbonPeriod = CarbonPeriod::create($from, $to);
            $arr = [];
            $keys = ['date', 'products', 'total'];
            foreach ($carbonPeriod as $key => $date) {
                $totalOrders = [];
                $totalOrdersKeys = ['name', 'qty', 'inventory_qty'];
                $footTotal = [];
                foreach ($products as $pkey => $product) { // 120
                    $qty = 0;
                    $inventoryQty = 0;
                    if (isset($product)) {
                        $skuOrderDetailData = DB::table('sku_order_details')
                                    ->join('order_details', function($join) {
                                        $join->on('order_details.order_id', '=', 'sku_order_details.order_id')
                                        ->on('order_details.sku_id', '=', 'sku_order_details.sku_id');
                                    })
                                    ->join('orders', 'order_details.order_id', 'orders.id')
                                    ->where('orders.status', '!=', 4)
                                    ->where('order_details.qty', '>', 0);
                        $getqty = $skuOrderDetailData->whereDate('orders.created_at', $date->format('Y-m-d'))
                                    ->where('sku_order_details.product_id', $product->id)
                                    ->where('sku_order_details.deleted_at', NULL)
                                    ->sum('order_details.qty');
                        $qty = $getqty;
                        $products[$pkey]['total'] += $qty;
                        $inventoryQty = Inventory::where('product_id', $product->id)->sum('qty');
                        array_push($totalOrders, array_combine($totalOrdersKeys, [$product->name, $qty, $inventoryQty]));
                        array_push($footTotal, $products[$pkey]['total']);
                    }
                }
                array_push($arr, array_combine($keys, [$date->format('m/d/Y'), $totalOrders, $footTotal]));
            }
            $products2 = $query->get();
            $arr2 = [];
            $keys2 = ['avg_total'];
            foreach (array_reverse($carbonPeriod->toArray()) as $key2 => $date2) {
                if (!empty($request['days_count'])) {
                    if ($key2 < $request['days_count']) {
                        $avgTotal2 = [];
                        foreach ($products2 as $pkey2 => $product2) { // 120
                            $avg_qty_total2 = 0;
                            if (isset($product2)) {
                                $getqty2 = DB::table('sku_order_details')
                                        ->join('order_details', function($join) {
                                            $join->on('order_details.order_id', '=', 'sku_order_details.order_id')
                                            ->on('order_details.sku_id', '=', 'sku_order_details.sku_id');
                                        })
                                        ->join('orders', 'order_details.order_id', 'orders.id')
                                        ->where('orders.status', '!=', 4)
                                        ->where('sku_order_details.product_id', $product2->id)
                                        ->where('sku_order_details.deleted_at', NULL)
                                        ->where('order_details.qty', '>', 0)
                                        ->whereDate('orders.created_at', $date2->format('Y-m-d'))
                                        ->sum('order_details.qty');
                                $avg_qty_total2 = $getqty2;
                            }
                            $products2[$pkey2]['avg_total'] += $avg_qty_total2;
                            array_push($avgTotal2, $products2[$pkey2]['avg_total']);
                        }
                        array_push($arr2, array_combine($keys2, [$avgTotal2]));
                    }
                }
            }
            $dayAvg = [];
            $monthAvg = [];
            if (!empty($request['days_count'])) {
                foreach (array_reverse($arr2)[0]['avg_total'] as $avgKeys => $avg) {
                    $totalDays = count($arr2);
                    if (!is_null($request['days_count'])) {
                        $totalDays = $request['days_count'];
                    }
                    array_push($dayAvg, ($avg/$totalDays));
                    array_push($monthAvg, (($avg/$totalDays) * 30));
                }
            } else {
                foreach (array_reverse($arr)[0]['total'] as $avgKeys => $avg) {
                    $totalDays = count($arr);
                    if (!is_null($request['days_count'])) {
                        $totalDays = $request['days_count'];
                    }
                    array_push($dayAvg, ($avg/$totalDays));
                    array_push($monthAvg, (($avg/$totalDays) * 30));
                }
            }
            $headers = $headers->pluck('name')->all();
            array_unshift($headers, 'Day/Avg');
            array_push($headers, 'Total');
            $dayHeader = $dayAvg;
            $monthHeader = $monthAvg;
            
            array_unshift($dayHeader, 'Day Avg');
            array_push($dayHeader, '0');

            array_unshift($monthHeader, 'Month Avg');
            array_push($monthHeader, '0');
            
            $report = array();
            $row = 0;
            foreach (array_reverse($arr) as $data) {
                $report[$row][] = $data['date'];
                $sumOfQty = 0;
                foreach ($data['products'] as $product) {
                    $report[$row][] = $product['qty'];
                    $sumOfQty += $product['qty'];
                }
                $report[$row][] = $sumOfQty;
                $row++;
            }
            $data = array('data' => $arr, 'products' => $products, 'day_avg' => $dayAvg, 'month_avg' => $monthAvg);
            $allData = $data;
            $fileName = rand().'.csv';
            $path = public_path('reports');
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            $output = fopen(public_path('reports/'.$fileName), "w");
            $thColumns = $headers;
            fputcsv($output, $thColumns);
            fputcsv($output, $dayHeader);
            fputcsv($output, $monthHeader);
            foreach ($report as $data) {
                fputcsv($output, $data);
            }
            fclose($output);
            if ($request['send_mail'] == 'true') {
                Mail::to('warehousesystem@gmail.com')->cc(['aleem.333@hotmail.com'])->send(new \App\Mail\ProductReportMail($fileName));
            }
            return $allData;
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
