<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use PaginationTrait;
use App\Models\Setting;
use App\Traits\Forecast;
use Carbon\CarbonPeriod;
use App\AdminModels\Units;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Jobs\ProductReport;
use App\Models\OrderReturn;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Category;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\Models\CustomerHasSku;
use PhpParser\Node\Stmt\Label;
use App\Models\CustomerProduct;
use App\Traits\AllProductReport;
use App\AdminModels\OrderDetails;
use App\AdminModels\OtwInventory;
use App\Models\OrderReturnDetail;
use App\Models\ProductLabelOrder;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\InventoryHistory;
use App\AdminModels\UpcomingInventory;
use App\Console\Commands\SendEmail;
use App\Http\Controllers\Admin\HomeController;
use App\Models\SkuOrder;
use App\Models\SkuOrderDetails;
use App\Traits\LabelQty;
use Illuminate\Support\Facades\Artisan;

class ReportingController extends Controller
{
    use Forecast, AllProductReport, LabelQty;
    public $todayDate;
    public $homeController;
    public function __construct(HomeController $homecontroller)
    {
        $this->middleware('auth');
        $this->todayDate = Carbon::now()->format('Y-m-d');
        $this->homeController = $homecontroller;
        // dd($this->homeController->getAllProfit());
    }
    public function changeDateFormat($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
    public function addDays($days)
    {
        return Carbon::now()->addDays($days)->format('Y-m-d');
    }
    public function subDays($days)
    {
        return Carbon::now()->subDays($days)->format('Y-m-d');
    }
    public function createPeriod($date1, $date2)
    {
        return CarbonPeriod::create($date1, $date2);
    }
    public function customerSales($customerId = null, $from = null, $to = null)
    {
        // dd($from, $to);
        $orders = DB::table('orders')->where('orders.status', '!=', 4)
            ->where('orders.customer_id', $customerId);
        if (!empty($from)) {
            if ($from == $to) {
                $orders = $orders->whereDate('orders.created_at', Carbon::parse($to)->format('Y-m-d'));
            } else {
                $orders = $orders->whereDate('orders.created_at', '>=', Carbon::parse($from)->format('Y-m-d'))->whereDate('orders.created_at', '<=', Carbon::parse($to)->format('Y-m-d'));
            }
        }
        $totalOrders = $orders->count();
        $totalMailers = $orders->sum('mailerqty');
        $query = $orders->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                // DB::raw('SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.customer_service_charges,"$[0].labels"))) as label_qty'),
                DB::raw('SUM(order_details.sku_selling_cost) as selling_amount'),
                DB::raw('SUM(order_details.sku_purchasing_cost * order_details.qty) as purchasing_amount')
            )
            ->where('order_details.qty', '>', 0)
            ->where('orders.deleted_at', NULL)
            ->where('order_details.deleted_at', NULL)
            ->first();
        return ['query' => $query, 'total_orders' => $totalOrders, 'total_mailers' => $totalMailers];
    }
    public function customerCharges($customerId = null, $from = null, $to = null)
    {
        $pick_pack = 0;
        $orders = DB::table('order_details')->where('order_details.deleted_at', NULL)
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.qty', '>', 0)
            ->where('orders.customer_id', $customerId)
            ->where('orders.status', '!=', 4)
            ->select('order_details.service_charges_detail', 'order_details.qty', 'orders.discounted_postage_status', 'orders.discounted_charges', 'orders.all_postage_charges');
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        if (!empty($from)) {
            if ($from == $to) {
                $orders = $orders->whereDate('orders.created_at', '>=', $from)->whereDate('orders.created_at', '<=', $to);
            } else {
                $orders = $orders->whereDate('orders.created_at', '>=', $from)->whereDate('orders.created_at', '<=', $to);
            }
        }
        $getorders = $orders->get();
        if (!is_null($getorders)) {
            $label = 0;
            $mailerCharges = 0;
            $postage = 0;
            $totalDiscountedPostage = 0;
            $totalPostageCharges = 0;
            $mailer_And_Postages = $orders->groupBy('order_id')->get();
            foreach ($mailer_And_Postages as $key => $mailerpostage) {
                if ($mailerpostage->qty > 0) {
                    $mailerpostageCharges = json_decode($mailerpostage->service_charges_detail);
                    foreach ($mailerpostageCharges as $key => $charge) {
                        if ($charge->slug == 'mailer_price') {
                            $mailerCharges += $charge->price;
                        }
                        if ($charge->slug == 'postage_price') {
                            $postage += $charge->price;
                        }
                    }
                }
            }
            foreach ($getorders as $dkey => $q) {
                if ($q->qty > 0) {
                    if ($q->discounted_postage_status == 1) {
                        $totalDiscountedPostage = 0;
                        $totalPostageCharges = 0;
                        $discounted_Postage_Charges = json_decode($q->discounted_charges);
                        foreach ($discounted_Postage_Charges as $pckey => $postageCharge) {
                            $totalDiscountedPostage += $postageCharge->price;
                        }
                        $allPostageCharges = json_decode($q->all_postage_charges);
                        foreach ($allPostageCharges as $tpckey => $totalPostageCharge) {
                            $totalPostageCharges += $totalPostageCharge->price;
                        }
                    }
                    $charges = json_decode($q->service_charges_detail);
                    foreach ($charges as $key => $charge) {
                        $pick = 0;
                        $pack = 0;
                        if ($charge->slug == 'labels_price') {
                            $label += $charge->price;
                        }
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
        $getNewOrders = Orders::where('deleted_at', NULL)->where('customer_id', $customerId)->where('status', '!=', 4)->whereDate('orders.created_at', '>=', $from)->whereDate('orders.created_at', '<=', $to)->get();
        $totalPostageCost = 0;
        $totalDiscountedPostageCost = 0;
        foreach ($getNewOrders as $nOkey => $newOrder) {
            if (isset($newOrder)) {
                if ($newOrder->discounted_postage_status == 1) {
                    $discountedPostageChargesData = json_decode($newOrder->all_postage_charges);
                    if (!is_null($discountedPostageChargesData)) {
                        foreach ($discountedPostageChargesData as $dpckey => $pscst) {
                            $totalPostageCost += $pscst->price;
                        }
                    }
                    $discountedPostageChargesData = json_decode($newOrder->discounted_charges);
                    if (!is_null($discountedPostageChargesData)) {
                        foreach ($discountedPostageChargesData as $dpckey => $pscst) {
                            $totalDiscountedPostageCost += $pscst->price;
                        }
                    }
                }
            }
        }
        $totalPostageProfit = $totalPostageCost - $totalDiscountedPostageCost;
        return ['pick_pack' => $pick_pack, 'labels' => $label, 'mailer_charges' => $mailerCharges, 'postage_charges' => $postage, 'discounted_postage_total_cost' => $totalDiscountedPostageCost, 'postage_profit' => $totalPostageProfit];
    }
    public function getSkuProfitWithCustomer()
    {
    }
    public function profitReport($type = null, Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->min_date)) {
                if ($request->max_date == null) {
                    $request->merge(['max_date' => $this->todayDate]);
                } else {
                    $request->max_date = $request->max_date;
                }
            }
            if ($type == 'customer') {
                $customers = Customers::withTrashed()->get();
                foreach ($customers as $customer_key => $customer) {
                    $pick_pack = 0;
                    $labels = 0;
                    $mailer_Charges = 0;
                    $postage_Charges = 0;
                    $purchasings = 0;
                    $sales = 0;
                    $totalOrders = 0;
                    $from = $request->min_date;
                    $to = $request->max_date;
                    $query = $this->customerSales($customer->id, $from, $to);
                    $totalOrders = $query['total_orders'];
                    $totalMailers = $query['total_mailers'];
                    $serviceCharges = $this->customerCharges($customer->id, $from, $to);
                    if (isset($query['query']->purchasing_amount)) {
                        $purchasings = $query['query']->purchasing_amount;
                    }
                    if (isset($query['query']->purchasing_amount)) {
                        $sales = $query['query']->selling_amount;
                    }
                    if (isset($serviceCharges)) {
                        $labels = $serviceCharges['labels'];
                    }
                    if (isset($serviceCharges)) {
                        $pick_pack = $serviceCharges['pick_pack'];
                    }
                    if (isset($serviceCharges)) {
                        $mailer_Charges = $serviceCharges['mailer_charges'];
                    }
                    if (isset($serviceCharges)) {
                        $postage_Charges = $serviceCharges['postage_charges'];
                    }
                    if (isset($serviceCharges)) {
                        $postage_Profit = $serviceCharges['postage_profit'];
                    }
                    if (isset($serviceCharges)) {
                        $discounted_postage_total_cost = $serviceCharges['discounted_postage_total_cost'];
                    }
                    $returnChargesOfCustomer = 0;
                    $returnedAmount = 0;
                    $orderReturn = OrderReturn::where('customer_id', $customer->id)->where('status', '!=', 2)->where('status', '!=', 3)->where('total_price', '>', 0)->where('total_qty', '>', 0);
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
                    $customers[$customer_key]->purchasing_amount = $purchasings;
                    $customers[$customer_key]->selling_amount = $sales;
                    $customers[$customer_key]->total_orders = $totalOrders;
                    $customers[$customer_key]->total_mailers = $totalMailers;
                    $customers[$customer_key]->pick_pack = $pick_pack;
                    $customers[$customer_key]->labels = $labels;
                    $customers[$customer_key]->mailer_charges = $mailer_Charges;
                    $customers[$customer_key]->postage_charges = $postage_Charges;
                    $customers[$customer_key]->postage_profit = $postage_Profit;
                    $customers[$customer_key]->discounted_postage_total_cost = $discounted_postage_total_cost;
                    $customers[$customer_key]->return_service_charges = $returnChargesOfCustomer;
                    if ($customers[$customer_key]->total_orders <= 0) {
                        if ($customers[$customer_key]->return_service_charges <= 0) {
                            unset($customers[$customer_key]);
                        }
                    }
                }
                // $customers = ($request->show_all == 'true') ? $customers : $this->purify($customers, 'profit');
                return $this->getCustomerDataTable($customers);
            } else if ($type == 'sku') {
                // $from = $request->min_date;
                // $to = $request->max_date;
                // $skus_array = DB::table('skus')
                //                 ->join('order_details', 'skus.id', '=', 'order_details.sku_id')
                //                 ->join('');

                // return $this->getSkuDataTable($skus_array);
            } else if ($type == 'product' || $type == 'category') {
                if ($type == 'category') {
                    $categories = Category::with('Products.sku_products.order_details')->get();

                    foreach ($categories as $ckey => $category) {
                        $sales = 0;
                        $purchases = 0;
                        $totalOrders = 0;
                        $pick_pack = 0;
                        $productName = '';
                        if (isset($category->Products)) {
                            foreach ($category->Products as $pkey => $product) {
                                if (isset($product->sku_products)) {
                                    foreach ($product->sku_products as $skukey => $skuProduct) {
                                        if (isset($skuProduct->order_details)) {
                                            if ($product->id == $skuProduct->product_id) {
                                                foreach ($skuProduct->order_details as $odkey => $detail) {
                                                    if ($detail->qty > 0) {
                                                        if ($skuProduct->sku_id == $detail->sku_id) {
                                                            $charges = json_decode($detail->service_charges_detail);
                                                            foreach ($charges as $chargeskey => $charge) {
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
                                                            $sales += $detail->sku_selling_cost;
                                                            $purchases += $detail->sku_purchasing_cost * $detail->qty;
                                                            $totalOrders += $detail->qty;
                                                        }
                                                    }
                                                }
                                            } else {
                                                $sales = 0;
                                                $purchases = 0;
                                                $totalOrders = 0;
                                                $pick_pack = 0;
                                            }
                                        }
                                    }
                                }
                                $productName = $product->name;
                            }
                        }
                        $categories[$ckey]->sales = $sales;
                        $categories[$ckey]->purchases = $purchases;
                        $categories[$ckey]->total_orders = $totalOrders;
                        $categories[$ckey]->pick_pack = $pick_pack;
                        $categories[$ckey]->product_name = $productName;
                    }
                    return $this->getProductDataTable($categories);
                }
                if ($type == 'product') {
                    $products = Products::with('sku_products.order_details')->get();
                    foreach ($products as $pkey => $product) {
                        $sales = 0;
                        $purchases = 0;
                        $totalOrders = 0;
                        $pick_pack = 0;
                        if (isset($product->sku_products)) {
                            foreach ($product->sku_products as $skukey => $skuProduct) {
                                if (isset($skuProduct->order_details)) {
                                    if ($product->id == $skuProduct->product_id) {
                                        foreach ($skuProduct->order_details as $odkey => $detail) {
                                            if ($detail->qty > 0) {
                                                if ($skuProduct->sku_id == $detail->sku_id) {
                                                    $charges = json_decode($detail->service_charges_detail);
                                                    foreach ($charges as $chargeskey => $charge) {
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
                                                    $sales += $detail->sku_selling_cost;
                                                    $purchases += $detail->sku_purchasing_cost * $detail->qty;
                                                    $totalOrders += $detail->qty;
                                                }
                                            }
                                        }
                                    } else {
                                        $sales = 0;
                                        $purchases = 0;
                                        $totalOrders = 0;
                                        $pick_pack = 0;
                                    }
                                }
                            }
                        }
                        $products[$pkey]->sales = $sales;
                        $products[$pkey]->purchases = $purchases;
                        $products[$pkey]->total_orders = $totalOrders;
                        $products[$pkey]->pick_pack = $pick_pack;
                    }
                    return $this->getProductDataTable($products);
                }
            }
        }
        $dataValues = $this->homeController->getAllProfit();
        return view('admin.reports.profit', compact('dataValues'));
    }
    public function getFilteredProfit(Request $request)
    {
        if ($request->ajax()) {
            $dataValues = $this->homeController->getAllProfit($request->from, $request->to);
            return response()->json(['status' => true, 'total_profit' => $dataValues['total_profit']]);
        }
    }
    public function salesReport($type = null, Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->min_date)) {
                if ($request->max_date == null) {
                    $request->merge(['max_date' => $this->todayDate]);
                } else {
                    $request->max_date = $request->max_date;
                }
            }
            if ($type == 'customer') {
                $customers = Customers::withTrashed()->get();
                foreach ($customers as $customer_key => $customer) {
                    $pick_pack = 0;
                    $sales = 0;
                    $totalOrders = 0;
                    $from = $request->min_date;
                    $to = $request->max_date;
                    $query = $this->customerSales($customer->id, $from, $to);
                    $totalOrders = $query['total_orders'];
                    $totalMailers = $query['total_mailers'];
                    if (isset($query['query']->purchasing_amount)) {
                        $sales = $query['query']->selling_amount;
                    }
                    $customers[$customer_key]->selling_amount = $sales;
                    $customers[$customer_key]->total_orders = $totalOrders;
                    $customers[$customer_key]->total_mailers = $totalMailers;
                    if ($customers[$customer_key]->total_orders <= 0) {
                        if ($customers[$customer_key]->return_service_charges <= 0) {
                            unset($customers[$customer_key]);
                        }
                    }
                }
                return $this->getCustomerDataTable($customers);
            } else if ($type == 'sku') {
                $from = $request->min_date;
                $to = $request->max_date;
                // DB::enableQueryLog(); // Enable query log
                $skus_array = DB::table('skus')
                    ->join('order_details', 'skus.id', '=', 'order_details.sku_id')
                    ->join('labels', 'skus.brand_id', '=', 'labels.id')
                    ->join('customers', 'labels.customer_id', '=', 'customers.id');
                if (!empty($request->min_date)) {
                    $skus_array = $skus_array->whereDate('order_details.created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
                    if (!empty($request->max_date)) {
                        $skus_array = $skus_array->whereDate('order_details.created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
                    }
                }
                $skus_array = $skus_array->select(['skus.name as sku_name', DB::raw('SUM(sku_selling_cost) as sales'), 'labels.brand as brand_name', 'customers.customer_name'])
                    ->where('skus.deleted_at', NULL)
                    ->where('order_details.deleted_at', NULL)
                    ->where('order_details.status', '!=', 4)
                    ->where('labels.deleted_at', NULL)
                    ->where('customers.deleted_at', NULL)
                    ->groupBy('skus.id')
                    ->get();
                // Log::info(DB::getQueryLog()); // Show results of log
                // dd($skus_array);
                return $this->getSkuDataTable($skus_array);
            } else if ($type == 'product' || $type == 'category' || $type == 'dailysaletable') {
                if ($type == 'category') {
                    $categories = DB::table('category')
                        ->join('products', 'category.id', '=', 'products.category_id')
                        ->join('sku_products', 'products.id', '=', 'sku_products.product_id')
                        // ->join('order_details', function($join) {
                        //     $join->on('order_details.order_id', '=', 'sku_order_details.order_id')
                        //     ->on('order_details.sku_id', '=', 'sku_order_details.sku_id');
                        // })
                        ->join('order_details', 'sku_products.sku_id', '=', 'order_details.sku_id')
                        ->select('category.name as category_name', 'products.name as product_name', DB::raw('SUM(order_details.sku_selling_cost) as sales'))
                        ->where('order_details.status', '!=', 4)
                        ->where('order_details.deleted_at', NULL)
                        ->where('products.deleted_at', NULL)
                        ->where('sku_products.deleted_at', NULL)
                        ->groupBy('category.id');
                    if (!empty($request->min_date)) {
                        $categories = $categories->whereDate('order_details.created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
                        if (!empty($request->max_date)) {
                            $categories = $categories->whereDate('order_details.created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
                        }
                    }
                    $categories = $categories->get();
                    return $this->getSkuDataTable($categories);
                }
                if ($type == 'product') {
                    $products = DB::table('products')
                        ->join('category', 'products.category_id', '=', 'category.id')
                        ->join('sku_products', 'products.id', '=', 'sku_products.product_id')
                        ->join('order_details', 'sku_products.sku_id', '=', 'order_details.sku_id')
                        ->select('category.name as category_name', 'products.name as product_name', DB::raw('SUM(order_details.sku_selling_cost) as sales'))
                        ->where('order_details.status', '!=', 4)
                        ->where('order_details.deleted_at', NULL)
                        ->where('products.deleted_at', NULL)
                        ->where('sku_products.deleted_at', NULL)
                        ->groupBy('products.id');
                    if (!empty($request->min_date)) {
                        $products = $products->whereDate('order_details.created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
                        if (!empty($request->max_date)) {
                            $products = $products->whereDate('order_details.created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
                        }
                    }
                    $products = $products->get();
                    return $this->getSkuDataTable($products);
                }
                // dd($categories);
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if ($type == "dailysaletable") {
                    $products = Products::with('category', 'customer_Product.customer', 'inventory', 'sku_products.sku.order_details')->get()->toArray();

                    foreach ($products as $index => $product) {
                        $skuProducts = SkuProducts::where('product_id', $product['id'])->get();

                        if (!array_key_exists('dailysaletable', $product)) {
                            $products[$index]['dailysaletable'] = 0;
                        }
                        if (!array_key_exists('dated', $product)) {
                            $products[$index]['dated'] = '';
                        }

                        foreach ($skuProducts as $skuProduct) {
                            if (isset($skuProduct)) {
                                $customerOrder = OrderDetails::where('sku_id', $skuProduct->sku_id);
                                if ($request->min_date) {
                                    if ($request->max_date) {
                                        $customerOrder = $customerOrder->whereBetween('created_at', [Carbon::parse($request->min_date)->format('Y-m-d'), Carbon::parse($request->max_date)->format('Y-m-d')])->get();
                                    } else {
                                        $customerOrder = $customerOrder->whereBetween('created_at', [Carbon::parse($request->min_date)->format('Y-m-d'), Carbon::now()])->get();
                                    }
                                } else {
                                    $customerOrder = $customerOrder->whereDate('created_at', '=', date('Y-m-d', strtotime(Carbon::now())))->get();
                                }
                                foreach ($customerOrder as $custOrder) {
                                    if (array_key_exists('dailysaletable', $products[$index])) {
                                        $products[$index]['dailysaletable'] += $custOrder->qty;
                                    } else {
                                        $products[$index]['dailysaletable'] = $custOrder->qty;
                                    }
                                    if (array_key_exists('dated', $products[$index])) {
                                        $products[$index]['dated'] = $custOrder->created_at->format('Y-m-d');
                                    }
                                }
                            }
                        }
                    }
                    $products = ($request->show_all == 'true') ? $products : $this->purify($products, 'dailysaletable');
                    return $this->getProductDataTable2($products);
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            }
        }
        return view('admin.reports.sales');
    }
    public function getCustomerDataTable($customers)
    {
        if (!is_array($customers)) {
            $customers = $customers->toArray();
        }
        return Datatables::of($customers)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row['customer_name'];
            })
            ->addColumn('total_orders', function ($row) {
                if (array_key_exists('total_orders', $row)) {
                    return number_format($row['total_orders']);
                } else {
                    return 0;
                }
            })
            ->addColumn('total_mailers', function ($row) {
                if (array_key_exists('total_mailers', $row)) {
                    return number_format($row['total_mailers']);
                } else {
                    return 0;
                }
            })
            ->addColumn('selling_amount', function ($row) {
                if (array_key_exists('selling_amount', $row)) {
                    return $row['selling_amount'];
                } else {
                    return 0;
                }
            })
            ->addColumn('purchasing_amount', function ($row) {
                if (array_key_exists('purchasing_amount', $row)) {
                    return $row['purchasing_amount'];
                } else {
                    return 0;
                }
            })
            ->addColumn('cog_profit', function ($row) {
                if (array_key_exists('selling_amount', $row) && array_key_exists('purchasing_amount', $row)) {
                    return ($row['selling_amount'] - $row['purchasing_amount']);
                } else {
                    return 0;
                }
            })
            ->addColumn('labels', function ($row) {
                if (array_key_exists('labels', $row)) {
                    return $row['labels'];
                } else {
                    return 0;
                }
            })
            ->addColumn('profit', function ($row) {
                if (array_key_exists('selling_amount', $row) && array_key_exists('purchasing_amount', $row)) {
                    // return ($row['selling_amount'] - $row['purchasing_amount']) + $row['pick_pack'] + $row['return_service_charges'];
                    return ((float)($row['selling_amount']) - (float)($row['purchasing_amount'])) + (float)($row['pick_pack']) + (float)($row['return_service_charges']) + (float)($row['postage_profit']);
                } else {
                    return 0;
                }
            })
            ->addColumn('pick_pack', function ($row) {
                if (array_key_exists('selling_amount', $row) && array_key_exists('purchasing_amount', $row)) {
                    return $row['pick_pack'];
                } else {
                    return 0;
                }
            })
            ->addColumn('mailer_charges', function ($row) {
                if (array_key_exists('mailer_charges', $row)) {
                    return $row['mailer_charges'];
                } else {
                    return 0;
                }
            })
            ->addColumn('postage_charges', function ($row) {
                if (array_key_exists('postage_charges', $row)) {
                    return $row['postage_charges'];
                } else {
                    return 0;
                }
            })
            ->addColumn('discounted_postage_profit', function ($row) {
                if (array_key_exists('postage_profit', $row)) {
                    return $row['postage_profit'];
                } else {
                    return 0;
                }
            })
            ->addColumn('discounted_postage_total_cost', function ($row) {
                if (array_key_exists('discounted_postage_total_cost', $row)) {
                    return $row['discounted_postage_total_cost'];
                } else {
                    return 0;
                }
            })
            ->addColumn('return_service_charges', function ($row) {
                if (array_key_exists('selling_amount', $row) && array_key_exists('purchasing_amount', $row)) {
                    return $row['return_service_charges'];
                } else {
                    return 0;
                }
            })
            ->make(true);
    }
    public function getSkuDataTable($skus)
    {
        // if (!is_array($skus)) {
        //     $skus = $skus->toArray();
        // }
        return Datatables::of($skus)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                if (isset($row->sku_name)) {
                    return $row->sku_name;
                }
            })
            ->addColumn('category_name', function ($row) {
                if (isset($row->category_name)) {
                    return $row->category_name;
                }
            })
            ->addColumn('product_name', function ($row) {
                if (isset($row->product_name)) {
                    return $row->product_name;
                }
            })
            ->addColumn('brand', function ($row) {
                if (isset($row->brand_name)) {
                    return $row->brand_name;
                }
            })
            ->addColumn('customer', function ($row) {
                if (isset($row->customer_name)) {
                    return $row->customer_name;
                }
            })
            ->addColumn('total_orders', function ($row) {
                // return $row->total_orders;
            })
            ->addColumn('sales', function ($row) {
                return $row->sales;
            })
            ->addColumn('purchases', function ($row) {
                // if ($row->purchases) {
                //     return '$' . number_format($row->purchases, 2);
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('pick_pack', function ($row) {
                // if ($row->pick_pack) {
                //     return '$' . number_format($row->pick_pack, 2);
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('return_charges', function ($row) {
                // if ($row->return_service_charges) {
                //     return '$' . number_format($row->return_service_charges, 2);
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('profit', function ($row) {
                // return ($row->sales - $row->purchases) + $row->pick_pack + $row->return_service_charges;
            })
            ->make(true);
    }
    public function getProductDataTable($products)
    {
        return Datatables::of($products)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return ucwords($row->name);
                // return $row->product_name;
            })
            ->addColumn('category', function ($row) {
                // return ucwords($row['category']['name']);
                return ucwords($row->name);
            })
            ->addColumn('total_orders', function ($row) {
                return number_format($row->total_orders);
            })
            ->addColumn('sales', function ($row) {
                return number_format($row->sales, 2);
            })
            ->addColumn('daily_sale', function ($row) {
                // if (array_key_exists('dailysaletable', $row)) {
                //     return number_format($row['dailysaletable']);
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('labels_profit', function ($row) {
                // if (array_key_exists('labelPrice', $row)) {
                //     return number_format($row['labelPrice']);
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('profit', function ($row) {
                // if (array_key_exists('selling_amount', $row)) {
                //     return "$" . number_format($row['selling_amount'] - $row['purchasing_amount'], 2);
                // } else {
                //     return '$0.00';
                // }
                // return "$".number_format($row['selling_cost'] - $row['purchasing_cost'],2);

            })

            ->make(true);
    }
    public function getProductDataTable2($products)
    {
        return Datatables::of($products)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row['dated'];
                // return $row['product_name'];
            })
            ->addColumn('name', function ($row) {
                return $row['name'];
                // return $row['product_name'];
            })
            ->addColumn('category', function ($row) {
                return ucwords($row['category']['name']);
                // return ucwords($row['category_name']);
            })
            ->addColumn('total_orders', function ($row) {
                if (array_key_exists('counts', $row)) {
                    return number_format($row['counts']);
                } else {
                    return 0;
                }
                // return number_format($row['totalOrders']);
            })
            ->addColumn('sales', function ($row) {
                if (array_key_exists('sales', $row)) {
                    return '$' . number_format($row['sales'], 2);
                } else {
                    return 0;
                }
                // return '$' . number_format($row['total_products_selling_cost'], 2);
            })
            ->addColumn('daily_sale', function ($row) {
                if (array_key_exists('dailysaletable', $row)) {
                    return number_format($row['dailysaletable']);
                } else {
                    return 0;
                }
            })
            ->addColumn('labels_profit', function ($row) {
                if (array_key_exists('labelPrice', $row)) {
                    return number_format($row['labelPrice']);
                } else {
                    return 0;
                }
            })
            ->addColumn('profit', function ($row) {
                if (array_key_exists('selling_amount', $row)) {
                    return "$" . number_format($row['selling_amount'] - $row['purchasing_amount'], 2);
                } else {
                    return '$0.00';
                }
                return "$" . number_format($row['selling_cost'] - $row['purchasing_cost'], 2);
                // return '$' . number_format($row['profit'], 2);
            })

            ->make(true);
    }
    public function getCategoryDataTable($categories)
    {
        return Datatables::of($categories)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                // return $row['name'];
                return $row['category_name'];
            })
            ->addColumn('total_orders', function ($row) {
                // if (array_key_exists('counts', $row)) {
                //     return $row['counts'];
                // } else {
                //     return 0;
                // }
                return number_format($row['total_counts']);
            })
            ->addColumn('sales', function ($row) {
                // if (array_key_exists('sales', $row)) {
                //     return '$' . number_format($row['sales'], 2);
                // } else {
                //     return 0;
                // }
                // return '$' . number_format($row['total_products_selling_cost'], 2);
            })
            ->addColumn('profit', function ($row) {
                // if (array_key_exists('selling_amount', $row)) {
                //     return "$" . number_format($row['selling_amount'] - $row['purchasing_amount'], 2);
                // } else {
                //     return '$0.00';
                // }
                return '$' . number_format($row['total_profit'], 2);
            })
            ->make(true);
    }
    public function getCategoryDataTable2($categories)
    {
        return Datatables::of($categories)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row['name'];
                // return $row['category_name'];
            })
            ->addColumn('total_orders', function ($row) {
                if (array_key_exists('counts', $row)) {
                    return $row['counts'];
                } else {
                    return 0;
                }
                // return number_format($row['total_counts']);
            })
            ->addColumn('sales', function ($row) {
                if (array_key_exists('sales', $row)) {
                    return '$' . number_format($row['sales'], 2);
                } else {
                    return 0;
                }
                // return '$' . number_format($row['total_products_selling_cost'], 2);
            })
            ->addColumn('profit', function ($row) {
                if (array_key_exists('selling_amount', $row)) {
                    return "$" . number_format($row['selling_amount'] - $row['purchasing_amount'], 2);
                } else {
                    return '$0.00';
                }
                // return '$' . number_format($row['total_profit'], 2);
            })
            ->make(true);
    }
    public function checkProducts($products)
    {
        foreach ($products as $key => $product) {
            if (!array_key_exists('purchasing_amount', $product)) {
                $products[$key]['purchasing_amount'] = 0.00;
            }
            if (!array_key_exists('selling_amount', $product)) {
                $products[$key]['selling_amount'] = 0.00;
            }
            if (!array_key_exists('sales', $product)) {
                $products[$key]['sales'] = 0.00;
            }
        }
        return $products;
    }
    public function purify($collection, $type)
    {
        if (!is_array($collection)) {
            $collection = $collection->toArray();
        }
        foreach ($collection as $key => $item) {
            if ($type == 'profit' && $item['selling_amount'] - $item['purchasing_amount'] <= 0) {
                unset($collection[$key]);
            } else if ($type == 'selling_amount' && $item['selling_amount'] <= 0) {
                unset($collection[$key]);
            } else if ($type == 'dailysaletable' && $item['dailysaletable'] <= 0) {
                unset($collection[$key]);
            }
        }
        return $collection;
    }
    public function forecastValues($row)
    {
        if ($row->automated_status == 1) {
            if ($row->days_left >= $row->threshold_val) {
                return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: green; color: white">' . $row->forecast_msg . '</span></center>';
            } else {
                if ($row->days_left == 'No Sales for ' . $row->forecast_days) {
                    return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: red; color: white">' . $row->forecast_msg . '</span></center>';
                } else if ($row->days_left < 0) {
                    return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: red; color: white">' . $row->forecast_msg . '</span></center>';
                } else {
                    return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: red; color: white">' . $row->forecast_msg . '' . '</span></center>';
                }
            }
        } else if ($row->forecast_status == 1) {
            if ($row->manual_threshold > 0) {
                if ($row->invent_qty < $row->manual_threshold) {
                    return '<center><span class="badge rounded-pill me-1" style="background-color: red; color: white">Order Now</span></center>';
                } else {
                    return '<center><span class="badge rounded-pill me-1" style="background-color: green; color: white">Enough</span></center>';
                }
            }
        } else if ($row->threshold_val > 0 || $row->forecast_days > 0) {
            if ($row->days_left >= $row->threshold_val) {
                return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: green; color: white">' . $row->forecast_msg . '' . '</span></center>';
            } else {
                if ($row->days_left < 0) {
                    return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: red; color: white">0d</span></center>';
                } else {
                    return '<center><span class="badge rounded-pill me-1" data-sorting="' . $row->forecast_msg . '" style="background-color: red; color: white">' . $row->forecast_msg . '' . '</span></center>';
                }
            }
        }
    }
    public function inventoryForecastReport(Request $request)
    {
        if ($request->ajax()) {
            $cid = $request->category_id;
            $columns = $request->get('columns');
            $orders = $request->get('order');
            $orderbyColAndDirection = [];
            foreach ($orders as $key => $value) {
                array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
            }
            $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
            function getQtyAlerts($row)
            {
                if ($row->forecast_status == 1) {
                    if ($row->invent_qty < $row->manual_threshold) {
                        return number_format($row->inventory_qty);
                    } else {
                        return number_format($row->inventory_qty);
                    }
                } else {
                    return number_format($row->inventory_qty);
                }
            }
            // calling function from trait ForcastTrait
            return Datatables::of($this->categoryProductData($cid))
                ->addIndexColumn()
                ->addColumn('sr', function ($row) {
                    return '';
                })
                ->addColumn('category_name', function ($row) {
                    return ucwords($row->category_name);
                })
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                // adding upcoming and otw
                ->addColumn('ordered', function ($row) {
                    $UpcomingInventory = UpcomingInventory::where('product_id', $row->id)->groupBy('product_id')->sum('qty');
                    return $UpcomingInventory;
                })
                ->addColumn('otw', function ($row) {
                    $OtwInventory = OtwInventory::where('product_id', $row->id)->groupBy('product_id')->sum('qty');
                    return $OtwInventory;
                })
                ->addColumn('weight', function ($row) {
                    return $row->weight;
                })
                ->addColumn('cog', function ($row) {
                    return '$' . number_format($row->cog, 2);
                })
                ->addColumn('shipping_cost', function ($row) {
                    return '$' . number_format($row->shipping_cost, 2);
                })
                ->addColumn('forecast_statuses', function ($row) {
                    if ($row->forecast_status == 1) {
                        return '<center><span class="badge rounded-pill me-1" style="background-color: brown; color: white">Manual Forecast Count</span></center>';
                    } else if ($row->automated_status == 1) {
                        return '<center><span class="badge rounded-pill me-1" style="background-color: blue; color: white">Default</span></center>';
                    } else if ($row->automated_status == 0) {
                        return '<center><span class="badge rounded-pill me-1" style="background-color: orange; color: white">Forecast Days</span></center>';
                    }
                })
                ->addColumn('value', function ($row) {
                    if (isset($row->product_unit)) {
                        $productUnit = Units::where('id', $row->product_unit)->first();
                        if (isset($productUnit)) {
                            if ($productUnit->unit_type == 1) {
                                return ucwords('lbs');
                            } else {
                                return ucwords($productUnit->name);
                            }
                        } else {
                            return 'Not set';
                        }
                    } else {
                        return 'Not set';
                    }
                })
                ->addColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })
                ->addColumn('is_active', function ($row) {
                    if ($row->is_active == 0) return '<span class="badge rounded-pill me-1" style="color: white; background-color: red">Not Active</span>';
                    if ($row->is_active == 1) return '<span class="badge rounded-pill me-1" style="color: white; background-color: green">Active</span>';
                })
                ->addColumn('image', function ($row) {
                    $url = asset('images/products/' . $row->image);
                    return '<img src="' . $url . '" border="0" width="40" class="img-rounded" align="center" />';
                })
                ->addColumn('pqty', function ($row) {
                    return $row->invent_qty;
                })
                ->setRowClass(function ($row) {
                    if ($row->forecast_status == 1) {
                        if ($row->inventory_qty < $row->manual_threshold) {
                            return 'dbRowColor';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                })
                ->addColumn('forecast_val', function ($row) {
                    return $this->forecastValues($row);
                    // if ($row->automated_status == 1) {
                    //   if ($row->days_left >= $row->threshold_val) {
                    //     return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: green; color: white">' .$row->days_left . 'd' . '</span></center>';
                    //   } else {
                    //     if ($row->days_left < 0) {
                    //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">0d</span></center>';
                    //     } else {
                    //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">' .$row->days_left . 'd' . '</span></center>';
                    //     }
                    //   }
                    // } else if ($row->forecast_status == 1) {
                    //   if ($row->manual_threshold > 0) {
                    //     if ($row->invent_qty < $row->manual_threshold) {
                    //       return '<center><span class="badge rounded-pill me-1" style="background-color: red; color: white">Order Now</span></center>';
                    //     } else {
                    //       return '<center><span class="badge rounded-pill me-1" style="background-color: green; color: white">Enough</span></center>';
                    //     }
                    //   }
                    // } else if ($row->threshold_val > 0 || $row->forecast_days > 0) {
                    //   if ($row->days_left >= $row->threshold_val) {
                    //     return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: green; color: white">' .$row->days_left . 'd' . '</span></center>';
                    //   } else {
                    //     if ($row->days_left <span 0) {
                    //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">0d</span></center>';
                    //     } else {
                    //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">' .$row->days_left . 'd' . '</span></center>';
                    //     }
                    //   }
                    // }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                          <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i data-feather="more-vertical"></i>
                          </button>
                          <div class="dropdown-menu">';
                    if (Auth::user()->can('inventory_create')) {
                        $btn .= '<a class="dropdown-item enter-pincode add-inventory" data-prod-id="' . $row->id . '" href="/product/' . $row->id . '/inventory" data-type="add"  data-bs-toggle="modal" data-bs-target="#inventoryModal">
                                  <i data-feather="plus"></i>
                                  <span>Add Inventory</span>
                              </a>';
                        $btn .= '<a class="dropdown-item enter-pincode reduce-inventory" data-prod-id="' . $row->id . '" href="/product/' . $row->id . '/reduce_inventory" data-type="reduce"  data-bs-toggle="modal" data-bs-target="#reduceinventoryModal">
                                  <i data-feather="minus"></i>
                                  <span>Reduce Inventory</span>
                              </a>';
                        $btn .= '<a class="dropdown-item enter-pincode reset-inventory" data-prod-id="' . $row->id . '">
                                          <i data-feather="minus-circle"></i>
                                          <span>Reset Inventory to Zero</span>
                                      </a>';
                    }
                    if (Auth::user()->can('product_update')) {
                        $btn .= '<a class="dropdown-item" href="/edit_category_product/' . $row->id . '">
                                <i data-feather="edit-2"></i>
                                <span>Edit Product</span>
                            </a>';
                    }

                    if (Auth::user()->can('product_delete')) {
                        $btn .= '<a class="dropdown-item" href="/delete/product/' . $row->id . '" onclick="confirmDelete(event)">
                                <i data-feather="trash-2"></i>
                                <span>Delete Product</span>
                            </a>';
                        // adding Upcoming
                        $btn .= '<a class="dropdown-item enter-pincode upcoming-inventory-each-item" data-prod-id="' . $row->id . '"  data-type="Upcoming"  data-bs-toggle="modal" data-bs-target="#upcomingInventoryModel">
                  <i data-feather="log-in"></i>
                  <span>Purchase Order</span>
               </a>';
                        //  Otw
                        $btn .= '<a class="dropdown-item enter-pincode otw-inventory-each-item" data-prod-id="' . $row->id . '"  data-type="otw"  data-bs-toggle="modal" data-bs-target="#otwInventoryModel">
                  <i data-feather="truck"></i>
                  <span>OTW</span>
               </a>';
                    }
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['action', 'is_active', 'image', 'forecast_val', 'pqty', 'forecast_statuses'])
                ->make(true);
        }
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('admin.reports.inventory_forecast_report', compact('categories'));
    }
    public function productReport(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $inventoryfirstdate = InventoryHistory::orderBy('date', 'ASC')->first();
            $today = date('Y-m-d', strtotime('1 days'));
            $date = '2020-01-01';
            if (isset($inventoryfirstdate)) {
                $date = $inventoryfirstdate->date;
            }
            $period = CarbonPeriod::create($date, $today);
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
            $orders = Orders::where('status', '!=', 4)->get();
            $customers = Customers::orderBy('id', 'ASC')->get();
            $productCustomers = CustomerProduct::with('product', 'customer')->where('product_id', $request->product_id)->groupBy('customer_id')->orderBy('created_at', 'ASC')->get();
            $productId = $request->product_id;
            $product = Products::where('id', $request->product_id)->first();
            $products = Products::get();
        } else {
            $inventoryfirstdate = InventoryHistory::orderBy('date', 'ASC')->first();
            $today = date('Y-m-d', strtotime('1 days'));
            if (isset($inventoryfirstdate)) {
                $date = $inventoryfirstdate->date;
            }
            $period = CarbonPeriod::create($date, $today);
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
            $orders = Orders::where('status', '!=', 4)->get();
            $customers = Customers::orderBy('id', 'ASC')->get();
            $productCustomers = CustomerProduct::with('product', 'customer')->where('product_id', $request->product_id)->groupBy('customer_id')->orderBy('created_at', 'ASC')->get();
            $productId = $request->product_id;
            $product = Products::where('id', $request->product_id)->first();
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
                $customerId = $customerUser->customer_id;
            }
            $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
            $products = array();
            $productsKeys = array('products');
            foreach ($customerProducts as $key => $cProduct) {
                $product = Products::where('id', $cProduct->product_id)->first();
                array_push($products, $product);
            }
            $products = (object)$products;
        }
        $data = array();
        return view('admin.reports.product_report', compact('product_inventory', 'products', 'customers', 'orders', 'period', 'productId', 'product', 'productCustomers', 'data'));
    }
    public function customerProducts(Request $request, $customer = null)
    {
        if ($request->ajax()) {
            if ($customer == 'all') {
                $products = Products::select('id', 'name')->get();
            } else {
                $products = DB::table('customer_products')
                    ->join('products', 'customer_products.product_id', 'products.id')
                    ->where('customer_products.customer_id', $customer)
                    ->select('products.*')->get();
            }
            return response()->json(['status' => true, 'products' => $products]);
        }
    }
    public function getCustomerBrandProducts(Request $request, $customer = null, $brand = null)
    {
        if ($request->ajax()) {
            if ($customer == 'all') {
                $products = Products::select('id', 'name')->get();
            } else {
                $products = DB::table('customer_has_products')
                    ->join('products', 'customer_has_products.product_id', 'products.id')
                    ->where('customer_has_products.customer_id', $customer)
                    ->where('customer_has_products.brand_id', $brand)
                    ->where('customer_has_products.deleted_at', NULL)
                    ->where('products.deleted_at', NULL)
                    ->select('products.*')->get();
            }
            return response()->json(['status' => true, 'products' => $products]);
        }
    }
    public function getCustomerOrders($productID, $customerID, $date)
    {
        $orderOut = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('sku_products', 'order_details.sku_id', '=', 'sku_products.sku_id')
            ->where('sku_products.product_id', '=', $productID)
            ->where('orders.customer_id', '=', $customerID)
            ->whereDate('orders.created_at', '=', $date)
            ->select(DB::raw('SUM(order_details.qty) as order_out'))->first();
        return $orderOut->order_out;
    }
    public function productSubmitReport(Request $request)
    {
        if ($request->ajax()) {
            try {
                $product = Products::where('id', $request->product_id)->select(['id', 'name', 'created_at'])->first();
                // $inventoryfirstdate = InventoryHistory::orderBy('created_at', 'ASC')->select(['created_at'])->first();
                $from = Carbon::now()->subDays(6)->format('Y-m-d');
                $to = Carbon::now()->format('Y-m-d');
                if (!is_null($request->time_duration)) {
                    if ($request->time_duration == 'yesterday') {
                        $from = Carbon::now()->subDays(1)->format('Y-m-d');
                    } else if ($request->time_duration == 'this_week') {
                        $from = Carbon::now()->startOfWeek()->format('Y-m-d');
                        $to = Carbon::now()->format('Y-m-d');
                    } else if ($request->time_duration == 'last_week') {
                        $previousWeek = strtotime('-1 week +1 day');
                        $start_week = strtotime("last monday midnight", $previousWeek);
                        $end_week = strtotime("next sunday", $start_week);
                        $start_week = date("Y-m-d", $start_week);
                        $end_week = date("Y-m-d", $end_week);
                        $from = $start_week;
                        $to = $end_week;
                    } else if ($request->time_duration == 'this_month') {
                        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
                        $to = Carbon::now()->format('Y-m-d');
                    } else if ($request->time_duration == 'last_month') {
                        $previousMonth = strtotime('-1 month +1 day');
                        $start_month = strtotime("first day of this month", $previousMonth);
                        $end_month = strtotime("last day of this month", $start_month);
                        $start_month = date("Y-m-d", $start_month);
                        $end_month = date("Y-m-d", $end_month);
                        $from = $start_month;
                        $to = $end_month;
                        // dd($from, $to);
                    } else if ($request->time_duration == 'last_six_months') {
                        $from = Carbon::now()->subDays(180)->format('Y-m-d');
                        $to = Carbon::now()->format('Y-m-d');
                    } else if ($request->time_duration == 'this_year') {
                        $from = Carbon::now()->startOfYear()->format('Y-m-d');
                        $to = Carbon::now()->format('Y-m-d');
                    } else if ($request->time_duration == 'last_year') {
                        $currentYear = Carbon::now()->format('Y');
                        $start_year = Carbon::parse(1)->setYear($currentYear)->subYear(1)->firstOfMonth()->format('Y-m-d');
                        $end_year = date('Y-m-d', strtotime('last day of December this year -1 years'));
                        $from = $start_year;
                        $to = $end_year;
                        // dd($from, $to);
                    }
                } else if (!is_null($request->from_date)) {
                    $from = Carbon::parse($request->from_date)->format('Y-m-d');
                    if (!empty($request->to_date)) {
                        $to = Carbon::parse($request->to_date)->format('Y-m-d');
                    }
                }
                if (isset($product)) {
                    $period = $this->createPeriod($this->changeDateFormat($from), $to);
                    if ($request->product_id == 'all') {
                    } else {
                        $data = array();
                        $keys = array('date', 'product_name', 'incoming', 'outgoing', 'customers');
                        $productCustomers = CustomerProduct::with('product:id,name', 'customer:id,customer_name')
                            ->where('product_id', $request->product_id);
                        if (!empty($request->customer)) {
                            $productCustomers = $productCustomers->where('customer_id', $request->customer);
                        }
                        $productCustomers = $productCustomers->groupBy('customer_id')
                            ->orderBy('created_at', 'ASC')
                            ->select(['customer_id', 'product_id'])
                            ->get();
                        $total = 0;
                        foreach (array_reverse($period->toArray()) as $key => $p) {
                            $inventoryHistory = InventoryHistory::whereDate('created_at', $p->format('Y-m-d'))->where('product_id', $request->product_id);

                            $incoming = $inventoryHistory->sum('manual_add') + $inventoryHistory->sum('supplier_inventory_received') + $inventoryHistory->sum('return_add');
                            $outGoing = $inventoryHistory->sum('manual_reduce');
                            $result = $incoming - $outGoing;

                            $customerSingleOut = array();
                            $singleOutKey = array('total_customer_order');
                            foreach ($productCustomers as $pcKey => $pCustomer) {
                                $orderOut = 0;
                                if (isset($pCustomer)) {
                                    $orders = Orders::where('customer_id', $pCustomer->customer_id)->whereDate('created_at', $p->format('Y-m-d'))->where('status', '!=', 4);
                                    if ($orders->exists()) {
                                        $orders = $orders->select(['id'])->get();
                                        foreach ($orders as $orderKey => $order) {
                                            if (isset($order)) {
                                                $orderQty = DB::table('sku_order_details')
                                                            ->join('order_details', function ($join) {
                                                                $join->on('sku_order_details.order_id', '=', 'order_details.order_id');
                                                                $join->on('sku_order_details.sku_id', '=', 'order_details.sku_id');
                                                            })
                                                            ->where('sku_order_details.order_id', $order->id)
                                                            ->where('sku_order_details.product_id', '=', $request->product_id)
                                                            ->sum('qty');
                                                $orderOut += $orderQty;
                                            }
                                        }
                                    }
                                }
                                array_push($customerSingleOut, array_combine($singleOutKey, [$orderOut]));
                                $total += $orderOut;
                            }
                            array_push($data, array_combine($keys, [$p->format('m/d/Y'), $product->name, $incoming, $outGoing, $customerSingleOut]));
                        }
                        return response()->json(['status' => true, 'msg' => 'Successfull', 'data' => $data, 'productCustomers' => $productCustomers]);
                    }
                } else {
                    return response()->json(['status' => false, 'msg' => 'Product not found']);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'msg' => 'Something went wrong']);
            }
        }
    }
    public function price($id)
    {
        $data = DB::table('order_details')
            ->join('sku_products', 'order_details.sku_id', '=', 'sku_products.sku_id')
            ->select(DB::raw('SUM(sku_products.selling_cost*order_details.qty) as price'))
            ->where('sku_products.product_id', '=', $id)
            ->get();
        return $data;
    }
    public function singleProductReport(Request $request)
    {
        if ($request->ajax()) {
            $qry = Products::with('customer_Product.customer', 'inventory', 'sku_products.sku.order_details');
            $data = $qry->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('pqty', function ($row) {
                    $qty = 0;
                    if (isset($row->inventory)) {
                        $qty = $row->inventory->qty;
                    }
                    return ($qty);
                })
                ->addColumn('price', function ($row) {
                    $data = $this->price($row->id);
                    return '$' . number_format($data->pluck('price')[0], 2);
                })
                ->make(true);
        }
    }
    public function showSingleProductReport(Request $request)
    {
        $duration = $request->time_duration;
        $total = 0;
        $html = '';
        $total_remaining = 0;
        $grandtotal = 0;
        $productid = $request->product_id;
        $date_range = $request->daterange;
        $date_range = explode(' - ', $date_range);
        $date1 = $date_range[0];
        $date2 = $date_range[1];
        $php_date1 = date('d-m-Y', strtotime($date1));
        $php_date2 = date('d-m-Y', strtotime($date2));
        $customers = Customers::orderBy('id', 'ASC')->get();
        $inventoryfirstdate = InventoryHistory::orderBy('date', 'ASC')->first();
        $today = date('Y-m-d', strtotime('1 days'));
        $html .= '<center><div class="table-responsive">
                <table class="table product-table table-bordered" style="width: 100%">
                <thead>
                <tr>
                <th>Date</th>
                <th>Product Name</th>
                <th>Incoming</th>';
        foreach ($customers as $customer) {
            $products_with_cust = CustomerProduct::where('customer_id', $customer->id)->where('product_id', $productid);
            if ($products_with_cust->exists()) {
                $html .= '<th>' . $customer->customer_name . '</th>';
            }
        }
        $html .= '<th>Total</th>
                </tr>
                </thead><tbody>';
        if (isset($inventoryfirstdate)) {
            $date = $inventoryfirstdate->date;
        }
        if ($duration == NULL) {
            if ($php_date1 != Null && $php_date2 != Null) {
                $period = CarbonPeriod::create($php_date1, $php_date2);
            } else if ($php_date1 != Null) {
                $php_date1 = date('d-m-Y', strtotime($php_date1));
                $period = CarbonPeriod::create($php_date1, $today);
            } else if ($php_date2 != Null) {
                $php_date2 = date('d-m-Y', strtotime($php_date2));
                $period = CarbonPeriod::create($date, $php_date2);
            } else {
                $period = CarbonPeriod::create($date, $today);
            }
        } else {
            if ($duration == 'yesterday') {
                $date = Carbon::now()->subDays(1)->format('d-m-Y');
            } else if ($duration == 'last_week') {
                $date = Carbon::now()->subDays(7)->format('d-m-Y');
            } else if ($duration == 'last_month') {
                $date = Carbon::now()->subDays(30)->format('d-m-Y');
            } else if ($duration == 'last_six_months') {
                $date = Carbon::now()->subDays(180)->format('d-m-Y');
            } else if ($duration == 'last_year') {
                $date = Carbon::now()->subDays(360)->format('d-m-Y');
            }
            $period = CarbonPeriod::create($date, $today);
        }
        $product_invent = Products::where('id', $productid)->get();
        foreach ($product_invent as $product) {
            foreach ($period as $p) {
                $dt = $p->toDateString();
                $inventoryData = InventoryHistory::select('qty')->orderBy('date', 'ASC')->where('product_id', $productid)->whereDate('date', $dt)->get();
                // $getinventoryData = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $productid)->whereDate('date', $dt)->first();
                $html .= '
                <tr>
                    <td>
                        ' . $p->format('Y-m-d') . '
                    </td>
                    <td>';
                if (isset($product)) {
                    $html .= $product->name;
                }
                $html .= '</td>';
                $incoming_qty = 0;
                foreach ($inventoryData as $inventdata) {
                    if (isset($inventdata)) {
                        $incoming_qty += $inventdata->qty;
                    }
                }
                $html .= '<td> ' . number_format($incoming_qty) . '
                    </td>';
                $totalDeduction = 0;
                foreach ($customers as $customer) {
                    $products_with_cust = CustomerProduct::where('customer_id', $customer->id)->where('product_id', $productid);
                    if ($products_with_cust->exists()) {
                        $html .= '<td>';
                        $sum = 0;
                        if (CustomerProduct::where('customer_id', $customer->id)->where('product_id', $productid)->exists()) {
                            $customerhassku = CustomerHasSku::select('customer_id', 'sku_id')->where('customer_id', $customer->id)->get();
                            foreach ($customerhassku as $cskukey => $chassku) {
                                $skuproduct = SkuProducts::select('sku_id')->where('sku_id', $chassku->sku_id)->where('product_id', $productid)->first();
                                if (isset($skuproduct)) {
                                    $customerOrder = OrderDetails::select('sku_id', 'qty')->where('sku_id', $skuproduct->sku_id)->whereDate('created_at', '=', date('Y-m-d', strtotime($dt)))->get();
                                    foreach ($customerOrder as $cust_order) {
                                        $sum += $cust_order->qty;
                                    }
                                }
                            }
                        }
                        $html .= number_format($sum);
                        $html .= '</td>';
                        $totalDeduction += $sum;
                    }
                    $total_remaining = $incoming_qty - $totalDeduction;
                }
                $html .= '<td>';
                $grandtotal += $total_remaining;
                $html .= number_format($grandtotal);
                $html .= '</td>';
                $totalvalues = $incoming_qty + $totalDeduction;
                $html .= '<input type="hidden" class="all_values" value="' . $totalvalues . '">
                </tr>';
            }
        }
        $html .= '</tbody></table></div></center>';
        return response()->json($html);
    }
    public function categoryProductsReport(Request $request)
    {
        $inventoryfirstdate = InventoryHistory::orderBy('date', 'ASC')->first();
        $today = date('Y-m-d', strtotime('1 days'));
        $date = '2020-01-01';
        if (isset($inventoryfirstdate)) {
            $date = $inventoryfirstdate->date;
        }
        $period = CarbonPeriod::create($date, $today);
        $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
        $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
        $products = Products::get();
        $orders = Orders::where('status', '!=', 4)->get();
        $customers = Customers::orderBy('id', 'ASC')->get();
        $productId = $request->product_id;
        $product = Products::where('id', $request->product_id)->first();
        $categories = Category::get();
        $selected = $request->category_id;
        return view('admin.reports.category_products_report', compact('product_inventory', 'products', 'customers', 'orders', 'period', 'productId', 'product', 'categories', 'selected'));
    }
    public function categoryProductsReportSubmit(Request $request)
    {
        $duration = $request->time_duration;
        $date_range = $request->daterange;
        $date_range = explode(' - ', $date_range);
        $date1 = $date_range[0];
        $date2 = $date_range[1];
        $php_date1 = date('d-m-Y', strtotime($date1));
        $php_date2 = date('d-m-Y', strtotime($date2));
        $inventoryfirstdate = InventoryHistory::orderBy('date', 'ASC')->first();
        $today = date('Y-m-d', strtotime('1 days'));
        if (isset($inventoryfirstdate)) {
            $date = $inventoryfirstdate->date;
        }
        if ($duration == NULL) {
            if ($php_date1 != Null && $php_date2 != Null) {
                $period = CarbonPeriod::create($php_date1, $php_date2);
            } else if ($php_date1 != Null) {
                $php_date1 = date('d-m-Y', strtotime($php_date1));
                $period = CarbonPeriod::create($php_date1, $today);
            } else if ($php_date2 != Null) {
                $php_date2 = date('d-m-Y', strtotime($php_date2));
                $period = CarbonPeriod::create($date, $php_date2);
            } else {
                $period = CarbonPeriod::create($date, $today);
            }
        } else {
            if ($duration == 'yesterday') {
                $date = Carbon::now()->subDays(1)->format('d-m-Y');
            } else if ($duration == 'last_week') {
                $date = Carbon::now()->subDays(7)->format('d-m-Y');
            } else if ($duration == 'last_month') {
                $date = Carbon::now()->subDays(30)->format('d-m-Y');
            } else if ($duration == 'last_six_months') {
                $date = Carbon::now()->subDays(180)->format('d-m-Y');
            } else if ($duration == 'last_year') {
                $date = Carbon::now()->subDays(360)->format('d-m-Y');
            }
            $period = CarbonPeriod::create($date, $today);
        }
        if ($request->category_id == 'all') {
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->get();
            $products = Products::get();
            $orders = Orders::where('status', '!=', 4)->get();
            $customers = Customers::orderBy('id', 'ASC')->get();
            $productId = $request->category_id;
            $product = Products::where('id', $request->product_id)->first();
            $selected = $request->category_id;
            $categories = Category::get();
            $products_data = Products::where('category_id', $request->category_id)->get();
            return view('admin.reports.category_products_report', compact('product_inventory', 'products', 'customers', 'orders', 'period', 'productId', 'product', 'date1', 'date2', 'selected', 'products_data', 'categories', 'duration'));
        } else {
            $product_inventory = InventoryHistory::orderBy('date', 'ASC')->where('product_id', $request->product_id)->get();
            $products = Products::get();
            $orders = Orders::where('status', '!=', 4)->get();
            $customers = Customers::orderBy('id', 'ASC')->get();
            $productId = $request->product_id;
            $selected = $request->category_id;
            $product = Products::where('id', $request->product_id)->first();
            $products_data = Products::where('category_id', $request->category_id)->get();
            $categories = Category::get();
            return view('admin.reports.category_products_report', compact('product_inventory', 'products', 'customers', 'orders', 'period', 'productId', 'product', 'date1', 'date2', 'selected', 'products_data', 'categories', 'duration'));
        }
    }
    public function showSingleCategoryProductReport(Request $request)
    {
        if ($request->ajax()) {
            if ($request->category_id == 'all') {
                $qry = Products::with('customer_Product.customer', 'inventory', 'sku_products.sku.order_details');
            } else {
                $qry = Products::with('customer_Product.customer', 'inventory', 'sku_products.sku.order_details')->where('category_id', $request->category_id);
            }
            $data = $qry->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('pqty', function ($row) {
                    $qty = 0;
                    if (isset($row->inventory)) {
                        $qty = $row->inventory->qty;
                    }
                    return ($qty);
                })
                ->addColumn('price', function ($row) {
                    $total_sale = 0;
                    $orderdetails = OrderDetails::get();
                    foreach ($orderdetails as $orderdetail) {
                        if (isset($orderdetail)) {
                            $orderdetail->load('skuproduct');
                            foreach ($orderdetail->skuproduct as $skuprod) {
                                if ($skuprod->product_id == $row->id) {
                                    $total_sale += $skuprod->selling_cost * $orderdetail->qty;
                                }
                            }
                        }
                    }
                    return '$' . number_format($total_sale, 2);
                })
                ->make(true);
        }
    }
    public function getForecastLabel(Request $request)
    {
        if (Auth::user()->can('labels_view')) {
            $query = DB::table('customer_has_products')
                ->join('customers', 'customer_has_products.customer_id', '=', 'customers.id')
                ->join('labels', 'customer_has_products.brand_id', '=', 'labels.id')
                ->select('customer_has_products.*', 'customer_has_products.label_qty as labelQty', 'customers.customer_name', 'customers.id as cust_id')
                ->where('customer_has_products.is_active', 0)->where('customers.deleted_at', null)->where('customer_has_products.deleted_at', null);
            $newquery = '';
            if ($request->customer_id != 'all') {
                $newquery = $query->where('customer_has_products.customer_id', $request->customer_id);
                $query = $newquery;
            } else {
                $query = $query;
            }
            if ($request->brand_id != 'all') {
                $newquery = $newquery->where('customer_has_products.brand_id', $request->brand_id);
                $query = $newquery;
            } else {
                $query = $query;
            }
            $data = $query->get();
        } else {
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
                $customerId = $customerUser->customer_id;
            }
            $query = DB::table('customers')
                ->join('customer_has_products', 'customers.id', '=', 'customer_has_products.customer_id')
                ->where('customers.id', $customerId)
                ->select('customer_has_products.*', 'customers.customer_name', 'customers.id as cust_id');
            if ($request->min_date != NULL) {
                $max = Carbon::now();
                if ($request->max_date != NULL) {
                    $max = $request->max_date;
                }
                $data = $query->whereBetween('customer_has_products.created_at', [Carbon::parse($request->min_date)->format('Y-md'), Carbon::parse($max)->format('Y-m-d')]);
            }
            $data = $query->where('customer_has_products.is_active', '=', '0')->where('customer_has_products.deleted_at', null)->get();
        }
        foreach ($data as $key => $labelsData) {
            $forecastData = $this->forecastLabels($labelsData->customer_id, $labelsData->brand_id, $labelsData->product_id, $request->days_filter);
            $htmlData = $forecastData['html'];
            if (!empty($request->days_filter)) {
                if ($request->days_filter == 'under') {
                    if ($forecastData['forecast_days'] < 365 && $forecastData['forecast_days'] > 0 && $forecastData['lastdayssale'] > 0) {
                        $data[$key]->forecastdays = $htmlData;
                    } else {
                        unset($data[$key]);
                    }
                }
                if ($request->days_filter == 'over') {
                    if ($forecastData['forecast_days'] > 365 && $forecastData['lastdayssale'] > 0) {
                        $data[$key]->forecastdays = $htmlData;
                    } else {
                        unset($data[$key]);
                    }
                }
                if ($request->days_filter == 'no_sale') {
                    if ($forecastData['lastdayssale'] <= 0) {
                        $data[$key]->forecastdays = $htmlData;
                    } else {
                        unset($data[$key]);
                    }
                }
            } else {
                $data[$key]->forecastdays = $htmlData;
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('cust_name', function ($row) {
                return ucwords($row->customer_name);
            })
            ->addColumn('brand_name', function ($row) {
                $brand = Labels::withTrashed()->where('id', $row->brand_id)->first();
                if (isset($brand)) {
                    return ucwords($brand->brand);
                }
            })
            ->addColumn('prod_name', function ($row) {
                $product = Products::where('id', $row->product_id)->first();
                if (isset($product)) {
                    return ucwords($product->name);
                }
            })
            ->addColumn('cust_has_label_qty', function ($row) {
                $label_Qty = $row->label_qty;
                $labelQty = $this->getLabelQty($row->customer_id, $row->brand_id, $row->product_id);
                if ($labelQty != null) {
                    return $labelQty;
                } else {
                    return $label_Qty;
                }
            })
            ->addColumn('forecast_days', function ($row) {
                return $row->forecastdays;
            })
            ->addColumn('action', function ($row) {
                $btn = '<input type="hidden" class="productId" value="' . $row->product_id . '" name="prodId[]">
                <input type="hidden" class="brandId" value="' . $row->brand_id . '">
                <div class="dropdown">
                  <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="' . $row->customer_id . '" data-product-id="" data-bs-toggle="dropdown">
                    . . .
                  </button>
                  <div class="dropdown-menu">';
                $btn .= '<a class="dropdown-item add-labels" href="#" data-brand-id="' . $row->brand_id . '" data-bs-target="#labelsModal" data-customer_id="" data-product-id=""';
                if ($row->is_active != 0) {
                    $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
                } else {
                    $btn .= 'data-bs-toggle="modal"';
                }
                $btn .= '>
                <span>Add Labels</span>
                </a>
                <a class="dropdown-item reduce-labels" href="#" data-brand-id="' . $row->brand_id . '" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id=""';
                if ($row->is_active != 0) {
                    $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
                } else {
                    $btn .= 'data-bs-toggle="modal"';
                }
                $btn .= '>
                    <span>Reduce Labels</span>
                </a>';
                $btn .= '</div></div>';
                return $btn;
            })
            ->rawColumns(['cust_has_label_qty', 'forecast_days', 'action'])
            ->make(true);
    }
    public function labelsReport(Request $request)
    {
        if ($request->ajax()) {
            return $this->getForecastLabel($request);
        }
        $customers = Customers::where('deleted_at', NULL)->get();
        $labels = Labels::where('deleted_at', NULL)->get();
        return view('admin.reports.labels_report', compact('labels', 'customers'));
    }
    public function getSameProductsWithDiffBrand(Request $request)
    {
        if ($request->ajax()) {
            $customerHasProducts = CustomerHasProduct::with('products:id,name,price', 'brands:id,brand', 'getcustomers:id,customer_name')->orderBy('product_id', 'ASC')->get();
            // dd($customerHasProducts->toArray());
            return Datatables::of($customerHasProducts)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    if (isset($row->products)) {
                        return ucwords($row->products->name);
                    } else {
                        return 'Not exists';
                    }
                })
                ->addColumn('brand_name', function ($row) {
                    if (isset($row->brands)) {
                        return ucwords($row->brands->brand);
                    } else {
                        return 'Not exists';
                    }
                })
                ->addColumn('customer_name', function ($row) {
                    if (isset($row->getcustomers)) {
                        return ucwords($row->getcustomers->customer_name);
                    } else {
                        return ucwords('Not exists');
                    }
                })
                ->addColumn('label_qty', function ($row) {
                    return ucwords($row->label_qty);
                })
                ->make(true);
        }
        return view('admin.reports.same_products');
    }
    public function showAllProfit()
    {
        $products = Products::query();
        $products = $products->with('customer_Product.customer.brands', 'category', 'sku')->get();
        $data = array();
        $keys = array('category_name', 'product_name', 'unit_price', 'total_products_purchasing_cost', 'total_products_selling_cost', 'totalOrders', 'profit', 'customers');
        foreach ($products as $key => $product) {
            $orderCount = 0;
            $productPrice = 0;
            $sellingPrice = 0;
            foreach ($product->sku as $sku_key => $skuProduct) {
                if ($skuProduct->product_id == $product->id) {
                    $orderDetails = OrderDetails::where('sku_id', $skuProduct->sku_id)->get();
                    foreach ($orderDetails as $o_key => $order) {
                        if ($order->qty > 0) {
                            $skuProd = $order->load('skuproduct');
                            foreach ($skuProd->skuproduct as $skuProdkey => $skProd) {
                                if (isset($skProd)) {
                                    if ($skProd->product_id == $product->id) {
                                        $orderCount = $orderCount + $order->qty;
                                        $sellingPrice = $sellingPrice + ($skProd->selling_cost * $order->qty);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $purchasingCost = $product->price * $orderCount;
            $sellingCost = $sellingPrice;
            $profit = $sellingCost - $purchasingCost;
            $customers = $product->customer_Product;
            $custArr = array();
            $keys2 = array('customer_id', 'product_id', 'customer_name', 'total_counts', 'selling_price', 'profit', 'customer_brands');
            $purchasing_cost = 0;
            $selling_cost = 0;
            foreach ($customers as $custkey => $value) {
                if (isset($value->customer)) {
                    $totalCounts = 0;
                    $customerOrders = Orders::with('Details')->where('customer_id', $value->customer->id)->where('status', '!=', 4)->get();
                    $customerProductSellingPrice = 0;
                    foreach ($customerOrders as $cOrderKey => $cOrder) {
                        if (isset($cOrder)) {
                            foreach ($cOrder->Details as $cOrderDetailsKey => $cOrderDetail) {
                                if (isset($cOrderDetail)) {
                                    $skuProducts = $cOrderDetail->load('skuproduct');
                                    foreach ($skuProducts->skuproduct as $skuproductkey => $skuproduct) {
                                        if (isset($skuproduct)) {
                                            if ($skuproduct->product_id == $product->id) {
                                                $totalCounts = $totalCounts + $cOrderDetail->qty;
                                                $customerProductSellingPrice = $customerProductSellingPrice + ($skuproduct->selling_cost * $cOrderDetail->qty);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($totalCounts > 0) {
                        array_push($custArr, array_combine($keys2, [$value->customer->id, $product->id, $value->customer->customer_name, number_format($totalCounts), number_format($customerProductSellingPrice, 2), number_format(($customerProductSellingPrice) - ($product->price * $totalCounts), 2), $value->customer->brands]));
                    }
                }
            }
            if ($orderCount > 0) {
                array_push($data, array_combine($keys, [$product->category->name, $product->name, $product->price, $purchasingCost, $sellingCost, $orderCount, $profit, $custArr]));
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('btn', function ($row) {
                return '<img src="/images/details_open.png" class="dt-control">';
            })
            ->addColumn('category_name', function ($row) {
                return ucwords($row['category_name']);
            })
            ->addColumn('product_name', function ($row) {
                return ucwords($row['product_name']);
            })
            ->addColumn('unit_price', function ($row) {
                return '$' . number_format($row['unit_price'], 2);
            })
            ->addColumn('total_products_purchasing_cost', function ($row) {
                return '$' . number_format($row['total_products_purchasing_cost'], 2);
            })
            ->addColumn('total_products_selling_cost', function ($row) {
                return '$' . number_format($row['total_products_selling_cost'], 2);
            })
            ->addColumn('totalOrders', function ($row) {
                return number_format($row['totalOrders']);
            })
            ->addColumn('profit', function ($row) {
                if ($row['profit'] > 0) {
                    return '<span class="badge rounded-pill m-1" style="background-color: green; color: white; padding: 5px">' . '$' . number_format($row['profit'], 2) . '</span>';
                } else {
                    return '<span class="badge rounded-pill m-1" style="background-color: red; color: white; padding: 5px">' . '$' . number_format($row['profit'], 2) . '</span>';
                }
            })
            ->rawColumns(['btn', 'profit'])
            ->make(true);
        // return response()->json(['success' => true, 'data' => $data]);
    }
    public function productReturnWeeklyReport(Request $request)
    {
        if ($request->ajax()) {
            $orders = DB::table('order_return_details')
                ->join('products', 'order_return_details.product_id', '=', 'products.id')
                ->groupBy('order_return_details.product_id')
                ->whereBetween('order_return_details.created_at', [date('Y-m-d', strtotime('last monday')), Carbon::now()->addDay()->format('Y-m-d')])
                ->select('order_return_details.created_at as o_Date', DB::raw('SUM(order_return_details.qty) as returned_qty'), 'products.name as product_name')->get();
            return Datatables::of($orders)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return (Carbon::parse($row->o_Date)->format('Y-m-d'));
                })
                ->addColumn('product_name', function ($row) {
                    return ($row->product_name);
                })
                ->addColumn('returned_qty', function ($row) {
                    return number_format($row->returned_qty);
                })
                ->make(true);
        }
    }
    public function getCustomerBrandProfitDetails(Request $request)
    {
        $brands = Labels::with('sku')->where('customer_id', $request->customer_id)->get();
        $data = array();
        $keys = array('brand_id', 'brand_name', 'brand_sales_count', 'brand_sales_price', 'brand_profit');
        $brandProductPrice = 0;
        foreach ($brands as $brand_key => $brand) {
            if (isset($brand)) {
                $brand = $brand->load('sku.sku_product');
                $sellingPrice = 0;
                $totalCounts = 0;
                foreach ($brand->sku as $sku_key => $sku) {
                    $orderDetails = OrderDetails::where('sku_id', $sku->id)->get();
                    foreach ($orderDetails as $o_key => $order) {
                        if ($order->qty > 0) {
                            $skuProd = $order->load('skuproduct');
                            foreach ($skuProd->skuproduct as $skuProdkey => $skProd) {
                                if (isset($skProd)) {
                                    if ($skProd->product_id == $request->product_id) {
                                        $totalCounts = $totalCounts + $order->qty;
                                        $sellingPrice = $sellingPrice + ($skProd->selling_cost * $order->qty);
                                    }
                                }
                            }
                        }
                    }
                }
                $product = Products::where('id', $request->product_id)->first();
                $productPurchasingPrice = 0;
                if (isset($product)) {
                    $productPurchasingPrice = $product->price;
                }
                if ($totalCounts > 0) {
                    array_push($data, array_combine($keys, [$brand->id, $brand->brand, number_format($totalCounts), number_format($sellingPrice, 2), number_format(($sellingPrice) - ($productPurchasingPrice * $totalCounts), 2)]));
                }
            }
        }
        return response()->json(['success' => true, 'data' => $data]);
    }
    public function allProductsReport(Request $request)
    {
        if ($request->ajax()) {
            try {
                $job = ProductReport::dispatch($request->all());
            } catch (\Exception $e) {
                dd($e);
            }
        } else {
            $categories = Category::get();
            return view('admin.reports.all_products_report', compact('categories'));
        }
    }
    public function showReportAndMail(Request $request)
    {
        if ($request->ajax()) {
            try {
                ini_set('memory_limit', '-1');
                if ($request->time_duration == 'last_six_months' || $request->time_duration == 'this_year' || $request->time_duration == 'last_year') {
                    ProductReport::dispatch($request->all());
                    // $send = new SendEmail($request->all());
                    // $sendData = $send->handle();
                    return response()->json(['status' => true]);
                } else {
                    $data = $this->generateReport($request->all());
                    return response()->json(['status' => true, 'data' => $data['data'], 'products' => $data['products'], 'day_avg' => $data['day_avg'], 'month_avg' => $data['month_avg']]);
                }
            } catch (\Exception $e) {
                dd($e);
                return response()->json(['status' => false]);
            }
        }
    }
    public function getDateWiseProducts($query, $daysCount, $selectedDays, $from, $to)
    {
        $products = Products::with('sku_products.order_details')->orderBy('id', 'ASC')->get();
        $dateWiseData = array();
        $dayWiseData = array();
        $keys = array('date', 'products');
        foreach ($query as $date => $date_Orders) { // One date with multiple orders against that date
            $productQtty = array();
            $pKeys = array('qty');
            foreach ($products as $key => $product) {
                if (isset($product)) {
                    if (count($date_Orders) > 0) { // if this date has orders
                        $data = array();
                        $qty = 0;
                        foreach ($date_Orders as $index1 => $dateOrder) { // orders products
                            if (isset($dateOrder)) {
                                $skuProducts = $dateOrder->skuproduct;
                                if (count($skuProducts) > 0) { // check if this order has skus with products
                                    foreach ($skuProducts as $index2 => $skuProduct) {
                                        if (isset($skuProduct)) {
                                            if ($product->id == $skuProduct->product_id) {
                                                $qty += $dateOrder->qty;
                                                array_push($data, $dateOrder->qty);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    array_push($productQtty, array_combine($pKeys, [$qty]));
                }
            }
            array_push($dateWiseData, array_combine($keys, [$date, $productQtty]));
        }
        $averages = array();
        $pKeys = array('dayAvg', 'monthAvg');
        if ($selectedDays == '') {
            foreach ($products as $index0 => $product) {
                $qty = 0;
                if (isset($product)) {
                    foreach ($query as $date => $date_Orders) { // One date with multiple orders against that date
                        if (count($date_Orders) > 0) { // if this date has orders
                            foreach ($date_Orders as $index1 => $dateOrder) { // orders products
                                if (isset($dateOrder)) {
                                    $skuProducts = $dateOrder->skuproduct;
                                    if (count($skuProducts) > 0) { // check if this order has skus with products
                                        foreach ($skuProducts as $index2 => $skuProduct) {
                                            if (isset($skuProduct)) {
                                                if ($product->id == $skuProduct->product_id) {
                                                    $qty += $dateOrder->qty;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($daysCount == 0) {
                        $daysCount = 1;
                    }
                    $daysAvg = $qty / $daysCount;
                    $monthAvg = ($qty / $daysCount) * 30;
                    array_push($averages, array_combine($pKeys, [$daysAvg, $monthAvg]));
                }
            }
        } else {
            // if (count($dateWiseData) > 0) {
            $lastRecord = end($dateWiseData);
            $lastDate = date_create($lastRecord['date']);
            $subFromLastDate = date_create($lastRecord['date']);
            $differenceInDates = date_sub($subFromLastDate, date_interval_create_from_date_string($selectedDays . " days"));
            $filteredDayWiseDate = date_format($differenceInDates, 'Y-m-d');
            $query = OrderDetails::with('skuproduct');
            $query = $query->whereBetween('created_at', [Carbon::parse($filteredDayWiseDate)->subDay(), Carbon::parse($lastDate)->addDay()])->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
            foreach ($products as $index0 => $product) {
                $qty = 0;
                if (isset($product)) {
                    foreach ($query as $date => $date_Orders) { // One date with multiple orders against that date
                        if (count($date_Orders) > 0) { // if this date has orders
                            foreach ($date_Orders as $index1 => $dateOrder) { // orders products
                                if (isset($dateOrder)) {
                                    $skuProducts = $dateOrder->skuproduct;
                                    if (count($skuProducts) > 0) { // check if this order has skus with products
                                        foreach ($skuProducts as $index2 => $skuProduct) {
                                            if (isset($skuProduct)) {
                                                if ($product->id == $skuProduct->product_id) {
                                                    $qty += $dateOrder->qty;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($daysCount == 0) {
                        $daysCount = 1;
                    }
                    $daysAvg = $qty / $selectedDays;
                    $monthAvg = ($qty / $selectedDays) * 30;
                    array_push($averages, array_combine($pKeys, [$daysAvg, $monthAvg]));
                }
            }
            // }
        }
        return array('products' => $products, 'dateWiseData' => $dateWiseData, 'averages' => $averages);
    }
    public function filterProductReport(Request $request)
    {
        try {
            $timeDuration = $request->time_duration;
            $dateRange = $request->daterange;
            $daysCount = 0;
            $selectedDays = $request->days_count;
            $from = '2021-01-01';
            $to = '2022-01-01';
            $query = OrderDetails::with('skuproduct');
            if ($timeDuration != null) {
                if ($timeDuration == 'yesterday') {
                    $dateRange = Carbon::now()->subDays(1);
                    $from = $dateRange->format('Y-m-d');
                    $to = $this->todayDate;
                    $query = $query->whereBetween('created_at', [$from, $to])->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $daysCount = 1;
                } else if ($timeDuration == 'last_week') {
                    //
                    $previous_week = strtotime("-1 week +1 day");
                    $start_week = strtotime("last sunday midnight", $previous_week);
                    $end_week = strtotime("next saturday", $start_week);
                    $from = date("Y-m-d", $start_week);
                    $to = date("Y-m-d", $end_week);
                    $query = $query->whereBetween('created_at', [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $daysCount = 7;
                } else if ($timeDuration == 'last_month') {
                    $dateRange = Carbon::now()->subDays(30);
                    $from = $dateRange->format('Y-m-d');
                    $to = $this->todayDate;
                    $query = $query->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $daysCount = 30;
                } else if ($timeDuration == 'last_six_months') {
                    $dateRange = Carbon::now()->subDays(180);
                    $from = $dateRange->format('Y-m-d');
                    $to = $this->todayDate;
                    $query = $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()])->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $daysCount = 180;
                } else if ($timeDuration == 'last_year') {
                    $dateRange = Carbon::now()->subDays(360);
                    $from = $dateRange->format('Y-m-d');
                    $to = $this->todayDate;
                    $query = $query->whereYear('created_at', date('Y', strtotime('-1 year')))->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $daysCount = 360;
                }
                $dateRange = null;
            } else if ($dateRange != null) {
                $dateRange = explode(' - ', $dateRange);
                if (isset($dateRange[1])) {
                    $from = date('Y-m-d', strtotime($dateRange[0]));
                    $to = date('Y-m-d', strtotime($dateRange[1]));
                }
                if ($from != '2022-01-01' && $to != '2022-01-01') {
                    $query = $query->whereBetween('created_at', [$from, $to])->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $fromDate = Carbon::createFromFormat('Y-m-d', $from);
                    $toDate = Carbon::createFromFormat('Y-m-d', $to);
                    $diffInDays = $fromDate->diffInDays($toDate);
                    $totalDifferenceDays = $diffInDays;
                    if ($totalDifferenceDays == 0) {
                        $totalDifferenceDays = 1;
                    }
                    $daysCount = $totalDifferenceDays;
                    $timeDuration = null;
                } else {
                    $query = OrderDetails::with('skuproduct')->orderBy('created_at')->get()->groupBy(function ($item) {
                        return $item->created_at->format('Y-m-d');
                    });
                    $checkFromFirstEntry = OrderDetails::orderBy('created_at', 'ASC')->first();
                    $firstDate = '';
                    if (isset($checkFromFirstEntry)) {
                        $firstDate = $checkFromFirstEntry->created_at->format('Y-m-d');
                    }
                    if ($firstDate != '') {
                        $from_Date = $firstDate;
                        $to_Date = $this->todayDate;
                        $fromDate = Carbon::createFromFormat('Y-m-d', $from_Date);
                        $toDate = Carbon::createFromFormat('Y-m-d', $to_Date);
                        $difference = $fromDate->diffInDays($toDate);
                        $totalDifferenceDays = $difference;
                        if ($totalDifferenceDays == 0) {
                            $totalDifferenceDays = 1;
                        }
                        $daysCount = $totalDifferenceDays;
                    } else {
                        $daysCount = 1;
                    }
                    $from = '2022/01/01';
                    $to = '2022/01/01';
                    $selectedDays = $request->days_count;
                }
            } else {
                $query = OrderDetails::with('skuproduct')->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
            }
            $data = $this->getDateWiseProducts($query, $daysCount, $selectedDays, $from, $to);
            $products = $data['products'];
            $dateWiseData = $data['dateWiseData'];
            $averages = $data['averages'];
            $time_duration = $timeDuration;
            $from = $from;
            $to = $to;
            if ($dateRange == null) {
                $from = null;
                $to = null;
            }
            $selectedDaysCount = $request->days_count;
            return view('admin.reports.filtered_reports', compact('products', 'dateWiseData', 'averages', 'time_duration', 'from', 'to', 'selectedDaysCount'));
        } catch (\Exception $e) {
            return view('admin.server_error');
            return redirect()->back()->withError('No Record Found');
        }
    }
    public function customerPostageReport($request)
    {
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
                $start_week = strtotime("last monday midnight", $previousWeek);
                $end_week = strtotime("next sunday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $from = $start_week;
                $to = $end_week;
            } else if ($request['time_duration'] == 'this_month') {
                $from = Carbon::now()->startOfMonth()->format('Y-m-d');
                $to = Carbon::now()->format('Y-m-d');
            } else if ($request['time_duration'] == 'last_month') {
                $previousMonth = strtotime('-1 month +1 day');
                $start_month = strtotime("first day of this month", $previousMonth);
                $end_month = strtotime("last day of this month", $start_month);
                $start_month = date("Y-m-d", $start_month);
                $end_month = date("Y-m-d", $end_month);
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
        $carbonPeriod = CarbonPeriod::create($from, $to);
        $customers = Customers::query();
        if (!empty($request->customer)) {
            $customers = $customers->where('id', $request->customer);
        }
        $customers = $customers->get();
        $arr = [];
        $keys = ['date', 'customers', 'total'];
        foreach ($carbonPeriod as $key => $date) {
            $totalOrders = [];
            $totalOrdersKeys = ['name', 'qty'];
            $footTotal = [];
            foreach ($customers as $ckey => $customer) {
                $orders = DB::table('orders');
                if (!empty($request['customer'])) {
                    $orders = $orders->where('customer_id', $request['customer']);
                }
                $orders = $orders->where('customer_id', $customer->id)->whereDate('orders.created_at', $date->format('Y-m-d'))->get();
                $sale = 0;
                foreach ($orders as $okey => $order) {
                    $sale += $order->postageqty;
                }
                $customers[$ckey]['postage_total'] += $sale;
                array_push($totalOrders, array_combine($totalOrdersKeys, [$customer->customer_name, $sale]));
                array_push($footTotal, $customers[$ckey]['postage_total']);
            }
            array_push($arr, array_combine($keys, [$date->format('m/d/Y'), $totalOrders, $footTotal]));
        }
        $data = ['data' => $arr, 'customers' => $customers];
        return $data;
    }
    public function postageReporting(Request $request)
    {
        // $query = SkuOrder::orderBy('created_at')->where('status', '!=', 4)->get()->groupBy(function ($item) {
        //     return $item->created_at->format('Y-m-d');
        // });
        // $customers = Customers::query();
        // if (!empty($request->customer)) {
        //     $customers = $customers->where('id', $request->customer);
        // }
        // $customers = $customers->get();
        // $dateWisePostage = array();
        // $dateWisePostageKeys = array('date', 'customers');
        // foreach ($query as $date => $date_Orders) {
        //     $postageQtty = array();
        //     $postageKeys = array('qty');
        //     foreach ($customers as $cKey => $customer) {
        //         if (isset($customer)) {
        //             if (count($date_Orders) > 0) {
        //                 $qty = 0;
        //                 foreach ($date_Orders as $dkey => $dateOrder) {
        //                     if (isset($dateOrder)) {
        //                         if ($dateOrder->customer_id == $customer->id) {
        //                             $qty += $dateOrder->postageqty;
        //                         }
        //                     }
        //                 }
        //             }
        //             array_push($postageQtty, array_combine($postageKeys, [$qty]));
        //         }
        //     }
        //     array_push($dateWisePostage, array_combine($dateWisePostageKeys, [$date, $postageQtty]));
        // }
        // $customerPostageTotal = array();
        // $customerPostageTotalKeys = array('qty');
        // foreach ($customers as $custkey => $customer) {
        //     $qty = 0;
        //     if (isset($customer)) {
        //         foreach ($query as $date => $date_Orders) {
        //             $postageQtty = array();
        //             $postageKeys = array('qty');
        //             if (count($date_Orders) > 0) {
        //                 foreach ($date_Orders as $dkey => $dateOrder) {
        //                     if (isset($dateOrder)) {
        //                         if ($dateOrder->customer_id == $customer->id) {
        //                             $qty += $dateOrder->postageqty;
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     array_push($customerPostageTotal, array_combine($customerPostageTotalKeys, [$qty]));
        // }
        if ($request->ajax()) {
            $data = $this->customerPostageReport($request);
            return response()->json(['status' => true, 'data' => $data['data'], 'customers' => $data['customers']]);
        }
        $customers = Customers::get();
        return view('admin.reports.postage_reporting', compact('customers'));
    }
    public function filterPostageReporting(Request $request)
    {
        $timeDuration = $request->time_duration;
        $dateRange = $request->daterange;
        $daysCount = 0;
        $selectedDays = $request->days_count;
        $from = '';
        $to = '';
        $query = Orders::with('Customer')->where('status', '!=', 4);
        if ($timeDuration != null) {
            if ($timeDuration == 'all') {
                $query = $query->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 1;
            } else if ($timeDuration == 'yesterday') {
                $dateRange = Carbon::now()->subDays(1);
                $from = $dateRange->format('Y-m-d');
                $to = $this->todayDate;
                $query = $query->whereBetween('created_at', [$from, $to])->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 1;
            } else if ($timeDuration == 'last_week') {
                //
                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $from = date("Y-m-d", $start_week);
                $to = date("Y-m-d", $end_week);
                $query = $query->whereBetween('created_at', [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 7;
            } else if ($timeDuration == 'last_month') {
                $dateRange = Carbon::now()->subDays(30);
                $from = $dateRange->format('Y-m-d');
                $to = $this->todayDate;
                $query = $query->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 30;
            } else if ($timeDuration == 'last_six_months') {
                $dateRange = Carbon::now()->subDays(180);
                $from = $dateRange->format('Y-m-d');
                $to = $this->todayDate;
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()])->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 180;
            } else if ($timeDuration == 'last_year') {
                $dateRange = Carbon::now()->subDays(360);
                $from = $dateRange->format('Y-m-d');
                $to = $this->todayDate;
                $query = $query->whereYear('created_at', date('Y', strtotime('-1 year')))->orderBy('created_at')->get()->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });
                $daysCount = 360;
            }
            $dateRange = null;
        } else if ($dateRange != null) {
            $dateRange = explode(' - ', $dateRange);
            $from = date('Y-m-d', strtotime($dateRange[0]));
            $to = date('Y-m-d', strtotime($dateRange[1]));
            $query = $query->whereBetween('created_at', [$from, $to])->orderBy('created_at')->get()->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
            $fromDate = Carbon::createFromFormat('Y-m-d', $from);
            $toDate = Carbon::createFromFormat('Y-m-d', $to);
            $diffInDays = $fromDate->diffInDays($toDate);
            $totalDifferenceDays = $diffInDays;
            if ($totalDifferenceDays == 0) {
                $totalDifferenceDays = 1;
            }
            $daysCount = $totalDifferenceDays;
            $timeDuration = null;
        }
        $customers = Customers::get();
        $dateWisePostage = array();
        $dateWisePostageKeys = array('date', 'customers');
        foreach ($query as $date => $date_Orders) {
            $postageQtty = array();
            $postageKeys = array('qty');
            foreach ($customers as $cKey => $customer) {
                if (isset($customer)) {
                    if (count($date_Orders) > 0) {
                        $qty = 0;
                        foreach ($date_Orders as $dkey => $dateOrder) {
                            if (isset($dateOrder)) {
                                if ($dateOrder->customer_id == $customer->id) {
                                    $qty += $dateOrder->postageqty;
                                }
                            }
                        }
                    }
                    array_push($postageQtty, array_combine($postageKeys, [$qty]));
                }
            }
            array_push($dateWisePostage, array_combine($dateWisePostageKeys, [$date, $postageQtty]));
        }
        $customerPostageTotal = array();
        $customerPostageTotalKeys = array('qty');
        foreach ($customers as $custkey => $customer) {
            $qty = 0;
            if (isset($customer)) {
                foreach ($query as $date => $date_Orders) {
                    $postageQtty = array();
                    $postageKeys = array('qty');
                    if (count($date_Orders) > 0) {
                        foreach ($date_Orders as $dkey => $dateOrder) {
                            if (isset($dateOrder)) {
                                if ($dateOrder->customer_id == $customer->id) {
                                    $qty += $dateOrder->postageqty;
                                }
                            }
                        }
                    }
                }
            }
            array_push($customerPostageTotal, array_combine($customerPostageTotalKeys, [$qty]));
        }
        return view('admin.reports.filtered_postage_report', compact('dateWisePostage', 'customers', 'customerPostageTotal'));
    }
    public function brandReporting(Request $request)
    {
        if ($request->ajax()) {
            $orderedbrands = Labels::with('customer')->where('deleted_at', NULL);
            $search_arr = $request->get('search');
            if (!empty($request->brand)) {
                $orderedbrands = $orderedbrands->where('labels.id', $request->brand);
            }
            if (!empty($request->customer)) {
                $orderedbrands = $orderedbrands->where('labels.customer_id', $request->customer);
            }
            if (!empty($search_arr['value'])) {
                $keyword = $search_arr['value'];
                $orderedbrands = $orderedbrands->where('brand', 'LIKE', "%$keyword%");
            }
            $orderedbrands = $orderedbrands->get();
            foreach ($orderedbrands as $bkey => $brand) {
                $orders = DB::table('orders')->where('brand_id', $brand->id);
                if (!empty($request->from_date)) {
                    $orders = $orders->whereDate('orders.created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
                    if (!empty($request->to_date)) {
                        $orders = $orders->whereDate('orders.created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
                    }
                }
                $orderedbrands[$bkey]->total_orders = $orders->sum('mailerqty');
            }
            return Datatables::of($orderedbrands)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    if (isset($row->customer)) {
                        return ucwords($row->customer->customer_name);
                    } else {
                        return '';
                    }
                })
                ->addColumn('brand', function ($row) {
                    return ucwords($row->brand);
                })
                ->addColumn('sales', function ($row) {
                    // return number_format($row->selling_cost, 2);
                })
                ->addColumn('total_orders', function ($row) {
                    return ($row->total_orders);
                })
                ->make(true);
        }
        $brands = Labels::where('deleted_at', NULL)->get();
        $customers = Customers::get();
        return view('admin.reports.brands_report', compact('customers', 'brands'));
    }
    public function brandReportingTotalOrders(Request $request)
    {
        if ($request->ajax()) {
            $orderedbrands = Labels::with('customer')->where('deleted_at', NULL);
            $search_arr = $request->get('search');
            if (!empty($request->customer)) {
                $orderedbrands = $orderedbrands->where('labels.customer_id', $request->customer);
            }
            if (!empty($request->brand)) {
                $orderedbrands = $orderedbrands->where('labels.id', $request->brand);
            }
            if (!empty($search_arr['value'])) {
                $keyword = $search_arr['value'];
                $orderedbrands = $orderedbrands->where('brand', 'LIKE', "%$keyword%");
            }
            $orderedbrands = $orderedbrands->get();
            $grandTotalOrders = 0;
            foreach ($orderedbrands as $bkey => $brand) {
                $orders = DB::table('orders')->where('brand_id', $brand->id);
                if (!empty($request->from_date)) {
                    $orders = $orders->whereDate('orders.created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
                    if (!empty($request->to_date)) {
                        $orders = $orders->whereDate('orders.created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
                    }
                }
                $orderedbrands[$bkey]->total_orders = $orders->sum('mailerqty');
                $grandTotalOrders += $orders->sum('mailerqty');
            }
            return response()->json(['status' => true, 'total_orders' => $grandTotalOrders]);
        } 
    }
    public function inventoryHistoryReport(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->pid;
            $data = DB::table("inventory_history")
                ->join('products', 'inventory_history.product_id', '=', 'products.id')
                ->join('category', 'products.category_id', '=', 'category.id')
                ->select(['category.name as category_name', 'products.name', 'inventory_history.*'])
                ->orderBy('inventory_history.created_at', 'DESC')
                ->orderBy('inventory_history.id', 'DESC')
                ->where('inventory_history.product_id', $id);
            if (!empty($request->from)) {
                $inventoryAvailableDate = Carbon::parse('2022-07-25');
                if (Carbon::parse($request->from)->lt($inventoryAvailableDate)) {
                    $request->from = Carbon::parse('2022-07-25');
                }
                $data = $data->whereDate('inventory_history.created_at', '>=', Carbon::parse($request->from)->format('Y-m-d'));
                if (!empty($request->to)) {
                    $data = $data->whereDate('inventory_history.created_at', '<=', Carbon::parse($request->to)->format('Y-m-d'));
                }
            } else {
                $data = $data->whereDate('inventory_history.created_at', '>=', '2022-07-25');
            }
            if (!empty($request->supplier_description_filter)) {
                if ($request->supplier_description_filter == 'manual_addition') {
                    $data = $data->where('manual_add', '>', 0);
                } else if ($request->supplier_description_filter == 'cancelled_orders') {
                    $data = $data->where('cancel_order_add', '>', 0);
                } else if ($request->supplier_description_filter == 'supplier_recvd') {
                    $data = $data->where('supplier_inventory_received', '>', 0);
                } else if ($request->supplier_description_filter == 'description') {
                    $data = $data->where('description', '!=', NULL);
                } else if ($request->supplier_description_filter == 'returned_orders') {
                    $data = $data->where('return_add', '>', 0);
                } else if ($request->supplier_description_filter == 'manual_deduction') {
                    $data = $data->where('manual_reduce', '>', 0);
                } else if ($request->supplier_description_filter == 'sales') {
                    $data = $data->where('sales', '>', 0);
                } else if ($request->supplier_description_filter == 'batch_edited') {
                    $data = $data->where('edit_batch_qty', '>', 0);
                } else if ($request->supplier_description_filter == 'return_edited') {
                    $data = $data->where('return_edited', '>', 0);
                }
            }
            // if (!empty($request->supplier_received)) {
            //     $data = $data->where('supplier_inventory_received', $request->supplier_received);
            // }
            $data = $data->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return ucwords($row->category_name);
                })
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('date', function ($row) {
                    // return '<span title="Created by: '.$row->user_name.'">'.(date("m/d/Y h:i:sa", strtotime($row->created_at))).'</span>';
                    return '<div class="popup">
                ' . (date("m/d/Y h:i:sa", strtotime($row->created_at))) . '
                <span class="popuptext d-none">Created by: ' . $row->user_name . '</span>
              </div>';
                })
                ->addColumn('qty', function ($row) {
                    // return ucwords($row->qty);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->qty . '</span>';
                })
                ->addColumn('manual_add', function ($row) {
                    // return ucwords($row->manual_add);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->manual_add . '</span>';
                })
                ->addColumn('batch_edited', function ($row) {
                    // return ucwords($row->edit_batch_qty);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->edit_batch_qty . '</span>';
                })
                ->addColumn('cancelled_orders', function ($row) {
                    // return ucwords($row->cancel_order_add);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->cancel_order_add . '</span>';
                })
                ->addColumn('supplier_received', function ($row) {
                    // return ucwords($row->supplier_inventory_received);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->supplier_inventory_received . '</span>';
                })
                ->addColumn('description', function ($row) {
                    // return ucwords($row->description);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->description . '</span>';
                })
                ->addColumn('returned', function ($row) {
                    // return ucwords($row->return_add);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->return_add . '</span>';
                })
                ->addColumn('return_edited', function ($row) {
                    // return ucwords($row->return_edited);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->return_edited . '</span>';
                })
                ->addColumn('manual_deduct', function ($row) {
                    // return ucwords($row->manual_reduce);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->manual_reduce . '</span>';
                })
                ->addColumn('sales', function ($row) {
                    // return ucwords($row->sales);
                    return '<span title="Created by: ' . $row->user_name . '">' . $row->sales . '</span>';
                })
                ->addColumn('total_inventory', function ($row) {
                    return ucwords($row->total);
                })
                ->rawColumns(['date', 'qty', 'manual_add', 'batch_edited', 'cancelled_orders', 'supplier_received', 'description', 'returned', 'return_edited', 'manual_deduct', 'sales'])
                ->make(true);
        }
        $categories = Category::get();
        return view('admin.reports.inventory_history_report', compact('categories'));
    }
}
