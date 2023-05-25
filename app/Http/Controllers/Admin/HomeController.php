<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Sku;
use App\Models\File;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\OrderReturn;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\CustomerProduct;
use App\AdminModels\OrderDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\InventoryHistory;

class HomeController extends Controller
{
    public $productModel;
    public function __construct()
    {
        $this->middleware('auth');
        $this->productModel = new Products();
    }
    //
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $customerProducts = CustomerProduct::where('customer_id', $customerId)->count();
            $getAllData = $this->getAllProfit();
            $data['products'] = $customerProducts;
            $data['purchases'] = $this->getPurchases();
            $data['sales'] = $this->getSales();
            $data['profit'] = $this->getProfit();
            $data['getAllPurchases'] = 0;
            $data['getAllBatches'] = $getAllData['total_batches'];
            $data['getAllMailers'] = $getAllData['total_mailer'];
            $data['sales_by_month'] = $this->getOrders('sales');
            $data['sales_by_month_cost'] = $this->getOrders('sales_cost');
            $data['highest_product_prices'] = $this->fifteenHighestProductsPrices();
            $data['highest_product_names'] = $this->fifteenHighestProductNames();
            $data['file_count'] = File::where('customer_id', $customerId)->count();
            $querysalescount = Orders::with('Customer')->where('customer_id', $customerId)->where('status', '!=', 4);
            $queryMailerCount = Orders::with('Customer')->where('customer_id', $customerId)->where('status', '!=', 4);
            $queryReturnCount = OrderReturn::with('customer')->where('customer_id', $customerId);
            $querysalescount->whereHas("Customer", function ($q) use ($customerId) {
                $q->where('customers.deleted_at', NULL)->where('customers.id', $customerId);
            });
            $queryReturnCount->whereHas("customer", function ($q) use ($customerId) {
                $q->where('customers.deleted_at', NULL)->where('customers.id', $customerId);
            });
            $data['sales_count'] = $querysalescount->count();
            $data['mailer_count'] = $queryMailerCount->sum('mailerqty');
            $data['complete_sales_count'] = $querysalescount->where('status', 3)->count();
            $data['returned_order_count'] = $queryReturnCount->count();
            $data['customers'] = 1;
            $ql = Labels::with('customer')->where('customer_id', $customerId);
            $ql->whereHas('customer', function ($q) use ($customerId) {
                $q->where('customers.deleted_at', NULL)->where('customers.id', $customerId);
            });
            $data['brands'] = $ql->count();
            $querySKU = Sku::with('brand');
            $querySKU->whereHas('brand', function ($q) use ($customerId) {
                $q->where('labels.deleted_at', NULL)->where('customer_id', $customerId);
            });
            $querySKU->whereHas('brand.customer', function ($q) use ($customerId) {
                $q->where('customers.deleted_at', NULL)->where('id', $customerId);
            });
            $data['sku'] = $querySKU->count();
            $data['earning'] = $this->getEarning();
            $data['percentage'] = $this->earningComparison();
            $data['orders_by_status_graph'] = $this->getOrderByStatus('graph');
            $data['orders_by_status'] = $this->getOrderByStatus('');
            $data['profit_by_month'] = $this->getMonthlyProfit();
            return view('admin.home', $data);
        } else {
            $getInventoryCost = $this->getInventoryCost();
            $getAllData = $this->getAllProfit();
            $data['getAllProfit'] = $getAllData['total_profit'];
            $data['getAllSales'] = $getAllData['total_sales'];
            $data['getAllPurchases'] = $getInventoryCost;
            $data['getAllBatches'] = $getAllData['total_batches'];
            $data['getAllMailers'] = $getAllData['total_mailer'];
            $data['products'] = Products::count();
            $data['purchases'] = $this->getPurchases();
            $data['sales'] = $this->getSales();
            $data['profit'] = $this->getProfit();
            $data['sales_by_month'] = $this->getOrders('sales');
            $data['sales_by_month_cost'] = $this->getOrders('sales_cost');
            $data['highest_product_prices'] = $this->fifteenHighestProductsPrices();
            $data['highest_product_names'] = $this->fifteenHighestProductNames();
            $data['file_count'] = File::count();
            $querysalescount = Orders::with('Customer')->where('status', '!=', 4);
            $queryMailerCount = Orders::with('Customer')->where('status', '!=', 4);
            $querysalescount->whereHas("Customer", function ($q) {
                $q->where('customers.deleted_at', NULL);
            });
            $data['sales_count'] = $querysalescount->count();
            $data['mailer_count'] = $queryMailerCount->sum('mailerqty');
            $data['customers'] = Customers::count();
            $ql = Labels::with('customer');
            $ql->whereHas('customer', function ($q) {
                $q->where('customers.deleted_at', NULL);
            });
            $data['brands'] = $ql->count();
            $querySKU = Sku::with('brand');
            $querySKU->whereHas('brand', function ($q) {
                $q->where('labels.deleted_at', NULL);
            });
            $querySKU->whereHas('brand.customer', function ($q) {
                $q->where('customers.deleted_at', NULL);
            });
            $data['sku'] = $querySKU->count();
            $data['earning'] = $this->getEarning();
            $data['percentage'] = $this->earningComparison();
            $data['orders_by_status_graph'] = $this->getOrderByStatus('graph');
            $data['orders_by_status'] = $this->getOrderByStatus('');
            $data['profit_by_month'] = $this->getMonthlyProfit();
            return view('admin.home', $data);
        }
    }
    public function customerCharges($from = null, $to = null)
    {
        $pick_pack = 0;
        $orders = DB::table('order_details')->where('order_details.deleted_at', NULL)
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.qty', '>', 0)
                ->where('orders.status', '!=', 4);
        if (!empty($from)) {
            $orders = $orders->whereDate('order_details.created_at', '>=', Carbon::parse($from)->format('Y-m-d'));
            if (!empty($to)) {
                $orders = $orders->whereDate('order_details.created_at', '<=', Carbon::parse($to)->format('Y-m-d'));
            }
        }
        $orders = $orders->select('order_details.service_charges_detail', 'order_details.qty');
        $orders = $orders->get();
        if (!is_null($orders)) {
            foreach ($orders as $dkey => $q) {
                if ($q->qty > 0) {
                    $charges = json_decode($q->service_charges_detail);
                    foreach ($charges as $key => $charge) {
                        $pick = 0;
                        $pack = 0;
                        if ($charge->slug == 'pick_price') {
                            $pick = $charge->price;
                        }
                        if ($charge->slug == 'pack_price') {
                            $pack = $charge->price;
                        }
                        $pick_pack += ($pick + $pack);
                    }
                }
            }
        }
        return $pick_pack;
    }
    public function getAllProfit($from = null, $to = null)
    {
        $orders = DB::table('orders')->where('orders.status', '!=', 4);
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $orders = $orders->where('customer_id', $customerId);
        }
        $sales = 0;
        $purchases = 0;
        $totalOrders = $orders->count();
        $totalMailers = $orders->sum('mailerqty');
        $query = $orders->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.qty', '>', 0)
                ->where('orders.deleted_at', NULL)
                ->where('order_details.deleted_at', NULL);
        if (!empty($from)) {
            $query = $query->whereDate('order_details.created_at', '>=', Carbon::parse($from)->format('Y-m-d'));
            if (!empty($to)) {
                $query = $query->whereDate('order_details.created_at', '<=', Carbon::parse($to)->format('Y-m-d'));
            }
        }
        $query = $query->select(
                    DB::raw('SUM(order_details.sku_selling_cost) as selling_amount'),
                    DB::raw('SUM(order_details.sku_purchasing_cost * order_details.qty) as purchasing_amount')
                );
        $query = $query->first();
        $pickPackCharges = $this->customerCharges($from, $to); 
        $customers = Customers::with('service_charges')->withTrashed()->get();
        $returnOrders = 0;
        $returnedAmount = 0;
        $returnChargesOfCustomer = 0;
        foreach ($customers as $ckey => $cust) {
            $orderReturn = OrderReturn::where('customer_id', $cust->id)->where('status', '!=', 2)->where('status', '!=', 3)->where('total_price', '>', 0)->where('total_qty', '>', 0);
            if (!empty($from)) {
                $orderReturn = $orderReturn->whereDate('created_at', '>=', Carbon::parse($from)->format('Y-m-d'));
                if (!empty($to)) {
                    $orderReturn = $orderReturn->whereDate('created_at', '<=', Carbon::parse($to)->format('Y-m-d'));
                }
            }
            if ($orderReturn->exists()) {
                $getReturns = $orderReturn->get();
                foreach ($getReturns as $getReturnkey => $return) {
                    $returnedAmount += $return->cust_return_charges;
                }
                $returnChargesOfCustomer = $returnedAmount;
            }
        }
        $allProfit = ($query->selling_amount - $query->purchasing_amount) + ($pickPackCharges) + ($returnChargesOfCustomer);
        return ['total_sales' => $query->selling_amount, 'total_purchases' => $query->purchasing_amount, 'total_profit' => $allProfit, 'total_mailer' => $totalMailers, 'total_batches' => $totalOrders];
    }
    public function getInventoryCost()
    {
        $products = DB::table('products')->where('deleted_at', NULL)->get();
        $totalCost = 0;
        foreach ($products as $key => $product) {
            if (isset($product)) {
                $inventory = DB::table('inventory')->where('product_id', $product->id)->sum('qty');
                $totalCost += $product->price * $inventory;
            }
        }
        return $totalCost;
    }
    public function fifteenHighestProductNames()
    {
        try {
            $products = $this->productModel->fifteenHighest('name');
            return json_encode(array_values($products));
        } catch (\Throwable $th) {
            return 'Something went wrong';
        }
    }
    public function fifteenHighestProductsPrices()
    {
        try {
            $products = $this->productModel->fifteenHighest('price');
            return json_encode(array_map('strval', array_values($products)));
        } catch (\Throwable $th) {
            return 'Something went wrong';
        }
    }
    public function getPurchases()
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
            foreach ($customerProducts as $key => $cProduct) {
                $products = Products::with('inventory')->where('id', $cProduct->product_id)->get();
                $purchases = 0;
                foreach ($products as $key => $product) {
                    $inventory = Inventory::where('product_id', $product->id)->sum('qty');
                    if ($inventory) {
                        $purchases += (float)($product->price) * $inventory;
                    }
                }
                return $purchases;
            }
        }
        // if (Auth::user()->can('report_view')) {
            $products = Products::with('inventory')->where('deleted_at', NULL)->get();
            $purchases = 0;
            foreach ($products as $key => $product) {
                $inventory = Inventory::where('product_id', $product->id)->sum('qty');
                if ($inventory) {
                    $purchases += (float)($product->price) * $inventory;
                }
            }
            return $purchases;
        // }
    }
    public function getSales()
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $orders = Orders::with('Details')->where('status', '!=', 4)->where('customer_id', $customerId)->sum('total_cost');
            return $orders;
        }
        // if (Auth::user()->can('report_view')) {
            $orders = Orders::with('Details')->where('status', '!=', 4)->sum('total_cost');
            return $orders;
        // }
    }
    public function getOrders($type)
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $date = Carbon::now()->format('Y-m');
            $year = Carbon::now()->format('Y');
            $dateFrom = Carbon::today()->subDays(360)->format('Y-m-d');
            $dateTo = Carbon::now()->format('Y-m-d');
            $chartPoint = collect();
            $temp_date = $dateFrom;
            $callLogsByYear = Orders::where('customer_id', $customerId)->where('status', '!=', 4)->selectRaw("count(*) as total, Month(created_at) as month ")->groupBy('month')->where('created_at', 'like', "$year%")->get();
            $chartPointyear = collect();
            $totalMonth = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $orders_cost = collect();
            foreach ($totalMonth as $month) {
                $record =  $callLogsByYear->where('month', $month)->first();
                $orders_total = Orders::where('customer_id', $customerId)->where('status', '!=', 4)->whereMonth('created_at', $month)->sum('total_cost');
                $orders_cost->push($orders_total);
                if ($record) {
                    $chartPointyear->push($record->total);
                } else {
                    $chartPointyear->push(0);
                }
            }
            if ($type == 'sales') {
                $point = str_replace('"', "", $chartPointyear);
                $chartPointsyear = $point;
                return $chartPointsyear;
            } else if ($type == 'sales_cost') {
                $cost = str_replace('"', "", $orders_cost);
                $orders_cost = $cost;
                return $orders_cost;
            }
        }
        // if (Auth::user()->can('report_view')) {
            $date = Carbon::now()->format('Y-m');
            $year = Carbon::now()->format('Y');
            $dateFrom = Carbon::today()->subDays(360)->format('Y-m-d');
            $dateTo = Carbon::now()->format('Y-m-d');
            $chartPoint = collect();
            $temp_date = $dateFrom;
            $callLogsByYear = Orders::selectRaw("count(*) as total, Month(created_at) as month ")->where('status', '!=', 4)->groupBy('month')->where('created_at', 'like', "$year%")->get();
            $chartPointyear = collect();
            $totalMonth = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $orders_cost = collect();
            foreach ($totalMonth as $month) {
                $record =  $callLogsByYear->where('month', $month)->first();
                $orders_total = Orders::whereMonth('created_at', $month)->where('status', '!=', 4)->sum('total_cost');
                $orders_cost->push($orders_total);
                if ($record) {
                    $chartPointyear->push($record->total);
                } else {
                    $chartPointyear->push(0);
                }
            }
            if ($type == 'sales') {
                $point = str_replace('"', "", $chartPointyear);
                $chartPointsyear = $point;
                return $chartPointsyear;
            } else if ($type == 'sales_cost') {
                $cost = str_replace('"', "", $orders_cost);
                $orders_cost = $cost;
                return $orders_cost;
            }
        // }
    }
    public function getProfit()
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $orders = Orders::with('Details')->where('status', '!=', 4)->where('customer_id', $customerId)->sum('total_cost');
            $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
            foreach ($customerProducts as $key => $cProduct) {
                $products = Products::with('inventory')->where('id', $cProduct->product_id)->get();
                $purchases = 0;
                foreach ($products as $key => $product) {
                    $inventory = Inventory::where('product_id', $product->id)->sum('qty');
                    if ($inventory) {
                        $purchases += (float)($product->price) * $inventory;
                    }
                }
                $profit = $orders - $purchases;
                if ($profit < 0) {
                    $profit = 0;
                }
                return $profit;
            }
        }
        // if (Auth::user()->can('report_view')) {
            $orders = Orders::with('Details')->where('status', '!=', 4)->sum('total_cost');
            $products = Products::with('inventory')->get();
            $purchases = 0;
            foreach ($products as $key => $product) {
                $inventory = Inventory::where('product_id', $product->id)->sum('qty');
                if ($inventory) {
                    $purchases += (float)($product->price) * $inventory;
                }
            }
            $profit = $orders - $purchases;
            if ($profit < 0) {
                $profit = 0;
            }
            return $profit;
        // }
    }
    public function getEarning()
    {
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $earning = Orders::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->where('customer_id', $customerId)
                ->sum('total_cost');
            return $earning;
        }
        // if (Auth::user()->can('report_view')) {
            $earning = Orders::whereMonth('created_at', date('m'))->where('status', '!=', 4)
                ->whereYear('created_at', date('Y'))
                ->sum('total_cost');
            return $earning;
        // }
    }
    public function earningComparison()
    {
        $last_month_earning = Orders::whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->where('status', '!=', 4)
            ->whereYear('created_at', date('Y'))
            ->sum('total_cost');
        $current_month_earning = $this->getEarning();
        if ($last_month_earning <= 0) {
            return '0';
        }
        $percentage = (($last_month_earning - $current_month_earning) / $last_month_earning) * 100;
        return number_format($percentage, 2);
    }
    public function getOrderByStatus($type = null)
    {
        // if (Auth::user()->can('report_view')) {
            $total = Orders::count();
            $new = Orders::where('status', 0)->count();
            $in_process = Orders::where('status', 1)->count();
            $shipped = Orders::where('status', 2)->count();
            $delivered = Orders::where('status', 3)->count();
            $cancelled = Orders::where('status', 4)->count();
            $percentage_array = array();
        // }
        if (Auth::user()->hasRole('customer')) {
            $customerId = $this->getCustomerId();
            $total = Orders::where('customer_id', $customerId)->count();
            $new = Orders::where('status', 0)->where('customer_id', $customerId)->count();
            $in_process = Orders::where('status', 1)->where('customer_id', $customerId)->count();
            $shipped = Orders::where('status', 2)->where('customer_id', $customerId)->count();
            $delivered = Orders::where('status', 3)->where('customer_id', $customerId)->count();
            $cancelled = Orders::where('status', 4)->where('customer_id', $customerId)->count();
            $percentage_array = array();
        }
        if ($total > 0) {
            array_push($percentage_array, round(($new / $total) * 100, 2));
            array_push($percentage_array, round(($in_process / $total) * 100, 2));
            array_push($percentage_array, round(($shipped / $total) * 100, 2));
            array_push($percentage_array, round(($delivered / $total) * 100, 2));
            array_push($percentage_array, round(($cancelled / $total) * 100, 2));
        } else {
            array_push($percentage_array, 0);
            array_push($percentage_array, 0);
            array_push($percentage_array, 0);
            array_push($percentage_array, 0);
            array_push($percentage_array, 0);
        }
        if ($type == 'graph') {
            return implode(',', $percentage_array);
        } else {
            return $order_counts = [
                'total' => $total,
                'new' => $new,
                'in_process' => $in_process,
                'shipped' => $shipped,
                'delivered' => $delivered,
                'cancelled' => $cancelled
            ];
        }
    }
    public function calculateSkuSalesTotal($id, $order_id)
    {
        $sku = Sku::with('sku_product')->where('id', $id)->first();
        $tempArr = [];
        $sku_purchase_total = 0;
        $sku_sell_price = 0;
        // calculating purchase price of sku
        foreach ($sku->sku_product as $key => $sku_product) {
            $sku_product->load('product');
            $sku_purchase_total += $sku_product->quantity * $sku_product->product->price;
            $sku_sell_price += $sku_product->selling_cost;
        }
        // calculate sale price of sku
        $sku_sales_total = 0;
        $order_details = OrderDetails::with('product')->where('sku_id', $id)->where('order_id', $order_id)->get();
        foreach ($order_details as $key => $detail) {
            $sku_sales_total += $detail->cost_of_good;
        }
        // dd('sku_purchase_price', $sku_purchase_total, 'sku_sell_price', $sku_sell_price, 'sku_sales_total',  $sku_sales_total);
    }
    public function getMonthlyProfit()
    {
        if (Auth::user()->hasRole('customer')) {
            $current_month = Carbon::now();
            $orders_array = [
                'month' => [],
                'profit' => []
            ];
            for ($i = 0; $i < 12; $i++) {
                $order_details = OrderDetails::whereMonth('created_at', $current_month->format('m'))->whereYear('created_at', $current_month->format('Y'))->get();
                array_push($orders_array['month'], '\'' . $current_month->format('M') . '\'');
                $sales = 0;
                $purchase = 0;
                $profit = 0;
                foreach ($order_details as $key => $detail) {
                    $sales += $detail->sku_selling_cost * $detail->qty;
                    $purchase += $detail->sku_purchasing_cost * $detail->qty;
                }
                $profit += $sales - $purchase;
                array_push($orders_array['profit'], round($profit, 2));
                $current_month = $current_month->subMonth();
            }
            $orders_array['month'] = implode(',', array_reverse($orders_array['month']));
            $orders_array['profit'] = implode(',', array_reverse($orders_array['profit']));
            return $orders_array;
        }
        // if (Auth::user()->can('report_view')) {
            $current_month = Carbon::now();
            $orders_array = [
                'month' => [],
                'profit' => []
            ];
            for ($i = 0; $i < 12; $i++) {
                $order_details = OrderDetails::whereMonth('created_at', $current_month->format('m'))->whereYear('created_at', $current_month->format('Y'))->get();
                array_push($orders_array['month'], '\'' . $current_month->format('M') . '\'');
                $sales = 0;
                $purchase = 0;
                $profit = 0;
                foreach ($order_details as $key => $detail) {
                    if (isset($detail)) {
                        if (!is_numeric($detail->qty)) {
                            $detailQty = 0;
                        } else {
                            $detailQty = $detail->qty;
                        }
                        if ($detail->sku_selling_cost == '' || $detail->sku_selling_cost == 0) {
                            $detailSkuSellingCost = 0;
                        } else {
                            $detailSkuSellingCost = $detail->sku_selling_cost;
                        }
                        if ($detail->sku_purchasing_cost == '' || $detail->sku_purchasing_cost == 0) {
                            $detailSkuPurchasingCost = 0;
                        } else {
                            $detailSkuPurchasingCost = $detail->sku_purchasing_cost;
                        }
                        $sales += $detailSkuSellingCost * $detailQty;
                        $purchase += $detailSkuPurchasingCost * $detailQty;
                    } else {
                        $sales = $sales;
                        $purchase = $purchase;
                    }
                }
                $profit += $sales - $purchase;
                array_push($orders_array['profit'], round($profit, 2));
                $current_month = $current_month->subMonth();
            }
            $orders_array['month'] = implode(',', array_reverse($orders_array['month']));
            $orders_array['profit'] = implode(',', array_reverse($orders_array['profit']));
            return $orders_array;
        // }
    }
    public function getCustomerId()
    {
        try {
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
                $customerId = $customerUser->customer_id;
            }
            return $customerId;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
