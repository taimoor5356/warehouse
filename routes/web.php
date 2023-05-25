<?php

use Carbon\Carbon;
use App\Models\Sku;
use App\Models\SkuOrder;
use App\AdminModels\Orders;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Models\OrderReturn;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Invoices;
use App\AdminModels\Products;
use App\Jobs\FixSkuOrdersJob;
use App\Jobs\UpdateSkuWeight;
use App\Models\MergedInvoice;
use App\Models\InvoicesMerged;
use Barryvdh\DomPDF\Facade\PDF;
use App\Models\OrderReturnDetail;
use App\Jobs\FixMergedInvoicesJob;
use App\Jobs\ProductLabelOrderJob;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\InventoryHistory;
use App\AdminModels\OrderDetails;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});

Route::get('/update-orders-status', function () {
    $orders = Orders::withTrashed()->get();
    foreach ($orders as $key => $order) {
        OrderDetails::withTrashed()->where('order_id', $order->id)->update([
            'status' => $order->status
        ]);
    }
});

Route::get('/truncate-jobs', function () {
    DB::table('jobs')->truncate();
});

Route::get('/view-jobs', function () {
    $jobs = DB::table('jobs')->get();
    dd($jobs);
});

Route::get('/view-failed-jobs', function () {
    $jobs = DB::table('failed_jobs')->get();
    dd($jobs);
});

Route::get('/update-return-selling-cost', function () {
    // OrderReturn::where('id', 245)->update([
    //     'cost_of_goods' => 2.82,
    //     'total_selling_cost' => 6.60,
    //     'total_price' => 2.82,
    // ]);
    OrderReturnDetail::where('order_return_id', 245)->update([
        'qty' => 1
    ]);
    $orderReturns = OrderReturn::withTrashed()->get();
    if (count($orderReturns) > 0) {
        foreach ($orderReturns as $key => $orderReturn) {
            if (isset($orderReturn)) {
                $orderReturnDetails = OrderReturnDetail::withTrashed()->where('order_return_id', $orderReturn->id)->get();
                $totalSellingPrice = 0;
                $totalCostOfGoods = 0;
                $totalQty = 0;
                if (count($orderReturnDetails) > 0) {
                    foreach ($orderReturnDetails as $key2 => $orderReturnDetail) {
                        if (isset($orderReturnDetail)) {
                            $product = Products::withTrashed()->where('id', $orderReturnDetail->product_id)->select(['price'])->first();
                            $productPrice = $product->price;
                            $customerHasProduct = CustomerHasProduct::withTrashed()->where('customer_id', $orderReturn->customer_id)->where('brand_id', $orderReturn->brand_id)->where('product_id', $orderReturnDetail->product_id)->first();
                            $sellingCost = $productPrice;
                            if (isset($customerHasProduct)) {
                                $sellingCost = $customerHasProduct->selling_price;
                            }
                            $orderReturnDetail->price = $productPrice;
                            $orderReturnDetail->selling_cost = $sellingCost;
                            $orderReturnDetail->save();
                            $totalSellingPrice += $sellingCost;
                            $totalCostOfGoods += $productPrice;
                            $totalQty += $orderReturnDetail->qty;
                        }
                    }
                }
                $orderReturn->cost_of_goods = $totalCostOfGoods;
                $orderReturn->total_selling_cost = $totalSellingPrice;
                $orderReturn->total_price = $totalCostOfGoods;
                $orderReturn->total_qty = $totalQty;
                $orderReturn->save();
            }
        }
    }
});

Route::get('/update-sku-weight', function () {
    $skus = Sku::with('sku_product:sku_id,product_id')->get();
    foreach ($skus as $key => $sku) {
        if (isset($sku)) {
            $skuWeight = 0;
            $skuProducts = $sku->sku_product;
            foreach ($skuProducts as $skey => $skuProduct) {
                if (isset($skuProduct)) {
                    $product = Products::where('id', $skuProduct->product_id)->select(['weight'])->first();
                    if (isset($product)) {
                        $skuWeight += $product->weight;
                    }
                }
            }
            $sku->weight = $skuWeight;
            $sku->save();
        }
    }
    // UpdateSkuWeight::dispatch();
});

Route::get('/fix-sku-orders', function () {
    FixSkuOrdersJob::dispatch();
});

Route::get('/fix-sku-order-seeder', function () {
    $exitCode = Artisan::call('db:seed FixSkuOrders');
    return 'DONE';
});

Route::get('/view-inventory-history/{from}/{to}/{product_id}', function ($from, $to, $productId) {
    $data = InventoryHistory::where('product_id', $productId)
                            ->whereDate('created_at', '>=', Carbon::parse($from)->format('Y-m-d'))
                            ->whereDate('created_at', '<=', Carbon::parse($to)->format('Y-m-d'))
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->toArray();
    dd($data);
});

Route::get('/view-return-orders/{from}/{to}/{product_id}', function($from, $to, $productId) {
    $data = OrderReturnDetail::where('product_id', $productId)
                            ->whereDate('created_at', '>=', Carbon::parse($from)->format('Y-m-d'))
                            ->whereDate('created_at', '<=', Carbon::parse($to)->format('Y-m-d'))
                            ->get()
                            ->toArray();
    dd($data);
});

Route::get('/update-merged-invoices/{id}/{date}', function($id, $date) {
    // if ($id == 43) {
        $mergedInvoices = MergedInvoice::where('id', $id)->first();
        if (isset($mergedInvoices)) {
            $invoices = InvoicesMerged::where('merged_invoice_id', $mergedInvoices->id)->get();
            foreach ($invoices as $key => $invoice) {
                $invoice->created_at = Carbon::parse($date)->format('Y-m-d');
                $invoice->updated_at = Carbon::parse($date)->format('Y-m-d');
                $invoice->save();
            }
            $mergedInvoices->created_at = Carbon::parse($date)->format('Y-m-d');
            $mergedInvoices->updated_at = Carbon::parse($date)->format('Y-m-d');
            $mergedInvoices->save();
        }
        MergedInvoice::where('id', $id)->update([
            'created_at' => Carbon::parse($date)->format('Y-m-d'),
            'updated_at' => Carbon::parse($date)->format('Y-m-d'),
        ]);
        return 'DONE';
    // } else {
        // return 'Error';
    // }
    // FixMergedInvoicesJob::dispatch();
});

Route::get('/run_migration', function () {
    // $exitCode = Artisan::call('migrate:rollback');
    // $exitCode = Artisan::call('migrate');
    return 'DONE'; //Return anything
});

Route::get('/artisan/{command}', function ($command) {
    $exitCode = Artisan::call($command);
    return 'DONE'; //Return anything
});

Route::get('/seeder', function(){
    $exitCode = Artisan::call('db:seed AddPermissionsSeeder');
    return 'DONE';
});

Route::get('/edit-batch-inventory-history', function () {
    $data = InventoryHistory::where('id', '=', 271858)->update([
        'manual_add' => 0,
        'edit_batch_qty' => 3148,
        'sales' => -3148
    ]);
    return 'DONE';
});

Route::get('/add-cust-return-charges', ['uses' => 'Admin\OrdersController@addCustomerReturnCharges']);

