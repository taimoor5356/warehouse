<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Inventory;
use App\Models\CustomerProduct;
use App\AdminModels\OrderDetails;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\InventoryHistory;
use Illuminate\Support\Facades\Route;

trait Forecast
{
  public function orderDetails($product = null, $forecast_Vals = null)
  {
    $lastDaysOrder = DB::table('order_details')
      // ->join('sku_products', 'order_details.sku_id', '=', 'sku_products.sku_id')
      ->join('sku_order_details', function($join) {
        $join->on('order_details.order_id', '=', 'sku_order_details.order_id')
        ->on('order_details.sku_id', '=', 'sku_order_details.sku_id');
      })
      ->join('orders', 'order_details.order_id', '=', 'orders.id')
      ->where('sku_order_details.product_id', '=', $product->id)
      ->where('orders.status', '!=', 4)
      // ->where('order_details.qty', '>', 0)
      ->whereDate('orders.created_at', '>=', Carbon::now()->subDays($forecast_Vals))
      // ->whereDate('orders.created_at', '<', Carbon::now()->subDay())
      ->select(DB::raw('SUM(order_details.qty) as last_days_order'))->first();
      
    return $lastDaysOrder->last_days_order;
  }
  public function getProductsData($products, $nearToEmpty = '')
  {
    $setting = Setting::first();
    foreach ($products as $prodkey => $product) {
      if (isset($product)) {
        $forecast_Vals = 0;
        $thresholdVal = 0;
        if (isset($setting)) {
          $forecast_Vals = $setting->forecast_days;
          $thresholdVal = $setting->threshold_val;
        }
        $manual_threshold = 0;
        if ($product->automated_status == 0) {
          if ($product->forecast_status == 0) {
            // if ($product->forecast_days != NULL || $product->forecast_days != '' || $product->forecast_days > 0) {
            $forecast_Vals = $product->forecast_days;
            $thresholdVal = $product->threshold_val;
            // }
          } else if ($product->forecast_status == 1) {
            $manual_threshold = $product->manual_threshold; // check if qty is less than manual threshold
          }
        }
        $lastDaysOrder = 0;
        $forecastMsg = '';
        $lastDaysOrder = $this->orderDetails($product, $forecast_Vals);
        if ($lastDaysOrder == null) {
          $lastDaysOrder = 0;
        }
        $productInventory = Inventory::where('product_id', $product->id)->first();
        $inventoryQty = 0;
        if (isset($productInventory)) {
          $inventoryQty = $productInventory->qty;
        }
        // if ($inventoryQty > 0) {
        if ($lastDaysOrder <= 0) {
          $forecast_Days = $forecast_Vals;
          $forecastMsg = 'No Sale for ' . $forecast_Vals . 'd';
        } else if ($lastDaysOrder > 0) {
          $perDaySale = ceil($lastDaysOrder / $forecast_Vals);
          $available = $inventoryQty;
          $res = ceil($available / $perDaySale);
          $forecast_Days = $res;
          if ($res > 365) {
            $forecastMsg = '365d +';
          } else {
            $forecastMsg = $res . 'd';
          }
        }
        // } else {
        //   $forecast_Days = $forecast_Vals;
        //   $forecastMsg = 'Add Inventory '.$forecast_Vals.'d';
        // }
        $products[$prodkey]->invent_qty = (int)($inventoryQty);
        $products[$prodkey]->days_left = ($forecast_Days); // left forecast days
        $products[$prodkey]->forecast_msg = ($forecastMsg); // left forecast days
        $products[$prodkey]->forecast_status = $product->forecast_status; // product forecast status
        $products[$prodkey]->automated_status = $product->automated_status; // product automated status
        $products[$prodkey]->manual_threshold = (int)($manual_threshold); // product manual threshold
        $products[$prodkey]->threshold_val = (int)($thresholdVal); // setting || product threshold value
        $products[$prodkey]->forecast_days = (int)($forecast_Vals); // setting || product forecast days
        // Drop Down History
        if ($nearToEmpty == 'near_to_empty') {
          if ($forecast_Days > $thresholdVal) {
            unset($products[$prodkey]);
          }
          if ($product->forecast_status == 1) {
            if ($inventoryQty >= $manual_threshold) {
              unset($products[$prodkey]);
            }
          }
          if ($lastDaysOrder <= 0) {
            if ($inventoryQty > $thresholdVal) {
              unset($products[$prodkey]);
            }
            if ($inventoryQty <= 0) {
              unset($products[$prodkey]);
            }
          }
        }
      }
    }
    return $products;
  }
  public function forecastData($catId = NULL)
  {
    if ($catId != null) {
      $products  = DB::table("products")
        ->join('category', 'category.id', '=', 'products.category_id')
        ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
        ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
        ->groupBy('product_id')
        ->where('products.category_id', '=', $catId)
        ->where('products.deleted_at', NULL)
        ->where('category.deleted_at', NULL)->get();
    } else {
      $products  = DB::table("products")
        ->join('category', 'category.id', '=', 'products.category_id')
        ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
        ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
        ->groupBy('product_id')
        ->where('products.deleted_at', NULL)
        ->where('category.deleted_at', NULL)->get();
    }
    if (Auth::user()->can('product_view')) {
      $routeName = Route::current()->uri;
      if ($catId != NULL) {
        $products = Products::query()->where('deleted_at', NULL)->where('category_id', $catId);
      } else {
        $products = Products::query()->where('deleted_at', NULL);
      }
      if ($routeName == 'products/admin' || $routeName == 'product_inventory') {
        $products = $products->get();
      } else if ($routeName == 'inventory/admin') {
        $products = $products->where('is_active', '1')->get();
      } else {
        $products = $products->get();
      }
    }
    if (Auth::user()->hasRole('customer')) {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $query = Products::with('sku', 'category', 'inventory', 'product_unit');
      $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
      $routeName = Route::current()->uri;
      if ($routeName == 'products/admin' || $routeName == 'product_inventory') {
        $products = array();
        foreach ($customerProducts as $key => $cProduct) {
          $query = $query->where('id', $cProduct->product_id)->first();
          array_push($products, $query);
        }
      } else if ($routeName == 'inventory/admin') {
        $products = array();
        foreach ($customerProducts as $key => $cProduct) {
          $query = $query->where('is_active', '1')->where('id', $cProduct->product_id)->first();
          array_push($products, $query);
        }
      } else {
        $products = array();
        foreach ($customerProducts as $key => $cProduct) {
          $query = $query->where('id', $cProduct->product_id)->first();
          array_push($products, $query);
        }
      }
    }
    // Forecast days tells how many sales in last forecast days
    // Threshold tells inventory qty left
    // dd($products);
    $productData = $this->getProductsData($products, $nearToEmpty = '');
    return $productData;
  }
  public function categoryProductData($cid, $min = null, $max = null, $duration = null)
  {
    $products  = DB::table("products")
      ->join('category', 'category.id', '=', 'products.category_id')
      ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
      ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
      ->groupBy('product_id')
      ->where('products.deleted_at', NULL)
      ->where('category.deleted_at', NULL);
    if ($cid != null) {
      $products  = DB::table("products")
        ->join('category', 'category.id', '=', 'products.category_id')
        ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
        ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
        ->groupBy('product_id')
        ->where('products.category_id', '=', $cid)
        ->where('products.deleted_at', NULL)
        ->where('category.deleted_at', NULL);
    }
    if (Auth::user()->can('product_view')) {
      if ($cid != null) {
        $products  = DB::table("products")
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('products.category_id', '=', $cid)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      } else {
        $products  = DB::table("products")
          ->join('category', 'category.id', '=', 'products.category_id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      }
    }
    if (Auth::user()->hasRole('customer')) {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      if ($cid != null) {
        $products  = DB::table("products")
          ->join('customer_products', 'products.id', '=', 'customer_products.product_id')
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('customer_products.customer_id', $customerId)
          ->where('products.category_id', '=', $cid)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      } else {
        $products  = DB::table("products")
          ->join('customer_products', 'products.id', '=', 'customer_products.product_id')
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('customer_products.customer_id', $customerId)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      }
    }
    $products = $products->get();
    // Forecast days tells how many sales in last forecast days
    // Threshold tells inventory qty left
    $setting = Setting::first();
    $productData = $this->getProductsData($products, $nearToEmpty = '');
    return $productData;
  }
  public function selectedCategoriesProductsData($cid)
  {
    if (Auth::user()->can('product_view')) {
      if ($cid != null) {
        $products  = DB::table("products")
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->whereIn('products.category_id', $cid)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      } else {
        $products  = DB::table("products")
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      }
    }
    if (Auth::user()->hasRole('customer')) {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      if ($cid != null) {
        $products  = DB::table("products")
          ->join('customer_products', 'products.id', '=', 'customer_products.product_id')
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('customer_products.customer_id', $customerId)
          ->whereIn('products.category_id', $cid)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      } else {
        $products  = DB::table("products")
          ->join('customer_products', 'products.id', '=', 'customer_products.product_id')
          ->join('category', 'category.id', '=', 'products.category_id')
          // ->join('product_units', 'products.product_unit_id', '=', 'product_units.id')
          ->leftJoin('inventory', 'products.id', '=', 'inventory.product_id')
          ->select('products.*', 'category.id as cat_id', 'category.name as category_name', DB::raw('SUM(inventory.qty) as inventory_qty'), 'inventory.id as inventory_id', 'inventory.date as inventory_date')
          ->groupBy('product_id')
          ->where('customer_products.customer_id', $customerId)
          ->where('products.deleted_at', NULL)
          ->where('category.deleted_at', NULL);
      }
    }
    $products = $products->get();
    // Forecast days tells how many sales in last forecast days
    // Threshold tells inventory qty left
    $setting = Setting::first();
    $productData = $this->getProductsData($products);
    return $productData;
  }
  public function nearToEmpty($category = null, $product = null)
  {
    if (Auth::user()->can('product_view')) {
      $routeName = Route::current()->uri;
      $products = Products::with('sku_products', 'category', 'inventory', 'product_unit')->where('deleted_at', NULL);
      if (!empty($category)) {
        $products = $products->where('category_id', $category);
        if (!empty($product)) {
          $products = $products->where('id', $product);
        }
      }
      if ($routeName == 'products/admin' || $routeName == 'product_inventory') {
        $products = $products->get();
      } else if ($routeName == 'inventory/admin') {
        $products = $products->get();
      } else {
        $products = $products->get();
      }
    }
    if (Auth::user()->hasRole('customer')) {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $routeName = Route::current()->uri;
      $getProduct = Products::with('sku', 'category', 'inventory', 'product_unit')->where('deleted_at', NULL);
      if ($routeName == 'products/admin' || $routeName == 'product_inventory') {
        $products = array();
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
        foreach ($customerProducts as $key => $cProduct) {
          $product = $getProduct->where('id', $cProduct->product_id)->first();
          array_push($products, $product);
        }
      } else if ($routeName == 'inventory/admin') {
        $products = array();
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
        foreach ($customerProducts as $key => $cProduct) {
          $product = $getProduct->where('id', $cProduct->product_id)->first();
          array_push($products, $product);
        }
      } else {
        $products = array();
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
        foreach ($customerProducts as $key => $cProduct) {
          $product = $getProduct->where('id', $cProduct->product_id)->first();
          array_push($products, $product);
        }
      }
    }
    // Forecast days tells how many sales in last forecast days
    // Threshold tells inventory qty left
    $productData = $this->getProductsData($products, $nearToEmpty = 'near_to_empty');
    return $productData;
  }
  public function forecastLabels($customer_id, $brand_id, $product_id, $daysFilter = null)
  {
    $html = '';
    $forecast_Days = 0;
    $setting = Setting::where('id', '1')->first();
    $forecast_Val = $setting->forecast_days;
    if ($forecast_Val == NULL || $forecast_Val == '') {
      $forecast_Val = 0;
    }
    $threshold = $setting->threshold_val;
    if ($threshold == NULL || $threshold == '') {
      $threshold = 0;
    }
    $mergedBrand = MergedBrandProduct::where('customer_id', $customer_id)->where('merged_brand', $brand_id)->where('product_id', $product_id)->select(['merged_qty']);
    $mergedSelectedBrand = MergedBrandProduct::where('customer_id', $customer_id)->where('selected_brand', $brand_id)->where('product_id', $product_id)->select(['merged_qty']);
    if ($mergedBrand->exists()) {
      $mergedProduct = $mergedBrand->first();
      if (isset($mergedProduct)) {
        $available = $mergedProduct->merged_qty;
      }
    } else if ($mergedSelectedBrand->exists()) {
      $mergedProduct = $mergedSelectedBrand->first();
      if (isset($mergedProduct)) {
        $available = $mergedProduct->merged_qty;
      }
    } else {
      $available = DB::table('customer_has_products')->where('customer_id', $customer_id)
                    ->where('brand_id', $brand_id)
                    ->where('product_id', $product_id)
                    ->sum('label_qty');
    }
    $lastForecastDaysLabelDeduction = 0;
    $getDate = DB::table('sku_order_details')
                ->join('order_details', function($join) {
                    $join->on('order_details.order_id', '=', 'sku_order_details.order_id')
                    ->on('order_details.sku_id', '=', 'sku_order_details.sku_id');
                })
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 4)
                ->where('sku_order_details.product_id', $product_id)
                ->where('sku_order_details.brand_id', $brand_id)
                ->where('sku_order_details.customer_id', $customer_id)
                ->where('sku_order_details.deleted_at', NULL)
                ->where('order_details.qty', '>', 0)
                ->whereDate('orders.created_at', '>=', Carbon::now()->subDays($forecast_Val))
                ->select('order_details.qty as qty')
                ->get();
    foreach ($getDate as $gkey => $getAvg) {
      if (isset($getAvg)) {
          $lastForecastDaysLabelDeduction += $getAvg->qty;
      }
    }
    $alertMsg = '';
    if ($lastForecastDaysLabelDeduction <= 0) {
      $forecast_Days = $forecast_Val;
      $alertMsg = 'No Sale for ' . $forecast_Val . 'd';
    } else if ($lastForecastDaysLabelDeduction > 0) {
      $perDaySale = ceil($lastForecastDaysLabelDeduction / $forecast_Val);
      $forecast_Days = ceil($available / $perDaySale);
      if ($forecast_Days > 365) {
        $alertMsg = '365d+';
      } else {
        $alertMsg = $forecast_Days . 'd';
      }
    }
    if ($threshold == 0) {
      $html .= '<span class="badge rounded-pill me-1" style="background-color: red; color: white">Set Threshold</span>';
    } else {
      if ($forecast_Days == 0) {
        $html .= '<span class="badge rounded-pill me-1" data-sorting="' . $alertMsg . '" style="background-color: red; color: white">' . $alertMsg . '</span>';
      } else if ($forecast_Days < $threshold) {
        $html .= '<span class="badge rounded-pill me-1" data-sorting="' . $alertMsg . '" style="background-color: red; color: white">' . $alertMsg . '</span>';
      } else {
        $html .= '<span class="badge rounded-pill me-1" data-sorting="' . $alertMsg . '" style="background-color: green; color: white">' . $alertMsg . '</span>';
      }
    }
    return ['html' => $html, 'value' => $alertMsg, 'forecast_days' => $forecast_Days, 'lastdayssale' => $lastForecastDaysLabelDeduction, 'available_label_qty' => $available];
  }
}