Route::get('/merged-data', function () {
    $mergedInvoices = MergedInvoice::where('id', 76)->get()->toArray();
    $invoicesMergeds = InvoicesMerged::where('merged_invoice_id', 76)->get()->toArray();
    dd($mergedInvoices, $invoicesMergeds);
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/set-product-label-order', function(){
        ProductLabelOrderJob::dispatch();
    });

    Route::get('/', [
        'uses' => 'Admin\HomeController@index',
        'as' => 'dashboard'
    ]);
    
    Route::get('/admin-home', [
        'uses' => 'Admin\HomeController@index',
        'as' => 'home.dashboard'
    ]);
    
    Route::get('user/admin', [
        'uses' => 'Admin\UsersController@index',
        'as' => 'user/admin'
    ]);

    Route::resource('user', 'Admin\UsersController');
    
    Route::get('delete/user/{id}', [
        'uses' => 'Admin\UsersController@destroy',
    ]);
    
    Route::get('edit/profile/{id}', [
        'uses' => 'Admin\UsersController@editProfile',
        'middleware' => 'permission:user_update'
    ]);
    
    Route::post('editProfileProcess', [
        'uses' => 'Admin\UsersController@editProfileProcess',
        'middleware' => 'permission:user_update'
    ]);
    
    Route::get('/add_category_product/{id}', [
        'uses' => 'Admin\CategoryController@addCategoryProduct',
        'as' => 'add_category_product',
        'middleware' => 'permission:product_create'
    ]);
    
    Route::post('/store_category_product/{id}', [
        'uses' => 'Admin\CategoryController@storeCategoryProduct',
        'as' => 'store_category_product',
        'middleware' => 'permission:product_create'
    ]);
    
    Route::get('/edit_category_product/{id}', [
        'uses' => 'Admin\CategoryController@editCategoryProduct',
        'as' => 'edit_category_product',
        'middleware' => 'permission:product_update'
    ]);
    
    Route::post('/update_category_product/{id}', [
        'uses' => 'Admin\CategoryController@updateCategoryProduct',
        'as' => 'update_category_product',
        'middleware' => 'permission:product_update'
    ]);
    
    Route::get('/category', [
        'uses' => 'Admin\CategoryController@index',
        'as' => 'category/admin',
        'middleware' => 'permission:category_view'
    ]);
    
    Route::get('category/admin', [
        'uses' => 'Admin\CategoryController@index',
        'as' => 'category/admin',
        'middleware' => 'permission:category_view'
    ]);
    
    Route::get('category/trash', [
        'uses' => 'Admin\CategoryController@trash',
        'as' => 'category.trash',
        'middleware' => 'permission:category_delete'
    ]);
    
    Route::get('category/{id}/restore', [
        'uses' => 'Admin\CategoryController@restore',
        'as' => 'category.restore',
        'middleware' => 'permission:category_delete'
    ]);
    
    Route::resource('category', 'Admin\CategoryController')->middleware('permission:category_view');
    
    Route::get('category/create', [
        'uses' => 'Admin\CategoryController@create',
        'middleware' => 'permission:category_create'
    ]);
    
    Route::get('delete/category/{id}', [
        'uses' => 'Admin\CategoryController@destroy',
        'middleware' => 'permission:category_delete'
    ]);
    
    Route::get('/category_products_report', [
        'uses' => 'Admin\ReportingController@categoryProductsReport',
        'as' => 'category_products_report',
        'middleware' => 'permission:report_category_product_view'
    ]);
    
    Route::post('/category_products_report_submit', [
        'uses' => 'Admin\ReportingController@categoryProductsReportSubmit',
        'as' => 'category_products_report_submit',
        'middleware' => 'permission:report_category_product_view'
    ]);
    
    Route::get('/show_single_category_product_report', [
        'uses' => 'Admin\ReportingController@showSingleCategoryProductReport',
        'as' => 'show_single_category_product_report',
        'middleware' => 'permission:report_category_product_view'
    ]);
    
    Route::get('/inventory-forecast-report', [
        'uses' => 'Admin\ReportingController@inventoryForecastReport',
        'as' => 'inventory_forecast_report', 
        'middleware' => 'permission:report_inventory_forecast_view'
    ]);
    
    Route::get('/showsingleproductreport', [
        'uses' => 'Admin\ReportingController@showSingleProductReport',
        'as' => 'showsingleproductreport',
        'middleware' => 'permission:report_view'
    ]);
    
    Route::get('/show_single_product_report', [
        'uses' => 'Admin\ReportingController@singleProductReport',
        'as' => 'show_single_product_report',
        'middleware' => 'permission:report_view'
    ]);
    
    Route::get('/product/report', [
        'uses' => 'Admin\ReportingController@productReport',
        'as' => 'product_report',
        'middleware' => 'permission:report_products_in_out_view'
    ]);
    
    Route::post('/product/submit/report', [
        'uses' => 'Admin\ReportingController@productSubmitReport',
        'as' => 'product_submit_report',
        'middleware' => 'permission:report_view'
    ]);

    Route::get('/postage-report', [
        'uses' => 'Admin\ReportingController@postageReporting',
        'as' => 'postage_reporting',
        'middleware' => 'permission:report_postage_view'
    ]);

    Route::post('filter-postage-report', [
        'uses' => 'Admin\ReportingController@filterPostageReporting',
        'as' => 'filter_postage_reporting',
        'middleware' => 'permission:report_postage_view'
    ]);

    Route::get('/products/add/{category_id?}', [
        'uses' => 'Admin\ProductsController@create',
        'middleware' => 'permission:product_create'
    ]);
    
    Route::get('/reset_table', [
        'uses' => 'Admin\ProductsController@resetTableSize'
    ]);
    
    Route::get('products/admin', [
        'uses' => 'Admin\ProductsController@index',
        'as' => 'products/admin',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/getProductstoCustomer', [
        'uses' => 'Admin\ProductsController@getProductstoCustomer',
        'as' => 'getProductstoCustomer',
    ]);
    
    Route::get('products/trash', [
        'uses' => 'Admin\ProductsController@trash',
        'as' => 'products.trash',
        'middleware' => 'permission:product_delete'
    ]);
    
    Route::get('products/{id}/restore', [
        'uses' => 'Admin\ProductsController@restore',
        'as' => 'products.restore',
        'middleware' => 'permission:product_delete'
    ]);
    
    Route::get('/get_product_sales/{id}', [
        'uses' => 'Admin\ProductsController@getProductSales',
        'as' => 'get_product_sales',
    ]);
    
    Route::resource('products', 'Admin\ProductsController')->middleware('permission:product_view');
    
    Route::get('products/create', [
        'uses' => 'Admin\ProductsController@create',
        'middleware' => 'permission:product_create'
    ]);
    
    Route::get('delete/product/{id}', [
        'uses' => 'Admin\ProductsController@destroy',
        'middleware' => 'permission:product_delete'
    ]);
    
    Route::post('/reset-inventory-qty', [
        'uses' => 'Admin\ProductsController@resetInventoryQty',
        'as' => 'reset-inventory-qty',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::post('/product/{id}/inventory', [
        'uses' => 'Admin\ProductsController@addInventory',
        'middleware' => 'permission:inventory_create'
    ]);
    
    Route::post('/product/{id}/reduce_inventory', [
        'uses' => 'Admin\ProductsController@reduceInventory',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::post('/getProductDetails', [
        'uses' => 'Admin\ProductsController@getProductDetails',
    ]);
    
    Route::get('categorywise/products', [
        'uses' => 'Admin\ProductsController@categoryProducts',
        'as' => 'categorywise/products',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::post('selected-categories-products', [
        'uses' => 'Admin\ProductsController@selectedCategoriesProducts',
        'as' => 'selected_categories_products',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/categorywise/products/{id}', [
        'uses' => 'Admin\ProductsController@categoryProducts',
        'as' => 'categoryProducts',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/product/product_history/{id?}', [
        'uses' => 'Admin\ProductsController@productHistory',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/product/product_history', [
        'uses' => 'Admin\ProductsController@productHistory',
        'as' => 'product/product_history',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/product/{id?}/view_order_details', [
        'uses' => 'Admin\ProductsController@viewOrderDetails',
        'as' => 'product/view_order_details',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/getProductDetailsSku', [
        'uses' => 'Admin\ProductsController@getProductDetailsSku',
        'as' => 'getProductDetailsSku',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/all/brand_products', [
        'uses' => 'Admin\CustomerController@brandProducts',
        'as' => 'brand_products',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/get_cust_brand_prod', [
        'uses' => 'Admin\CustomerController@getCustBrandProd',
        'as' => 'get_cust_brand_prod',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/get_cust_brand_prods', [
        'uses' => 'Admin\CustomerController@getCustBrandProds',
        'as' => 'get_cust_brand_prods',
        // 'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/get_product_detail/{id}', [
        'uses' => 'Admin\ProductsController@getProductDetail',
        'as' => 'get_product_detail',
        'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/delete_customer_brand_product/{c_id}/{b_id}/{p_id}', [
        'uses' => 'Admin\CustomerController@deleteCustomerBrandProduct',
        'as' => 'delete_customer_brand_product',
        'middleware' => 'permission:product_delete'
    ]);
    
    Route::get('/get_cust_brand_remaining_prods', [
        'uses' => 'Admin\CustomerController@getCustBrandRemainingProds',
        'as' => 'get_cust_brand_remaining_prods',
        // 'middleware' => 'permission:product_view'
    ]);
    
    Route::get('/add_customer_label/{id}', [
        'uses' => 'Admin\CustomerController@addCustomerLabel',
        'as' => 'add_customer_label',
        'middleware' => 'permission:labels_create'
    ]);
    
    Route::get('/add_brand_products/{id}', [
        'uses' => 'Admin\CustomerController@addBrandProducts',
        'as' => 'add_brand_products',
        'middleware' => 'permission:labels_create'
    ]);
    
    Route::get('/add_customer_product', [
        'uses' => 'Admin\CustomerController@addCustomerProduct',
        'as' => 'add_customer_product',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::get('/customer_products/{id}', [
        'uses' => 'Admin\CustomerController@customerProducts',
        'as' => 'customer_products',
        'middleware' => 'permission:customer_view'
    ]);

    Route::get('/get-customer-products/{id?}', [
        'uses' => 'Admin\ReportingController@customerProducts',
        'as' => 'get_customer_products',
        'middleware' => 'permission:customer_view'
    ]);

    Route::get('/get-customer-brand-products/{c_id?}/{b_id?}', [
        'uses' => 'Admin\ReportingController@getCustomerBrandProducts',
        'as' => 'get_customer_brand_products',
        // 'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/customer/{id}/show_all', [
        'uses' => 'Admin\CustomerController@showAll',
        'as' => 'show_all',
        'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/customer/{id}/show_all_products', [
        'uses' => 'Admin\CustomerController@showAllProducts',
        'as' => 'show_all_products',
        'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('customers/admin', [
        'uses' => 'Admin\CustomerController@index',
        'as' => 'customers/admin',
        'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/add_labels/get_labels_data', [
        'uses' => 'Admin\LabelsController@getLabelsData',
        'as' => 'get_labels_data',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('/add_labels/get_brand_products', [
        'uses' => 'Admin\LabelsController@getBrandProducts',
        'as' => 'get_brand_products',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('/customers/trash', [
        'uses' => 'Admin\CustomerController@trash',
        'as' => 'customer-trash',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/customers/{id}/restore', [
        'uses' => 'Admin\CustomerController@restore',
        'as' => 'customer-restore',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/add_labels', [
        'uses' => 'Admin\CustomerController@add_labels',
        'as' => 'add_labels',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::post('/save_customer_product/{id}', [
        'uses' => 'Admin\CustomerController@saveCustomerProduct',
        'as' => 'save_customer_product',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::post('/save_customer_brand_product', [
        'uses' => 'Admin\CustomerController@saveCustomerBrandProduct',
        'as' => 'save_customer_brand_product',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::post('/update_customer_prod_selling_price', [
        'uses' => 'Admin\CustomerController@updateCustomerProdSellingPrice',
        'as' => 'update_customer_prod_selling_price',
        'middleware' => 'permission:customer_update'
    ]);
    
    Route::get('/delete_customer_prod/{c_id}/{p_id}', [
        'uses' => 'Admin\CustomerController@deleteCustomerProd',
        'as' => 'delete_customer_prod',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/customer_trashed_products/{id}', [
        'uses' => 'Admin\CustomerController@customerTrashedProducts',
        'as' => 'customer_trashed_products',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/restore_customer_trashed_product/{c_id}/{p_id}', [
        'uses' => 'Admin\CustomerController@restoreCustomerTrashedProduct',
        'as' => 'restore_customer_trashed_product',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/customers/{id}/edit', [
        'uses' => 'Admin\CustomerController@edit',
        'middleware' => 'permission:customer_update'
    ]);
    
    Route::get('/customers', [
        'uses' => 'Admin\CustomerController@index',
        'as' => 'customers/index',
        'middleware' => 'permission: customer_view'
    ]);
    
    Route::resource('customers', 'Admin\CustomerController')->middleware('permission:customer_view');;
    
    Route::get('customers/create', [
        'uses' => 'Admin\CustomerController@create',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::get('/customer/{id}/brands', [
        'uses' => 'Admin\CustomerController@customerBrands',
        'as' => 'customer-labels',
        // 'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/customer/{id}/brand', [
        'uses' => 'Admin\CustomerController@getCustomerBrand',
        'as' => 'customer-brand',
        // 'middleware' => 'permission:return_order_create'
    ]);
    
    Route::get('/getAllBrands', [
        'uses' => 'Admin\CustomerController@getAllBrands',
        'as' => 'getAllBrands',
        // 'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/customer/{id}/service-charges', [
        'uses' => 'Admin\CustomerController@getServiceCharges',
        'as' => 'customer-service-charges',
        // 'middleware' => 'permission:customer_view'
    ]);
    Route::get('/customer/{cid}/{oid}/{bid}/edit-order-service-charges', [
        'uses' => 'Admin\CustomerController@editOrderGetServiceCharges',
        'as' => 'customer-edit-order-service-charges',
        // 'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('customer/delete/{id}', [
        'uses' => 'Admin\CustomerController@permanentlyDeleteCustomer',
        'as' => 'permanent-delete-customer',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/customer/{id}/brand/create', [
        'uses' => 'Admin\CustomerController@createCustomerBrand',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::post('/customer/{id}/brand/store', [
        'uses' => 'Admin\CustomerController@storeCustomerBrand',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::get('/edit_customer_brand', [
        'uses' => 'Admin\CustomerController@editCustomerBrand',
        'as' => 'edit_customer_brand',
        'middleware' => 'permission:customer_update'
    ]);
    
    Route::get('/customer/{id}/brand/{brand_id}/edit', [
        'uses' => 'Admin\CustomerController@editCustomerSingleBrand',
        'as' => 'editCustomerBrand',
        'middleware' => 'permission:customer_update'
    ]);
    
    Route::post('/customer/{id}/brand/{brand_id}/update', [
        'uses' => 'Admin\CustomerController@updateCustomerBrand',
        'middleware' => 'permission:customer_update'
    ]);
    
    Route::get('/customer/{id}/brand/{brand_id}/labels/history', [
        'uses' => 'Admin\CustomerController@customerBrandLabelsHistory',
        'middleware' => 'permission:customer_view'
    ]);
    
    Route::get('/customer/{id}/brands/trash', [
        'uses' => 'Admin\CustomerController@customerBrandTrash',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('/getCustomerLabel/{id}', [
        'uses' => 'Admin\CustomerController@getCustomerLabel',
        'as' => 'getCustomerLabel',
        'middleware' => 'permission:customer_view'
    ]);
    
    Route::post('/set_label_status', [
        'uses' => 'Admin\CustomerController@setLabelStatus',
        'as' => 'set_label_status',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::post('/set_seller_cost_status', [
        'uses' => 'Admin\CustomerController@setSellerCostStatus',
        'as' => 'set_seller_cost_status',
        'middleware' => 'permission:customer_create'
    ]);
    
    Route::get('/get_customer_charges/{id}', [
        'uses' => 'Admin\CustomerController@getCustomerCharges',
        'as' => 'get_customer_charges',
    ]);
    
    Route::get('/delete/customer/{id}', [
        'uses' => 'Admin\CustomerController@destroy',
        'middleware' => 'permission:customer_delete'
    ]);
    
    Route::get('inventory/admin', [
        'uses' => 'Admin\InventoryController@index',
        'as' => 'inventory/admin',
        'middleware' => 'permission:inventory_view'
    ]);

    Route::post('/inventory-cost-total', [
        'uses' =>'Admin\InventoryController@inventoryCostTotal',
        'as' => 'inventory_cost_total'
    ]);
    
    Route::get('inventory/trash', [
        'uses' => 'Admin\InventoryController@trashInv',
        'as' => 'inventory/trash',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::get('inventory/history', [
        'uses' => 'Admin\InventoryController@inventoryHistory',
        'as' => 'inventory/history',
        'middleware' => 'permission:inventory_view'
    ]);
    
    Route::get('/inventory/history/{id?}', [
        'uses' => 'Admin\InventoryController@inventoryHistory',
        'middleware' => 'permission:inventory_view'
    ]);
    
    Route::get('/restoreTrash/{id}', [
        'uses' => 'Admin\InventoryController@restoreTrash',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::resource('inventory', 'Admin\InventoryController')->middleware('permission:inventory_view');;
    
    Route::get('delete/inventory/{id}', [
        'uses' => 'Admin\InventoryController@destroy',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::get('inventory/history/{id}/revert', [
        'uses' => 'Admin\InventoryController@revertInventory',
        'middleware' => 'permission:inventory_delete'
    ]);
    
    Route::get('upcoming/inventory/admin', [
        'uses' => 'Admin\UpcomingInventoryController@index',
        'as' => 'upcoming/inventory/admin',
        'middleware' => 'permission:upcoming_inventory_view'
    ]);
    
    Route::get('upcoming_inventory/create/{id?}', [
        'uses' => 'Admin\UpcomingInventoryController@create',
        // 'as' => 'upcoming/inventory/admin',
        'middleware' => 'permission:upcoming_inventory_create'
    ]);
    
    Route::resource('upcoming_inventory', 'Admin\UpcomingInventoryController')->middleware('permission:upcoming_inventory_view');;
    
    Route::get('delete/upcoming/inventory/{id}', [
        'uses' => 'Admin\UpcomingInventoryController@destroy',
        'middleware' => 'permission:upcoming_inventory_delete'
    ]);
    
    
    Route::get('otw/inventory/admin', [
        'uses' => 'Admin\OtwInventoryController@index',
        'as' => 'otw/inventory/admin',
        'middleware' => 'permission:otw_inventory_view'
    ]);
    
    Route::resource('otw_inventory', 'Admin\OtwInventoryController')->middleware('permission:otw_inventory_view');;
    
    Route::get('delete/otw/inventory/{id}', [
        'uses' => 'Admin\OtwInventoryController@destroy',
        'middleware' => 'permission:otw_inventory_delete'
    ]);
    
    Route::post('otw_inventory/storeStock', [
        'uses' => 'Admin\OtwInventoryController@storeStock',
        'middleware' => 'permission:otw_inventory_move_stock'
    ]);
    
    Route::get('/get_brand_mailer', [
        'uses' => 'Admin\LabelsController@getBrandMailer',
        'as' => 'get_brand_mailer'
    ]);
    
    Route::get('/get_edit_brand_mailer', [
        'uses' => 'Admin\LabelsController@getEditBrandMailer',
        'as' => 'get_edit_brand_mailer'
    ]);
    
    Route::get('brands/admin', [
        'uses' => 'Admin\LabelsController@index',
        'as' => 'label/admin',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('brands/manage_labels', [
        'uses' => 'Admin\LabelsController@manageLabels',
        'as' => 'manage_labels',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('/brands/add/{customer_id?}/{brand?}', [
        'uses' => 'Admin\LabelsController@create',
        'middleware' => 'permission:labels_create'
    ]);
    
    Route::resource('brands', 'Admin\LabelsController')->middleware('permission:labels_view');;
    
    Route::get('brands/create', [
        'uses' => 'Admin\LabelsController@create',
        'middleware' => 'permission:labels_create'
    ]);
    
    Route::get('delete/brand/{id}', [
        'uses' => 'Admin\LabelsController@destroy',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('brands/history/table', [
        'uses' => 'Admin\LabelsController@labelsHistory',
        'as' => 'brands/history/table',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('/brand/history/{id?}', [
        'uses' => 'Admin\LabelsController@labelsHistory',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('labels/trash', [
        'uses' => 'Admin\LabelsController@trashLabels',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('brands/trash/table', [
        'uses' => 'Admin\LabelsController@trashLabels',
        'as' => 'brands/trash/table',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('brands/restoreTrash/{id}', [
        'uses' => 'Admin\LabelsController@restoreTrash',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::post('brand/{id}/labels', [
        'uses' => 'Admin\LabelsController@addLabels',
        'as' => 'add-labels',
        'middleware' => 'permission:labels_create'
    ]);

    Route::post('toggleLabelCost', [
        'uses' => 'Admin\LabelsController@toggleLabelCost',
        'as' => 'toggleLabelCost',
        'middleware' => 'permission:labels_create'
    ]);
    
    Route::get('brand/history/{id}/revert', [
        'uses' => 'Admin\LabelsController@revertLabels',
        'as' => 'revert-labels',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('get_product_labels/{id}', [
        'uses' => 'Admin\LabelsController@getProductLabels',
        'as' => 'get_product_labels'
    ]);
    
    Route::post('add_label_to_product', [
        'uses' => 'Admin\LabelsController@addLabelToProduct',
        'as' => 'add_label_to_product'
    ]);
    
    Route::post('reset_label_to_zero', [
        'uses' => 'Admin\LabelsController@resetLabelToZero',
        'as' => 'reset-label-to-zero'
    ]);
    
    Route::post('add_alert_days_to_product', [
        'uses' => 'Admin\LabelsController@addAlertDaysToProduct',
        'as' => 'add_alert_days_to_product'
    ]);
    
    Route::post('reduce_label_to_product', [
        'uses' => 'Admin\LabelsController@reduceLabelToProduct',
        'as' => 'reduce_label_to_product'
    ]);
    
    Route::post('add_label_cost_to_product', [
        'uses' => 'Admin\LabelsController@addLabelCostToProduct',
        'as' => 'add_label_cost_to_product'
    ]);
    
    Route::get('/customer/{c_id}/brand/{b_id}/product/{p_id}/labels', [
        'uses' => 'Admin\LabelsController@productLabelsHistory',
        'as' => 'product_labels_history',
        'middleware' => 'permission:labels_view'
    ]);
    
    Route::get('/delete_prod_labels_history/{id}', [
        'uses' => 'Admin\LabelsController@deleteProdLabelsHistory',
        'as' => 'delete_prod_labels_history',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('/trash_product_labels_history', [
        'uses' => 'Admin\LabelsController@trashProductLabelsHistory',
        'as' => 'trash_product_labels_history',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('/revert_product_label/{id}', [
        'uses' => 'Admin\LabelsController@revertProductLabel',
        'as' => 'revert_product_label',
        'middleware' => 'permission:labels_delete'
    ]);
    
    Route::get('/labelsforecast', [
        'uses' => 'Admin\LabelsController@labelsForecast',
        'as' => 'labelsforecast',
        'middleware' => 'permission:report_labels_forecast_view'
    ]);
    
    // Spatie Role and Permission
    Route::get('role/admin', [
        'uses' => 'RoleController@index',
        'as' => 'role/admin',
        'middleware' => 'permission:userrole_view'
    ]);
    
    Route::resource('roles', 'RoleController');
    
    Route::get('delete/role/{id}', [
        'uses' => 'RoleController@destroy',
        'middleware' => 'permission:userrole_delete'
    ]);
    
    Route::get('role_has_permissions/{id}', [
        'uses' => 'RoleController@roleHasPermissions',
        'middleware' => 'permission:userrole_view'
    ]);
    
    Route::post('/assign_permissions', [
        'uses' => 'RoleController@assignPermissions',
        'middleware' => 'permission:userrole_view'
    ]);
    
    Route::get('role_permissions/{id}', 'Admin\UserRolesController@permissions');
    
    Route::get('/create_return_order', [
        'uses' => 'Admin\OrdersController@createReturnOrder',
        'as' => 'create_return_order',
        'middleware' => 'permission:return_order_create'
    ]);
    
    Route::get('/check_order_number', [
        'uses' => 'Admin\OrdersController@checkOrderNumber',
        'as' => 'check_order_number'
    ]);
    
    Route::post('/save_return_order', [
        'uses' => 'Admin\OrdersController@saveReturnOrder',
        'as' => 'save_return_order',
        'middleware' => 'permission:return_order_create'
    ]);
    
    Route::get('/view_returned_products/{id}', [
        'uses' => 'Admin\OrdersController@viewReturnedProducts',
        'as' => 'view_returned_products',
        'middleware' => 'permission:return_order_view'
    ]);
    
    Route::get('/product_notes/{order_id}', [
        'uses' => 'Admin\OrdersController@productNotes',
        'as' => 'product_notes',
        'middleware' => 'permission:return_order_create'
    ]);
    
    Route::post('/update_product_notes', [
        'uses' => 'Admin\OrdersController@updateProductNotes',
        'as' => 'update_product_notes',
        'middleware' => 'permission:return_order_update'
    ]);
    
    Route::get('edit_order_details', [
        'uses' => 'Admin\OrdersController@editOrderDetails',
        'as' => 'edit_order_details',
        'middleware' => 'permission:order_update'
    ]);
    
    Route::post('orders/{id}/update', [
        'uses' => 'Admin\OrdersController@update',
        'as' => 'update_order',
        'middleware' => 'permission:order_update'
    ]);
    
    Route::post('orders/{id}/delete', [
        'uses' => 'Admin\OrdersController@destroy',
        'as' => 'delete_order',
        'middleware' => 'permission:order_delete'
    ]);
    
    Route::get('order_return', [
        'uses' => 'Admin\OrdersController@orderReturn',
        'as' => 'orders.orderReturn',
        'middleware' => ['permission:report_returned_view|return_order_view']
    ]);

    Route::post('/total-return-orders', function (Request $request) {
        try {
            $returnedOrders = OrderReturn::with('order_return_details_with_trashed')->where('total_qty', '!=', 0);
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
                $customerId = $customerUser->customer_id;
            }
            if (Auth::user()->hasRole('customer')) {
                $returnedOrders = $returnedOrders->where('customer_id', $customerId);
            } else {
                if (!empty($request->customer)) {
                    $returnedOrders = $returnedOrders->where('customer_id', $request->customer);
                }
            }
            if (!empty($request->min_date)) {
                $returnedOrders = $returnedOrders->whereDate('created_at', '>=', Carbon::parse($request->min_date));
                if (!empty($request->max_date)) {
                    $returnedOrders = $returnedOrders->whereDate('created_at', '<=', Carbon::parse($request->max_date));
                }
            }
            $returnedOrders = $returnedOrders->get();
            $getTotalReturnedProducts = 0;
            $getTotalSellingCost = 0;
            $totalSellingCost = 0;
            $totalReturnCharges = 0;
            foreach ($returnedOrders as $key => $order) {
                if (isset($order)) {
                    if ($order->order_return_details_with_trashed) {
                        $getTotalReturnedProducts += $order->order_return_details_with_trashed->count();
                        if ($order->status != 2 && $order->status != 3) {
                            $getTotalSellingCost += $order->total_selling_cost;
                        }
                        if ($order->status == 2) {
                          $totalSellingCost += (0 - $order->cust_return_charges);
                        } else if ($order->status == 3) {
                          $totalSellingCost += (0 - $order->cust_return_charges);
                        } else {
                          $totalSellingCost += $order->total_selling_cost - $order->cust_return_charges;
                        }
                    }
                    $totalReturnCharges += $order->cust_return_charges;
                }
            }
            return response()->json(['status' => true, 'total_returned_products' => $getTotalReturnedProducts, 'total_selling_cost' => $getTotalSellingCost, 'total_credit' => $totalSellingCost, 'total_return_charges' => $totalReturnCharges]);
        } catch (\Exception $e) {
            dd($e);
        }
    })->name('total_return_orders');
    
    Route::get('return-order/edit/{order_id}', [
        'uses' => 'Admin\OrdersController@editReturnOrder',
        'as' => 'order_return_edit',
        'middleware' => 'permission:return_order_update'
    ]);
    
    Route::get('return-order/delete/{order_id}/{customer_id?}/{delete?}', [
        'uses' => 'Admin\OrdersController@deleteReturnOrder',
        'as' => 'order_return_delete',
        'middleware' => 'permission:return_order_delete'
    ]);

    Route::post('/update-return-order/{order_id}', [
        'uses' => 'Admin\OrdersController@updateReturnOrder',
        'as' => 'update_return_order',
        'middleware' => 'permission:return_order_update'
    ]);
    
    Route::resource('orders', 'Admin\OrdersController')->middleware('permission:order_view');;

    Route::post('/get-customer-total-batches', function(Request $request) {
        $batches = Orders::where('deleted_at', NULL);
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
        }
        if (Auth::user()->hasRole('customer')) {
            $batches = $batches->where('customer_id', $customerId);
        } else {
            if (!empty($request->customer)) {
                $batches = $batches->where('customer_id', $request->customer);
            }
        }
        if (!empty($request->brand)) {
            $batches = $batches->where('brand_id', $request->brand);
        }
        if (!empty($request->min_date)) {
            $batches = $batches->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
            if (!empty($request->max_date)) {
                $batches = $batches->whereDate('created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
            }
        }
        if (!empty($request->status)) {
            if ($request->status == 0) {
                $batches = $batches->where('status', '=', 0);
            } else if ($request->status == 1) {
                $batches = $batches->where('status', '=', 1);
            } else if ($request->status == 2) {
                $batches = $batches->where('status', '=', 2);
            } else if ($request->status == 3) {
                $batches = $batches->where('status', '=', 3);
            } else if ($request->status == 4) {
                $batches = $batches->where('status', '=', 4);
            } else {
                $batches = $batches->where('status', '!=', 4);
            }
        } else {
            $batches = $batches->where('status', '!=', 4);
        }
        $totalMailer = $batches->sum('mailerqty');
        $totalBatchesCost = $batches->sum('total_cost');
        if (!empty($request->status)) {
            if ($request->status == 0) {
            } else if ($request->status == 1) {
                $totalMailer = $totalMailer;
                $totalBatchesCost = $totalBatchesCost;
            } else if ($request->status == 2) {
                $totalMailer = $totalMailer;
                $totalBatchesCost = $totalBatchesCost;
            } else if ($request->status == 3) {
                $totalMailer = $totalMailer;
                $totalBatchesCost = $totalBatchesCost;
            } else if ($request->status == 4) {
                $totalMailer = 0;
                $totalBatchesCost = 0;
            } else {
                $totalMailer = $totalMailer;
                $totalBatchesCost = $totalBatchesCost;
            }
        } else {
            $totalMailer = $totalMailer;
            $totalBatchesCost = $totalBatchesCost;
        }
        return response()->json(['status' => true, 'mailerqty' => $totalMailer, 'total_cost' => $totalBatchesCost]);
    })->name('get_customer_total_batches');

    Route::post('/footer-invoice-total', function(Request $request) {
        $query = Invoices::with('orders','Customer', 'brand_data')
                ->whereHas('orders', function ($q) {
                  $q->where('status', '!=', 4);
                })
                ->whereHas('brand_data', function($q){
                  $q->withTrashed();
                });
        if (!empty($request->customer)) {
            $query->whereHas("Customer", function ($q) use ($request) {
                $q->where("id", "=", $request->customer);
            });
        }
        if (!empty($request->brand)) {
          $order = Orders::where('brand_id', $request->brand)->where('status', '!=', 4)->get()->pluck('id');
          if (isset($order)) {
            $query = $query->whereIn('order_id', $order);
          }
        }
        // if (!empty($request->customer)) {
        //     $query = $query->where('customer_id', $request->customer);
        // }
        // if (!empty($request->brand)) {
        //   $order = Orders::where('deleted_at', NULL)
        //                 ->where('brand_id', $request->brand)
        //                 ->where('status', '!=', 4)
        //                 ->get()
        //                 ->pluck('id');
        // //   if (isset($order)) {
        //     $query = $query->whereIn('order_id', $order);
        // //   }
        // }
        if (!empty($request->min_date)) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
            if (!empty($request->max_date)) {
                $query = $query->whereDate('created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
            }
        }
        $invoiceTotal = $query->sum('grand_total');
        $paidTotal = $query->sum('paid');
        $remainingTotal = $query->sum('remaining');
        return response()->json(['status' => true, 'invoice_total' => $invoiceTotal, 'paid_total' => $paidTotal, 'remaining_total' => $remainingTotal]);
    })->name('footer_invoice_total');
    
    Route::post('orders/search', [
        'uses' => 'Admin\OrdersController@index',
        'as' => 'orders/search',
        'middleware' => 'permission:order_view'
    ]);
    
    Route::get('order/listing', [
        'uses' => 'Admin\OrdersController@index',
        'as' => 'order/listing',
        'middleware' => 'permission:order_view'
    ]);
    
    Route::post('updateOrderStatus', [
        'uses' => 'Admin\OrdersController@updateOrderStatus',
        'middleware' => 'permission:order_update'
    ]);
    
    Route::get('order/details/{id?}', [
        'uses' => 'Admin\OrdersController@orderDetail',
        'middleware' => 'permission:order_view'
    ]);

    Route::post('/check-job-status', function(Request $request) {
        $jobDetails = getInvoicesJobsDetails($request->job_name1, $request->job_name2);
        if ($jobDetails == 'exists') {
            $mergeLeft = DB::table('orders')->where('merged', 0)->where('merge_running', 1)->count();
            return response()->json(['status' => true, 'msg' => 'Merging Invoices...', 'left_invoices' => $mergeLeft]);
        } else {
            return response()->json(['status' => false, 'msg' => '']);
        }
    })->name('check_job_status');
    
    Route::get('invoices', [
        'uses' => 'Admin\InvoicesController@index',
        'as' => 'invoice/listing',
        'middleware' => 'permission:invoices_view'
    ]);
    
    Route::resource('invoices', 'Admin\InvoicesController')->middleware('permission:invoices_view');;
    
    Route::get('invoice/listing', [
        'uses' => 'Admin\InvoicesController@index',
        'as' => 'invoice/listing',
        'middleware' => 'permission:invoices_view'
    ]);
    
    Route::post('updateInvoiceStatus', [
        'uses' => 'Admin\InvoicesController@updateInvoiceStatus',
        'middleware' => 'permission:order_update'
    ]);
    
    Route::get('invoice/details/{id?}', [
        'uses' => 'Admin\InvoicesController@invoiceDetails',
        'middleware' => 'permission:invoices_view'
    ]);
    
    Route::get('invoice/order/details/{id?}', [
        'uses' => 'Admin\InvoicesController@orderInvoiceDetails',
        'as' => 'orderInvoiceDetails',
        'middleware' => 'permission:invoices_view'
    ]);
    
    Route::post('getBrands', [
        'uses' => 'Admin\OrdersController@getBrands',
        'middleware' => 'permission:order_view'
    ]);
    
    Route::post('getStates', [
        'uses' => 'Admin\OrdersController@getStates',
        // 'middleware' => 'permission:order_view'
    ]);
    
    Route::post('getCities', [
        'uses' => 'Admin\OrdersController@getCities',
        // 'middleware' => 'permission:order_view'
    ]);
    
    Route::get('units/admin', [
        'uses' => 'Admin\UnitsController@index',
        'as' => 'units/admin',
        'middleware' => 'permission:unit_view'
    ]);
    
    Route::resource('units', 'Admin\UnitsController')->middleware('permission:unit_view');
    
    Route::get('delete/unit/{id}', [
        'uses' => 'Admin\UnitsController@destroy',
        'middleware' => 'permission:unit_view'
    ]);
    
    Route::get('/batch', [
        'uses' => 'Admin\OrdersController@getBatchNumber',
        'as' => 'get-batch-number',
        'middleware' => 'permission:order_view'
    ]);
    // sku routes
    
    Route::get('sku/trash', [
        'uses' => 'Admin\SkuController@trash',
        'as' => 'sku-trash',
        'middleware' => 'permission:sku_delete'
    ]);
    
    Route::get('sku/restore/{id}', [
        'uses' => 'Admin\SkuController@restore',
        'as' => 'sku-restore',
        'middleware' => 'permission:sku_delete'
    ]);
    
    Route::get('brand/{id}/sku', [
        'uses' => 'Admin\SkuController@brandSku',
        'as' => 'brand-sku',
        'middleware' => 'permission:sku_view'
    ]);
    
    Route::get('brand/{id}/sku/create', [
        'uses' => 'Admin\SkuController@createBrandSku',
        'as' => 'brand-sku-create',
        'middleware' => 'permission:sku_create'
    ]);
    
    Route::get('brand/{id}/sku/{sku_id}/edit', [
        'uses' => 'Admin\SkuController@editBrandSku',
        'as' => 'brand-sku-edit',
        'middleware' => 'permission:sku_update'
    ]);
    
    Route::get('brand/{id}/sku/trash', [
        'uses' => 'Admin\SkuController@trash',
        'as' => 'brand-sku-trash',
        'middleware' => 'permission:sku_delete'
    ]);
    
    Route::get('sku/delete/{id}', [
        'uses' => 'Admin\SkuController@permanentlyDeleteSku',
        'as' => 'permanent-delete-sku',
        'middleware' => 'permission:sku_delete'
    ]);
    
    Route::get('sku/{id}/delete', [
        'uses' => 'Admin\SkuController@destroy',
        'as' => 'delete-sku',
        'middleware' => 'permission:sku_delete'
    ]);
    
    Route::get('brand/{id}/sku/list', [
        'uses' => 'Admin\SkuController@getBrandSku',
        'as' => 'get-brand-sku',
        // 'middleware' => 'permission:sku_view'
    ]);
    
    Route::get('sku/{id}/detail', [
        'uses' => 'Admin\SkuController@skuDetail',
        'as' => 'get-sku-detail',
        'middleware' => 'permission:sku_view'
    ]);

    Route::get('/sku', [
        'uses' => 'Admin\SkuController@index',
        'middleware' => 'permission:sku_view'
    ]);
    
    Route::resource('sku', 'Admin\SkuController')->middleware('permission:sku_view');

    Route::post('/get-sku-products-and-labels', [
        'uses' => 'Admin\SkuController@getSkuProductsAndLabels',
        'as' => 'get_sku_products_and_labels'
    ]);
    
    Route::get('sku/create', [
        'uses' => 'Admin\SkuController@create',
        'as' => 'sku.create',
        'middleware' => 'permission:sku_create'
    ]);
    
    Route::get('/sku/{id}/product_details', [
        'uses' => 'Admin\SkuController@skuProductDetails',
        'as' => 'skuProductDetails',
        // 'middleware' => 'permission:sku_view'
    ]);
    
    Route::get('/edit-order-sku/{id}/product_details', [
        'uses' => 'Admin\SkuController@editOrderSkuProductDetails',
        'as' => 'edit_order_sku_product_details',
        'middleware' => 'permission:sku_view'
    ]);
    
    Route::get('/getCountsProducts', [
        'uses' => 'Admin\SkuController@getCountsProducts',
        'as' => 'getCountsProducts',
        // 'middleware' => 'permission:sku_view'
    ]);
    //  reporting
    Route::get('reports/profit/{type?}', [
        'uses' => 'Admin\ReportingController@profitReport',
        'as' => 'profit-report',
        'middleware' => 'permission:report_profit_view'
    ]);
    
    Route::get('reports/labels_report', [
        'uses' => 'Admin\ReportingController@labelsReport',
        'as' => 'labels_report',
        'middleware' => 'permission:report_labels_view'
    ]);
    
    Route::get('reports/sales/{type?}', [
        'uses' => 'Admin\ReportingController@salesReport',
        'as' => 'sales-report',
        'middleware' => 'permission:report_sales_view'
    ]);
    // stock forecasting
    Route::get('near_to_empty', [
        'uses' => 'Admin\InventoryController@nearToEmptyData',
        'as' => 'near_to_empty',
        'middleware' => 'permission:product_view'
    ]);

    Route::get('category/{id}/products', [
        'uses' => 'Admin\InventoryController@categoryProducts',
        'as' => 'categor_products_filter',
        // 'middleware' => 'permission:product_view'
    ]);
    // settings
    Route::get('settings_state', [
        'uses' => 'Admin\SettingController@settingsState',
        'as' => 'settings_state',
        // 'middleware' => 'permission:setting_update'
    ]);
    
    Route::get('get_return_order_state', [
        'uses' => 'Admin\SettingController@getReturnOrderState',
        'as' => 'get_return_order_state',
        // 'middleware' => 'permission:setting_update'
    ]);

    Route::get('get-create-return-order-state', [
        'uses' => 'Admin\SettingController@getCreateReturnOrderState',
        'as' => 'get_create_return_order_state'
    ]);

    Route::post('save-create-return-order-state', [
        'uses' => 'Admin\SettingController@saveReturnOrderState',
        'as' => 'save_create_return_order_state'
    ]);
    
    Route::get('settings', [
        'uses' => 'Admin\SettingController@edit',
        'as' => 'settings.edit',
        'middleware' => 'permission:setting_update'
    ]);
    
    Route::post('settings/update', [
        'uses' => 'Admin\SettingController@update',
        'as' => 'settings.update',
        'middleware' => 'permission:setting_update'
    ]);
    
    Route::post('settings/update_return_order_state', [
        'uses' => 'Admin\SettingController@returnOrderState',
        'as' => 'update_return_order_state',
    ]);
    // check pin
    Route::post('check_pin', [
        'uses' => 'Admin\PinCodeController@check_pin',
        'as' => 'pin_code.check_pin',
    ]);
    
    Route::get('get-return-pdf/{id}/{type?}', [
        'uses' => 'ReturnOrder\ReturnOrderController@returnInvoicePdf',
        'as' => 'get-return-pdf',
        // 'middleware' => 'permission:setting_update'
    ]);
    // getting upcoming inventory for indivual product
    Route::get('upcoming-inventory-by-item/{id}', [
        'uses' => 'Admin\UpcomingInventoryController@show',
        'as' => 'upcoming-inventory-by-item',
        'middleware' => 'permission:upcoming_inventory_view'
    ]);
    //  getting otw for indivual item
    Route::get('otw-inventory-by-item/{id}', [
        'uses' => 'Admin\OtwInventoryController@show',
        'as' => 'otw-inventory-by-item',
        'middleware' => 'permission:otw_inventory_view'
    ]);
    //  files
    Route::resource('stored_files', 'FileController')->middleware('permission:file_storage_view');
    
    Route::post('/get-customer-files', [
        'uses' => 'FileController@getCustomerFiles',
        'as' => 'get_customer_files'
    ]);

    Route::get('download_file/{id}', [
        'uses' => 'FileController@downloadFile',
        'as' => 'download_file'
    ]);
    
    Route::get('truncate_files', [
        'uses' => 'FileController@truncateFiles',
        'as' => 'truncate_files'
    ]);
    
    Route::get('get_products_customers/{id}', [
        'uses' => 'Admin\CustomerController@getProductsCustomers',
        'as' => 'get_products_customers'
    ]);
    
    Route::post('save_merged_items', [
        'uses' => 'Admin\LabelsController@saveMergedItems',
        'as' => 'save_merged_items'
    ]);
    
    Route::post('update_cs_status/{id}', [
        'uses' => 'Admin\OrdersController@updateCsStatus',
        'as' => 'update_cs_status',
        'middleware' => 'permission:order_update'
    ]);

    Route::get('show-all-profit', [
        'uses' => 'Admin\ReportingController@showAllProfit',
        'as' => 'show_all_profit'
    ]);

    Route::get('product_brands_report', [
        'uses' => 'Admin\ReportingController@getSameProductsWithDiffBrand',
        'as' => 'product_brands_report',
        'middleware' => 'permission:report_products_brand_view'
    ]);

    Route::get('product_return_weekly_report', [
        'uses' => 'Admin\ReportingController@productReturnWeeklyReport',
        'as' => 'product_return_weekly_report'
    ]);

    Route::get('get_customer_brand_profit_details', [
        'uses' => 'Admin\ReportingController@getCustomerBrandProfitDetails',
        'as' => 'get_customer_brand_profit_details'
    ]);

    Route::post('create-merge-invoice', [
        'uses' => 'Admin\InvoicesController@createMergeInvoice',
        'as' => 'create_merge_invoice'
    ]);

    Route::post('merge-customer-all-invoices', [
        'uses' => 'Admin\InvoicesController@mergeCustomerAllInvoices',
        'as' => 'merge_customer_all_invoices'
    ]);

    Route::get('edit-merged-invoices/{id}', [
        'uses' => 'Admin\InvoicesController@editMergedInvoices',
        'as' => 'edit_merged_invoices',
        'middleware' => 'permission:invoices_view'
    ]);

    Route::post('update-merged-invoices/{id}', [
        'uses' => 'Admin\InvoicesController@updateMergedInvoices',
        'as' => 'update_merged_invoices',
        'middleware' => 'permission:invoices_view'
    ]);

    Route::get('view-merged-invoices', [
        'uses' => 'Admin\InvoicesController@viewMergedInvoices',
        'as' => 'view_merged_invoices',
        'middleware' => 'permission:invoices_view'
    ]);

    Route::post('/get-customer-invoice-total', function(Request $request) {
        $mergedInvoices = MergedInvoice::orderBy('created_at', 'DESC');
        if (!empty($request->customer)) {
            $mergedInvoices = $mergedInvoices->where('customer_id', $request->customer);
        }
        if (!empty($request->min_date)) {
            $mergedInvoices->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'));
            if (!empty($request->max_date)) {
                $mergedInvoices->whereDate('created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
            }
        }
        $mergedInvoices = $mergedInvoices->get();
        $totalCount = 0;
        $totalCost = 0;
        foreach ($mergedInvoices as $key => $minvoice) {
            if (isset($minvoice)) {
                $totalInvoices = explode(',', $minvoice->invoice_ids);
                $totalCount += count($totalInvoices);
                $totalCost += $minvoice->total_cost;
            }
        }
        return response()->json(['status' => true, 'total_qty' => $totalCount, 'total_cost' => $totalCost]);
    })->name('get_customer_total_invoices');

    Route::get('merged-invoice-detail/{id}', [
        'uses' => 'Admin\InvoicesController@mergedInvoiceDetail',
        'as' => 'merged_invoice_detail',
        'middleware' => 'permission:invoices_view'
    ]);

    Route::get('/view-return-orders', function () {
        $orderReturns = OrderReturn::with('order_return_details')->orderBy('created_at', 'DESC')->get()->toArray();
        $data = InvoicesMerged::get()->toArray();
        dd($orderReturns);
    });

    Route::get('/delete-return-orders', function () {
        $orderReturns = OrderReturn::with('order_return_details')->whereDate('created_at', '>=', '2022-10-13')->orderBy('created_at', 'DESC')->delete();
        $orderReturnDetails = OrderReturnDetail::whereDate('created_at', '>=', '2022-10-13')->delete();
    });

    Route::get('print-merged-invoice', [
        'uses' => 'Admin\InvoicesController@printMergedInvoice',
        'as' => 'print_merged_invoice'
    ]);

    Route::get('partial-payments/{id}', [
        'uses' => 'Admin\InvoicesController@partialPayments',
        'as' => 'partial_payments',
        'middleware' => 'permission:invoices_view'
    ]);

    Route::get('truncate-merged-invoices', [
        'uses' => 'Admin\InvoicesController@truncateMergedInvoices',
        'as' => 'truncate_merged_invoices',
    ]);

    Route::match(['GET', 'POST'], 'all-products-report', [
        'uses' => 'Admin\ReportingController@allProductsReport',
        'as' => 'all_products_report',
        'middleware' => 'permission:report_all_products_view'
    ]);

    Route::match(['GET', 'POST'], 'filter-product-report', [
        'uses' => 'Admin\ReportingController@filterProductReport',
        'as' => 'filter_product_report'
    ]);

    Route::post('send-email', [
        'uses' => 'Admin\InvoicesController@sendEmail',
        'as' => 'send_email'
    ]);

    Route::post('show-report-and-email', [
        'uses' => 'Admin\ReportingController@showReportAndMail',
        'as' => 'show_report_and_mail'
    ]);

    Route::get('agent-commission/{id}', [
        'uses' => 'Admin\AgentCommissionController@index',
        'as' => 'agent_commission'
    ]);
   
    Route::post('update-partial-payments', [
        'uses' => 'Admin\InvoicesController@updatePartialPayments',
        'as' => 'update_partial_payments'
    ]);

    Route::get('/get-product-history', [
        'uses' => 'Admin\ProductsController@getProductHistory',
        'as' => 'get_product_history'
    ]);

    Route::get('phpmyinfo', function () {
        phpinfo(); 
    })->name('phpmyinfo');

    Route::get('/brands-report', [
        'uses' => 'Admin\ReportingController@brandReporting',
        'as' => 'brands_report',
        'middleware' => 'permission:report_brands_view'
    ]);

    Route::post('/brand-reporting-total-orders', [
        'uses' => 'Admin\ReportingController@brandReportingTotalOrders',
        'as' => 'get_brand_total_orders',
        'middleware' => 'permission:report_brands_view'
    ]);
    
    Route::get('/invoice-view',function(){
        $detail = InvoicesMerged::with('merged_invoice.customer.service_charges', 'product.product_unit')->where('merged_invoice_id', 1)->groupBy('product_id')->where('product_qty', '>', '0')->get();
        $id = 1;
        $pdf = PDF::loadView('admin.invoices.emailtemplate', compact('detail', 'id'));
        return $pdf->stream('invoice.pdf');
    });

    Route::post('/get-filtered-profit', [
        'uses' => 'Admin\ReportingController@getFilteredProfit',
        'as' => 'get_filtered_profit'
    ]);

    Route::get('/inventory-history/{id}', [
        'uses' => 'Admin\InventoryController@inventoryHistory',
        'as' => 'inventory_history',
        'middleware' => 'permission:inventory_view'
    ]);

    Route::get('/inventory-history-report', [
        'uses' => 'Admin\ReportingController@inventoryHistoryReport',
        'as' => 'inventory_history_report',
        'middleware' => 'permission:inventory_view'
    ]);

    Route::post('/enable-dicounted-postage', [
        'uses' => 'Admin\CustomerController@enableDiscountedPostage',
        'as' => 'enable_discounted_postage'
    ]);
});
Auth::routes();
