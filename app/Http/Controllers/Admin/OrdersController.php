<?php

namespace App\Http\Controllers\Admin;

ini_set('memory_limit', '5120M');
use Session;
use Redirect;
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use App\Models\User;
use App\Models\Setting;
use App\Models\SkuOrder;
use App\AdminModels\Cities;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\AdminModels\States;
use App\Models\OrderReturn;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use App\Models\ProductNotes;
use Illuminate\Http\Request;
use App\AdminModels\Invoices;
use App\AdminModels\Products;
use App\AdminModels\Countries;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\CustomerLedger;
use App\Models\ServiceCharges;
use App\Models\CustomerProduct;
use App\Models\SkuOrderDetails;
use App\AdminModels\OrderDetails;
use App\Models\OrderReturnDetail;
use App\Models\ProductLabelOrder;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use App\Models\ProductOrderDetail;
use Illuminate\Support\Facades\DB;
use App\AdminModels\InvoiceDetails;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductLabel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\AdminModels\OrderShippingInfo;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class OrdersController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('auth');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $draw = $request->get('draw');
      $start = $request->get("start");
      $rowperpage = $request->get("length"); // Rows display per page
      $columnIndex_arr = $request->get('order');
      $columnName_arr = $request->get('columns');
      $order_arr = $request->get('order');
      $search_arr = $request->get('search');
      $columnIndex = $columnIndex_arr[0]['column']; // Column index
      $columnName = $columnName_arr[$columnIndex]['data']; // Column name
      $columnSortOrder = $order_arr[0]['dir']; // asc or desc
      $searchValue = $search_arr['value']; // Search value
      
      // Total records
      $totalRecords = Orders::with('Details', 'Customer', 'brand')
                      ->select('count(*) as allcount')
                      ->whereHas("Customer", function ($q){
                        $q->where('customers.deleted_at', '=', NULL);
                      })
                      ->whereHas("brand", function ($q){
                        $q->where('labels.deleted_at', '=', NULL);
                      })
                      ->orderBy('created_at', 'DESC')
                      ->count();
      
      $totalRecordswithFilter = Orders::with('Details', 'Customer', 'brand')
                                ->select('count(*) as allcount')
                                ->whereHas("Customer", function ($q){
                                  $q->where('customers.deleted_at', '=', NULL);
                                })
                                ->whereHas("brand", function ($q){
                                  $q->where('labels.deleted_at', '=', NULL);
                                })
                                ->orderBy('created_at', 'DESC');
      $query = Orders::query()->with('Details', 'Customer', 'brand');
      if (Auth::user()->hasRole('admin')) {
        // if (Auth::user()->can('order_view')) {
          $query = Orders::query()->with('Details', 'Customer', 'brand');
        // } 
      } else {
        if (Auth::user()->hasRole('customer')) {
          $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
          $customerId = Auth::user()->id;
          if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
          }
          $query = $query->where('customer_id', $customerId);
          $totalRecordswithFilter = $totalRecordswithFilter->where('customer_id', $customerId);
        }
      }
      $query->whereHas("Customer", function ($q){
        $q->where('customers.deleted_at', '=', NULL);
      });
      $query->whereHas("brand", function ($q){
        $q->where('labels.deleted_at', '=', NULL);
      });
      if (!empty($request->min_date)) {
        if ($request->max_date == null) {
          $request->merge(['max_date' => Carbon::now()->format('Y-m-d')]);
        }
        $query->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
        $totalRecordswithFilter = $totalRecordswithFilter->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($request->max_date)->format('Y-m-d'));
      }
      if ($request->order_status != null) {
        if ($request->order_status == 0) {
          $query->where('status', 'LIKE', "0");
          $totalRecordswithFilter = $totalRecordswithFilter->where('status', 'LIKE', "0");
        } else if ($request->order_status == null) {
        } else {
          $query->where('status', $request->order_status);
          $totalRecordswithFilter = $totalRecordswithFilter->where('status', $request->order_status);
        }
      }
      if ($request->customer != null) {
        $query->where('customer_id', $request->customer);
        $totalRecordswithFilter = $totalRecordswithFilter->where('customer_id', $request->customer);
      }
      if ($request->brand != null) {
        $query->where('brand_id', $request->brand);
        $totalRecordswithFilter = $totalRecordswithFilter->where('brand_id', $request->brand);
      }
      // SORTING
      if (!empty($columnName)) {
        if ($columnName == 'customer_name') {
          $query = $query->whereHas('Customer', function ($q) use ($columnSortOrder) {
            $q->orderBy('customers.customer_name', $columnSortOrder)->withTrashed();
          });
        } else if ($columnName == 'brand_name') {
          $query = $query->whereHas('orders', function ($q) use ($columnSortOrder) {
            $q->whereHas('brand', function($r) use ($columnSortOrder) {
              $r->orderBy('brand', $columnSortOrder);
            });
          });
        } else {
          $query = $query->orderBy($columnName, $columnSortOrder);
        }
      }
      $query = $query->select(['orders.*'])
      ->skip($start)
      ->take($rowperpage);
      $data = $query->orderBy('created_at', 'DESC')->get();
      
      $data_arr = [];
      $sno = $start+1;
      foreach($data as $record){
        $id = $record->id;
        $date = $record->created_at;
        $brand = Labels::where('id', $record->brand_id)->first();
        if (isset($brand)) {
          $brand_name = $brand->brand;
        } else {
          $brand_name = '';
        }
        $customer_name = isset($record->Customer) ? $record->Customer->customer_name : '';
        $brandName = $brand_name;
        $invoiceNumber = $record->customer_name.'-invoice-no-'.$record->inv_no;
        $status = '';
        $checkBox = '';
        if ($record->status == 4) {
          $checkBox .= '<input type="checkbox" disabled />';
        } else {
          $checkBox .= '<input type="checkbox" data-singleorder-id="' . $record->id . '" class="singleOrderCheck" />';
        }
        $btn = '';
        $btn = '<div class="dropdown">
          <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              <i data-feather="more-vertical"></i>
          </button>
          <div class="dropdown-menu">';
          if ($record->status != 4) {
            $btn .= '
              <a class="dropdown-item enter-pincode reduce-inventory set-data" href="/invoice/order/details/' . $record->id . '" target="_blank">
                                    <i data-feather="eye"></i>
                                    <span>View Details</span>
                                </a>';
            if (Auth::user()->can('order_create')) {
              $btn .= '
                <a class="dropdown-item enter-pincode reduce-inventory" href="/orders/' . $record->id . '/edit" target="_blank">
                                      <i data-feather="edit"></i>
                                      <span>Edit Order</span>
                                  </a>';
            }
          } else {
            $btn .= '
              <a class="dropdown-item enter-pincode reduce-inventory set-data" href="/invoice/order/details/' . $record->id . '" target="_blank">
                                    <i data-feather="eye"></i>
                                    <span>View Details</span>
                                </a>';
            $btn .= '
              <a class="dropdown-item delete_order reduce-inventory" data-order-id="'.$record->id.'">
                                    <i data-feather="trash-2"></i>
                                    <span>Delete</span>
                                </a>';
          }
        $btn .= '</div></div>';
        $invoiceNumberData = '';
        if ($record->order_status == 4) {
          $invoiceNumberData .= '<a href="#" class="text-center py-2">'.$customer_name.'-invoice-no-'.$record->order_id.'</a>';
        } else {
          $invoiceNumberData .= '<a href="#" class="text-center py-2" onclick="window.open(`invoice/order/details/'.$record->order_id.'`, ``, `width=1300, height=700`)";>'.$customer_name.'-invoice-no-'.$record->order_id.'</a>';
        }
        
        $totalQty = 0;
        if ($record->status != 4) {
          if (isset($record->Details)) {
            $totalQty = $record->mailerqty;
          } else {
            $totalQty = 0;
          }
        }
        $totalCost = 0.00;
        if ($record->status != 4) {
          $totalCost = $record->total_cost;
        }
        $statusChangeBtn = '';
        if (Auth::user()->can('order_create')) {
          if ($record->status == 4) {
            $statusChangeBtn .= '<p>Cancelled</p>';
          } else {
            $statusChangeBtn .= '<select name="status_id" data-order-id="' . $record->id . '" id="order_status_id" class="form-control-sm order_status_id" >
                        <option ' . ($record->status == 0 ? "selected" : "") . ' value="0">New Batch</option>
                        <option ' . ($record->status == 1 ? "selected" : "") . ' value="1">In Process</option>
                        <option ' . ($record->status == 2 ? "selected" : "") . ' value="2">Shipped</option>
                        <option ' . ($record->status == 3 ? "selected" : "") . ' value="3">Delivered</option>
                        <option ' . ($record->status == 4 ? "selected" : "") . ' value="4">Cancelled</option>
                      </select>';
          }
        } else {
          $statusChangeBtn .= '<select name="status_id" data-order-id="' . $record->id . '" id="order_status_id" class="form-control-sm" >';
          $opt = '';
          if ($record->status == 0) {
            $opt = 'New Batch';
          } else if ($record->status == 1) {
            $opt = 'In Process';
          } else if ($record->status == 2) {
            $opt = 'Shipped';
          } else if ($record->status == 3) {
            $opt = 'Delivered';
          } else if ($record->status == 4) {
            $opt = 'Cancelled';
          }
          $statusChangeBtn .= '<option selected value="0">' . $opt . '</option>';
          $statusChangeBtn .= '</select>';
        }
        $totalMailerQty = 0;
        if ($record->status != 4) {
          $totalMailerQty = $record->mailerqty;
        } else {
          $totalMailerQty = 0;
        }
        $userName = $record->user_name;
        $createdAt = date("m/d/Y H:i:s", strtotime($record->created_at));
        $titleData = '<div class="popup">
                        '.$createdAt.'
                        <span class="popuptext d-none">Created by: '.$userName.'</span>
                      </div>';
        $data_arr[] = array(
          'id' => $id,
          'user_name' => $record->user_name,
          'order_checkbox' => $checkBox,
          'created_at' => $titleData,
          'customer_name' => $customer_name,
          'brand_name' => $brandName,
          'qty' => $totalQty,
          'mailerqty' => $totalMailerQty,
          'notes' => $record->notes,
          'total_cost' => $totalCost,
          'status' => $statusChangeBtn,
          'action' => $btn,
          'rowClass' => $status
        );
      }
      return Datatables::of($data_arr)
        ->addIndexColumn()
        ->addColumn('order_checkbox', function ($row) {
          return $row['order_checkbox'];
        })
        ->addColumn('created_at', function ($row) {
          return $row['created_at'];
        })
        ->addColumn('customer_name', function ($row) {
          return $row['customer_name'];
        })
        ->addColumn('brand_name', function ($row) {
          return $row['brand_name'];
        })
        ->addColumn('mailerqty', function ($row) {
          return $row['mailerqty'];
        })
        ->addColumn('notes', function ($row) {
          return $row['notes'];
        })
        ->addColumn('total_cost', function ($row) {
          return $row['total_cost'];
        })
        ->addColumn('status', function ($row) {
          return $row['status'];
        })
        ->addColumn('action', function ($row) {
          return $row['action'];
        })
        ->with([
          "draw" => intval($draw),
          'recordsTotal' => $totalRecords,
          'recordsFiltered' => $totalRecordswithFilter->count(),
          "data" => $data_arr
        ])
        ->rawColumns(['created_at', 'order_number', 'action', 'order_checkbox', 'name', 'status_change', 'service_charges'])
        ->make(true);
    }
    $customers = Customers::where('is_active', 1);
    if (Auth::user()->can('customer_view')) {
      if (!Auth::user()->hasRole('customer')) {
        $customers = $customers->get();
      }
      if (Auth::user()->hasRole('customer')) {
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $customers = $customers->where('id', $customerId)->get();
      }
    } else {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $customers = $customers->where('id', $customerId)->get();
    }
    return view('admin.orders.orders', compact('customers'));
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $exitCode = Artisan::call('cache:clear');
    $customers = Customers::all(['id', 'customer_name']);
    return view('admin.orders.add_order', compact('customers'));
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'customer'     => 'required',
      "sku"          => "required",
      "qty"          => "required",
    ]);
    DB::beginTransaction();
    try {
      $customerServiceCharges = ServiceCharges::where('customer_id', $request->customer)->first();
      $totalCalculatedLabelQty = $request->labelqty;
      $totalCalculatedPickPackFlatQty = $request->pickpackflatqty;
      $totalEachPostageValue = [];
      array_push($totalEachPostageValue, ['slug' => 'one_to_four_ounces', 'name' => 'one to four', 'price' => $request->one_to_four_ounces]);
      array_push($totalEachPostageValue, ['slug' => 'five_to_eight_ounces', 'name' => 'five to eight', 'price' => $request->five_to_eight_ounces]);
      array_push($totalEachPostageValue, ['slug' => 'nine_to_twelve_ounces', 'name' => 'nine to twelve', 'price' => $request->nine_to_twelve_ounces]);
      array_push($totalEachPostageValue, ['slug' => 'thirteen_to_fifteen_ounces', 'name' => 'thirteen to fifteen', 'price' => $request->thirteen_to_fifteen_ounces]);
      array_push($totalEachPostageValue, ['slug' => 'one_lbs', 'name' => 'one lbs', 'price' => $request->one_lbs]);
      array_push($totalEachPostageValue, ['slug' => 'one_to_two_lbs', 'name' => 'one to two lbs', 'price' => $request->one_to_two_lbs]);
      array_push($totalEachPostageValue, ['slug' => 'two_to_three_lbs', 'name' => 'two to three', 'price' => $request->two_to_three_lbs]);
      array_push($totalEachPostageValue, ['slug' => 'three_to_four_lbs', 'name' => 'three to four', 'price' => $request->three_to_four_lbs]);
      $totalEachDiscountedPostageValue = [];
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_to_four_ounces', 'name' => 'discounted one to four', 'price' => $request->discounted_one_to_four_ounces]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_five_to_eight_ounces', 'name' => 'discounted five to eight', 'price' => $request->discounted_five_to_eight_ounces]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_nine_to_twelve_ounces', 'name' => 'discounted nine to twelve', 'price' => $request->discounted_nine_to_twelve_ounces]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_thirteen_to_fifteen_ounces', 'name' => 'discounted thirteen to fifteen', 'price' => $request->discounted_thirteen_to_fifteen_ounces]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_lbs', 'name' => 'discounted one lbs', 'price' => $request->discounted_one_lbs]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_one_to_two_lbs', 'name' => 'discounted one to two lbs', 'price' => $request->discounted_one_to_two_lbs]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_two_to_three_lbs', 'name' => 'discounted two to three', 'price' => $request->discounted_two_to_three_lbs]);
      array_push($totalEachDiscountedPostageValue, ['slug' => 'discounted_three_to_four_lbs', 'name' => 'discounted three to four', 'price' => $request->discounted_three_to_four_lbs]);
      $order = Orders::create([
        'customer_id' => $request->input('customer'),
        'brand_id' => $request->input('brand'),
        'total_cost' => $request->input('grand_total_price'),
        'freight_cost' => '0',
        'duty_fee' => '0',
        'selling_price' => '0',
        'margin' => '0',
        'status' => '0',
        'labelqty' => $totalCalculatedLabelQty,
        'pickqty' => $request->pickqty,
        'packqty' => $request->packqty,
        'mailerqty' => $request->mailerqty,
        'postageqty' => $request->postageqty,
        'pick_pack_flat_qty' => $totalCalculatedPickPackFlatQty,
        'pick_pack_flat_price' => $request->grandTotalPickPackFlatPrice,
        'customer_service_charges' => json_encode($customerServiceCharges),
        'discounted_postage_status' => $request->discounted_postage_status,
        'discounted_charges' => json_encode($totalEachDiscountedPostageValue),
        'all_postage_charges' => json_encode($totalEachPostageValue),
        'user_id' => Auth::user()->id,
        'user_name' => Auth::user()->name,
        'notes' => $request->notes,
      ]);
      $customerBrand = Labels::where('id', $request->brand)->first();
      if (isset($customerBrand)) {
        $orderServiceCharges = json_decode($order->customer_service_charges);
        $customerBrandMailerCharges = $customerBrand->mailer_cost;
        if ($customerBrandMailerCharges != 0) {
          $customerBrandMailerCharges = $customerBrand->mailer_cost;
        } else {
          $customerBrandMailerCharges = $customerServiceCharges->mailer;
        }
        $orderServiceCharges->mailer = $customerBrandMailerCharges;
        $order->customer_service_charges = json_encode($orderServiceCharges);
        $order->save();
      }
      $qtys = $request->qty;
      $labelsQtty = 0;
      foreach ($request->sku as $key => $value) {
        $qty = $qtys[$key];
        if ($qty > 0) {
          $skuProduct = SkuProducts::where('sku_id', $request->sku[$key])->get();
          foreach ($skuProduct as $skey => $skuvalue) {
            $custhasProd = CustomerHasProduct::where('customer_id', $request->customer)->where('brand_id', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->where('is_active', 0)->first();
            if (isset($custhasProd)) {
              $mergedBrandProduct = MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id);
              $selectedBrandProduct = MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id);
              if ($mergedBrandProduct->exists()) {
                $mergedQty = $mergedBrandProduct->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty - $qty;
                  $mergedQty->merged_qty = $res;
                  $mergedQty->save();
                  $checkIfCustHasProduct = CustomerHasProduct::where('customer_id', $mergedQty->customer_id)->where('brand_id', $mergedQty->selected_brand)->where('product_id', $mergedQty->product_id)->where('is_active', 0)->first();
                  if (isset($checkIfCustHasProduct)) {
                    $checkIfCustHasProduct->label_qty = $mergedQty->merged_qty;
                    $checkIfCustHasProduct->save();
                  }
                }
              } else if ($selectedBrandProduct->exists()) {
                $mergedQty = $selectedBrandProduct->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty - $qty;
                  $mergedQty->merged_qty = $res;
                  $mergedQty->save();
                  $custhasProd->label_qty = $res;
                }
              } else {
                $res = $custhasProd->label_qty - $qty;
                $custhasProd->label_qty = $res;
              }
              if ($custhasProd->is_active == 0) {
                ProductLabelOrder::create([
                  'customer_id' => $request->customer,
                  'brand_id' => $request->brand,
                  'product_id' => $skuProduct[$skey]->product_id,
                  'label_deduction' => $qty,
                  'order_id' => $order->id,
                  'sku_id' => $skuProduct[$skey]->sku_id
                ]);
              }
              $custhasProd->save();
              if ($custhasProd->seller_cost_status == 1) {
                if ($customerServiceCharges->labels > 0) {
                  $labelsQtty += $qty;
                }
              }
            }
            $custProd = CustomerProduct::where('customer_id', $request->customer)->where('product_id', $skuProduct[$skey]->product_id)->where('is_active', 0)->first();
            if (isset($custProd)) {
              $res = $custProd->label_qty - $qty;
              $custProd->label_qty = $res;
              $custProd->save();
              if ($qty > 0) {
                $productOrderDetails = ProductOrderDetail::create([
                  'order_id' => $order->id,
                  'sku_id' => $request->sku[$key],
                  'product_id' => $skuProduct[$skey]->product_id,
                  'seller_cost_status' => $custProd->seller_cost_status
                ]);
              }
            }
          }
        }
      }
      if ($labelsQtty == $totalCalculatedLabelQty) {
        $order->labelqty = $labelsQtty;
        $order->save();
      } else {
        return redirect()->back()->withError('Check labels quantity');
      }
      $customer_Name = '';
      $customerData = Customers::where('id', $request->input('customer'))->first();
      if (isset($customerData)) {
        $customer_Name = $customerData->customer_name;
      }
      ////////////////adding invoce data/////////////////
      $invoice = Invoices::create([
        'order_id' => $order->id,
        'customer_invoice_number' => $customer_Name.'_'.$order->id,
        'subtotal' => null,
        'tax' => null,
        'grand_total' => isset($order) ? $order->total_cost : 0.00,
        'customer_id' => $request->input('customer'),
        'is_paid' => '0',
        'status' => '0',
      ]);
      ////////////////adding orders details data/////////////////
      foreach ($request->sku as $sku_key => $id) {
        $sku = Sku::with('sku_product', 'brand')->where('id', $id)->first();
        $mailer_cost = 0;
        $brandMailerCost = Labels::where('id', $request->brand)->where('customer_id', $request->customer)->first();
        $customerServiceCharges = ServiceCharges::where('customer_id', $request->customer)->first();
        $setting = Setting::first();
        if (isset($brandMailerCost)) {
          $mailer_cost = $brandMailerCost->mailer_cost;
          if ($mailer_cost == 0) {
            if (isset($customerServiceCharges)) {
              $mailer_cost = $customerServiceCharges->mailer;
              if ($mailer_cost == 0) {
                if (isset($setting)) {
                  $mailer_cost = $mailer_cost;
                }
              }
            }
          }
        }
        $service_charges_details = [];
        array_push($service_charges_details, ['slug' => 'labels_price', 'name' => 'Labels Price', 'price' => $request->_total_label_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'pick_price', 'name' => 'Pick Price', 'price' => $request->_total_pick_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'pack_price', 'name' => 'Pack Price', 'price' => $request->_total_pack_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'mailer_price', 'name' => 'Mailer Price', 'price' => $request->mailer_costNew]);
        array_push($service_charges_details, ['slug' => 'postage_price', 'name' => 'Postage Price', 'price' => $request->total_postage_price]);
        // if ($request->qty[$sku_key] > 0) {
          $orderDetail = OrderDetails::create([
            'sku_id' => $id,
            'order_id' => $order->id,
            'qty' => $request->qty[$sku_key],
            'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
            'sku_purchasing_cost' => $sku->purchasing_cost,
            'sku_selling_cost' => $request->sku_selling_cost[$sku_key],
            'service_charges' => $request->_total_service_charges[$sku_key],
            'service_charges_detail' => json_encode($service_charges_details),
          ]);
          $invoiceDetail = InvoiceDetails::create([
            'invoice_id' => $invoice->id,
            'sku_id' => $id,
            'qty' => $request->qty[$sku_key],
            'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
            'service_charges' => $request->_total_service_charges[$sku_key],
            'service_charges_detail' => json_encode($service_charges_details),
          ]);
          $skuOrder = SkuOrder::create([
            'order_id' => $order->id,
            'customer_id' => $request->customer,
            'sku_id' => $sku->id,
            'sku_id_name' => $sku->sku_id,
            'name' => $sku->name,
            'weight' => $sku->weight,
            'brand_id' => $sku->brand_id,
            'purchasing_cost' => $sku->purchasing_cost,
            'selling_cost' => $sku->selling_cost,
            'grand_total_amount' => $sku->grand_total_amount,
            'pick_pack_flat_status' => $sku->pick_pack_flat_status,
            'service_charges' => $sku->service_charges,
            'service_charges_detail' => $sku->service_charges_detail,
            'mailer_cost' => $mailer_cost,
            'date' => Carbon::now()->format('Y-m-d')
          ]);
          // update inventory and labels
          foreach ($sku->sku_product as $prod_key => $sku_product) {
            $sku_product = $sku_product->load('product');
            $inventory = Inventory::where('product_id', $sku_product->product_id)->first();
            $ifProductInventoryHistoryExists = InventoryHistory::where('product_id', $sku_product->product_id)
              ->whereDate('created_at', Carbon::now())
              ->orderBy('created_at', 'DESC');
            if (isset($inventory)) {
              $inventory->qty = $inventory->qty - ($sku_product->quantity * $request->qty[$sku_key]);
              $inventory->save();
            }
            if ($request->qty[$sku_key] > 0) {
              InventoryHistory::create([
                'product_id' => $sku_product->product_id,
                'qty' => -($request->qty[$sku_key]),
                'sales' => $request->qty[$sku_key],
                'total' => $inventory->qty,
                'order_id' => $order->id,
                'sku_id' => $sku_product->sku_id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
              ]);
            }
            $custhasProd = CustomerHasProduct::where('customer_id', $request->customer)->where('brand_id', $request->brand)->where('product_id', $sku_product->product_id)->first();
            $isActive = 1;
            $customerServiceCharges = ServiceCharges::where('customer_id', $request->customer)->first();
            $customerProductLabelCharges = $customerServiceCharges->labels;
            if (isset($custhasProd)) {
              $isActive = $custhasProd->is_active;
              $customerProductLabelCharges = $custhasProd->label_cost;
              if ($customerProductLabelCharges == 0) {
                $customerProductLabelCharges = $customerServiceCharges->labels;
              }
            }
            $custProd = CustomerProduct::where('customer_id', $request->customer)->where('product_id', $sku_product->product_id)->first();
            $sellerCostStatus = 0;
            if (isset($custProd)) {
              $sellerCostStatus = $custProd->seller_cost_status;
            }
            $skuOrderDetails = SkuOrderDetails::create([
              'sku_id' => $skuOrder->sku_id,
              'sku_product_id' => $sku_product->id,
              'order_id' => $skuOrder->order_id,
              'sku_order_id' => $skuOrder->id,
              'customer_id' => $skuOrder->customer_id,
              'brand_id' => $skuOrder->brand_id,
              'product_id' => $sku_product->product_id,
              'quantity' => $sku_product->quantity,
              'purchasing_cost' => $sku_product->purchasing_cost,
              'selling_cost' => $sku_product->selling_cost,
              'label' => $customerProductLabelCharges,
              'pick' => (!is_null($sku_product->pick)) ? $sku_product->pick : '0.00',
              'pack' => (!is_null($sku_product->pack)) ? $sku_product->pack : '0.00',
              'pick_pack_flat_status' => (!is_null($sku_product->pick_pack_flat_status)) ? $sku_product->pick_pack_flat_status : '0',
              'is_active' => $isActive,
              'seller_cost_status' => $sellerCostStatus,
              'date' => Carbon::now()->format('Y-m-d')
            ]);
          }
          $sku->brand->qty = $sku->brand->qty - $request->qty[$sku_key];
        // }
      }
      DB::commit();
      return redirect('/orders')->withSuccess('Batch added successfully');
    } catch (\Exception $e) {
      dd($e);
      DB::rollback();
      return view('admin.server_error');
      return redirect()->back()->withError('Something went wrong');
    }
  }
  public function getBrands(Request $request)
  {
    $id = $request->input('customer_id');
    $data['brands'] = Labels::select('*')->where('customer_id', $id)->get();
    return view('admin.partials.brands')->with($data);
  }
  public function getStates(Request $request)
  {
    $id = $request->input('country_id');
    $data['states'] = States::select('*')->where('country_id', $id)->get();
    return view('admin.partials.states')->with($data);
  }
  public function updateOrderStatus(Request $request)
  {
    // keto -> 148356, forskolin -> 4875
    DB::beginTransaction();
    try {
      $order_id = $request->input('order_id');
      $status_id = $request->input('status_id');
      Orders::where('id', $order_id)->update(array('status' => $status_id));
      if ($status_id == 4) {
        $order = Orders::where('id', $order_id)->select(['customer_id', 'brand_id'])->first();
        $orderDetailData = OrderDetails::with('skuproduct')->select('*')->where('order_id', $order_id)->get();
        foreach ($orderDetailData as $value) {
          if (isset($value)) {
            foreach ($value->skuproduct as $skukey => $sku_Product) {
              if (isset($sku_Product)) {
                $newQty = 0;
                $productInfo = Inventory::select('*')->where('product_id', $sku_Product->product_id)->first();
                if (isset($productInfo)) {
                  $newQty = $value->qty + $productInfo->qty;
                }
                if ($value->qty > 0) {
                  Inventory::where('product_id', $sku_Product->product_id)->update(array('qty' => $newQty));
                  $inven = Inventory::select('*')->where('product_id', $sku_Product->product_id)->first();
                  InventoryHistory::create([
                    'qty' => $value->qty,
                    'cancel_order_add' => $value->qty,
                    'order_id' => $order_id,
                    'sku_id' => $sku_Product->sku_id,
                    'total' => $inven->qty,
                    'product_id' => $sku_Product->product_id,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                  ]);
                  $customerHasProducts = CustomerHasProduct::where('customer_id', $order->customer_id)->where('brand_id', $order->brand_id)->where('product_id', $sku_Product->product_id)->first();
                  $customerProducts = CustomerProduct::where('customer_id', $order->customer_id)->where('product_id', $sku_Product->product_id)->first();
                  if (isset($customerHasProducts)) {
                    $res = $customerHasProducts->label_qty + $value->qty;
                    $customerHasProducts->label_qty = $res;
                    $customerHasProducts->save();
                  }
                  if (isset($customerProducts)) {
                    $res2 = $customerProducts->label_qty + $value->qty;
                    $customerProducts->label_qty = $res2;
                    $customerProducts->save();
                  }
                  CustomerProductLabel::create([
                    'user_id' => 1,
                    'customer_id' => $order->customer_id,
                    'product_id' => $sku_Product->product_id,
                    'brand_id' => $order->brand_id,
                    'label_qty' => $value->qty
                  ]);
                }
              }
            }
            $value->status = $order->status;
            $value->save();
          }
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      dd($e);
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function getCities(Request $request)
  {
    $id = $request->input('state_id');
    $data['cities'] = Cities::select('*')->where('state_id', $id)->get();
    return view('admin.partials.cities')->with($data);
  }
  public function orderDetail($orderId)
  {
    $data['orderMainData'] = Orders::where('id', $orderId)->first();
    $data['customerData'] = Customers::where('id', $data['orderMainData']->customer_id)->first();
    $data['labelsData'] = Labels::where('customer_id', $data['orderMainData']->customer_id)->first();
    $data['orderDetailData'] = OrderDetails::where('order_id', $orderId)->with("Product", 'sku_detail')->get();
    $data['orderDetailsData'] = OrderDetails::where('order_id', $orderId)->get();
    return view('admin.orders.orderDetails')->with($data);
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
  public function edit($orderId)
  {
    try {
      $order = Orders::where('id', $orderId)->where('status', '!=', 4)->first();
      // if ($order->status == 4 || $order->merged == 1) {
      //   return redirect('/orders')->with('error', 'Batch is Cancelled OR Merged');
      // }
      $customer_id = '';
      if (isset($order)) {
        $customer_id = $order->customer_id;
      }
      $data['customer'] = Customers::with('service_charges')->where('id', $customer_id)->first();
      $data['brand'] = Labels::where('id', $order->brand_id)->first();
      $data['order_details'] = $order->Details;
      $orderInvoiceId = Invoices::where('order_id', $orderId)->first();
      $data['invoiceMainData'] = Invoices::where('invoice_number', $orderInvoiceId->invoice_number)->first();
      // $orderId = $data['invoiceMainData']->order_id;
      $data['orderMainData'] = Orders::where('id', $orderId)->where('status', '!=', 4)->first();
      $data['customerData'] = Customers::where('id', $data['orderMainData']->customer_id)->first();
      $data['labelsData'] = Labels::where('customer_id', $data['orderMainData']->customer_id)->first();
      $query = OrderDetails::with('sku_order_detail')->where('order_id', $orderId);
      $data['orderDetailData'] = $query->get();
      $orders = OrderDetails::where('order_id', $orderId);
      $orders = $orders->get();
      $skuproducts = array();
      foreach ($orders as $order) {
        if (isset($order)) {
          $sku_products = SkuOrderDetails::where('order_id', $order->order_id)->where('sku_id', $order->sku_id)->get();
          foreach ($sku_products as $skuproduct) {
            $product = Products::where('id', $skuproduct->product_id)->first();
            array_push($skuproducts, $skuproduct->product_id);
          }
        }
      }
      $unique = array_unique($skuproducts);
      $products = array();
      $keys = array('prod_id', 'product_name', 'product_occ', 'product_price');
      foreach ($unique as $u) {
        $product_data = Products::where('id', $u)->first();
        if (isset($product_data)) {
          $qty = 0;
          $price = 0;
          foreach ($orders as $order) {
            $quantity = array();
            $sellerCostStatus = 0;
              if (isset($order)) {
                $sku_products = SkuOrderDetails::where('order_id', $order->order_id)->where('sku_id', $order->sku_id)->get();
                foreach ($sku_products as $skuproduct) {
                  if ($skuproduct->product_id == $u) {
                    if ($skuproduct->seller_cost_status == 1) {
                      if ($order->qty > 0) {
                        $qty = $qty + $order->qty;
                        $price = $skuproduct->selling_cost;
                        $sellerCostStatus = $skuproduct->seller_cost_status;
                      }
                    } else {
                      $qty = $qty + $order->qty;
                      $price = 0;
                      $sellerCostStatus = $skuproduct->seller_cost_status;
                    }
                  }
                }
              }
              array_push($quantity, $qty, $price * $qty, $sellerCostStatus);
          }
          array_push($products, array_combine($keys, [$product_data->id, $product_data->name, $quantity, $product_data->price]));
        }
      }
      if (env('APP_ENV') == 'production') {
        return view('admin.invoices.edit_batch', compact('orderId', 'order', 'products'))->with($data);
      } else {
        return view('admin.invoices.edit_batch', compact('orderId', 'order', 'products'))->with($data);
      }
        // return view('admin.invoices.edit_batch', compact('orderId', 'order', 'products'))->with($data);
    } catch (\Exception $e) {
      dd($e);
      return view('admin.server_error');
      return redirect()->back()->with('error', 'Something went wrong');
    }
  }
  // Edit Order Details
  public function editOrderDetails(Request $request)
  {
    $orderDetail = OrderDetails::with('sku_order.sku_product', 'sku_order_detail.product', 'sku_order_detail.labelqty')->where('order_id', $request->order_id);
    $orderDetail = $orderDetail->get();
    return response()->json($orderDetail);
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateOrder($request, $orderId)
  {
    $order = Orders::where('id', $orderId)->where('status', '!=', 4)->where('customer_id', $request->customer)->where('brand_id', $request->brand)->update([
      'customer_id' => $request->input('customer'),
      'brand_id' => $request->input('brand'),
      'total_cost' => $request->grand_total_price,
      'freight_cost' => '0',
      'duty_fee' => '0',
      'selling_price' => '0',
      'margin' => '0',
      'status' => '0',
      'labelqty' => $request->labelqty,
      'pickqty' => $request->pickqty,
      'packqty' => $request->packqty,
      'mailerqty' => $request->mailerqty,
      'postageqty' => $request->postageqty,
      'pick_pack_flat_qty' => $request->pickpackflatqty,
      'pick_pack_flat_price' => $request->grandTotalPickPackFlatPrice,
      'notes' => $request->notes
    ]);
    return $order;
  }
  public function updateOrderDetails($request, $id, $orderId, $sku, $sku_key)
  {
    $orderDetail = OrderDetails::where('sku_id', $id)->where('order_id', $orderId)->update([
      'sku_total_cost' => $sku->selling_cost * $request->qty[$sku_key],
      'sku_id' => $id,
      'qty' => $request->qty[$sku_key],
      'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
      'sku_purchasing_cost' => $sku->purchasing_cost,
      'sku_selling_cost' => $request->sku_selling_cost[$sku_key],
      'service_charges_detail' => json_encode($this->updateServiceChargesDetails($request, $sku, $sku_key, $orderId)),
    ]);
  }
  public function updateServiceChargesDetails($request, $sku, $sku_key, $orderId)
  {
    $qtty = $request->qty;
    $totalLabelCharges = 0.00;
    $totalPickCharges = 0.00;
    $totalPackCharges = 0.00;
    $skuProducts = SkuOrderDetails::where('sku_order_id', $sku->id)->where('order_id', $orderId)->get();
    foreach ($skuProducts as $skuProductKey => $skuProduct)
    {
      if ($qtty[$sku_key] > 0)
      {
        $totalLabelCharges += ($request->labelqty > 0) ? $request->labels_cost * $qtty[$sku_key] : '0.00';
        if ($skuProduct->pick > 0)
        {
          $totalPickCharges += ($request->pickqty > 0) ? $request->pick_cost * $qtty[$sku_key] : '0.00';
        }
        if ($skuProduct->pack > 0)
        {
          $totalPackCharges += ($request->packqty > 0) ? $request->pack_cost * $qtty[$sku_key] : '0.00';
        }
      }
    }
    $totalMailerCharges = $request->mailerqty * $request->mailer_cost;
    $totalPostageCharges = $request->postage_cost;
    $service_charges_details = [];
    if ($request->labelqty > 0) {
      array_push($service_charges_details, ['slug' => 'labels_price', 'name' => 'Labels Price', 'price' => $totalLabelCharges]);
    } else {
      array_push($service_charges_details, ['slug' => 'labels_price', 'name' => 'Labels Price', 'price' => 0.00]);
    }
    if ($request->pickqty > 0) {
      array_push($service_charges_details, ['slug' => 'pick_price', 'name' => 'Pick Price', 'price' => $totalPickCharges]);
    } else {
      array_push($service_charges_details, ['slug' => 'pick_price', 'name' => 'Pick Price', 'price' => 0.00]);
    }
    if ($request->packqty > 0) {
      array_push($service_charges_details, ['slug' => 'pack_price', 'name' => 'Pack Price', 'price' => $totalPackCharges]);
    } else {
      array_push($service_charges_details, ['slug' => 'pack_price', 'name' => 'Pack Price', 'price' => 0.00]);
    }
    if ($request->mailerqty > 0) {
      array_push($service_charges_details, ['slug' => 'mailer_price', 'name' => 'Mailer Price', 'price' => $totalMailerCharges]);
    } else {
      array_push($service_charges_details, ['slug' => 'mailer_price', 'name' => 'Mailer Price', 'price' => 0.00]);
    }
    if ($request->postageqty > 0) {
      array_push($service_charges_details, ['slug' => 'postage_price', 'name' => 'Postage Price', 'price' => $totalPostageCharges]);
    } else {
      array_push($service_charges_details, ['slug' => 'postage_price', 'name' => 'Postage Price', 'price' => 0.00]);
    }    
    return $service_charges_details;
  }
  public function updateInvoiceDetails($request, $invoiceData, $id, $sku_key, $sku, $orderId)
  {
    $invoiceDetail = InvoiceDetails::where('invoice_id', $invoiceData->id)->where('sku_id', $id)->update([
      'invoice_id' => $invoiceData->id,
      'sku_id' => $id,
      'qty' => $request->qty[$sku_key],
      'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
      'service_charges' => '0.00',
      'service_charges_detail' => json_encode($this->updateServiceChargesDetails($request, $sku, $sku_key, $orderId)),
    ]);
  }
  public function updateSkuProductDetails($request, $sku, $sku_key, $orderDetails, $orderId)
  {
    foreach ($sku->sku_product as $prod_key => $sku_product) {
      $sku_product = $sku_product->load('product');
      $inventory = Inventory::where('product_id', $sku_product->product_id)->first();
      $ifProductInventoryHistoryExists = InventoryHistory::where('product_id', $sku_product->product_id)
        ->whereDate('created_at', Carbon::now())
        ->orderBy('id', 'DESC')
        ->orderBy('created_at', 'DESC');
      if (isset($inventory)) {
        if ($request->qty[$sku_key] == $orderDetails->qty) {
          //
        } else if ($request->qty[$sku_key] > $orderDetails->qty) {
          $difference = $request->qty[$sku_key] - $orderDetails->qty;
          $inventory->qty = $inventory->qty - ($sku_product->quantity * $difference);
          $inventory->save();
              InventoryHistory::create([
                'product_id' => $sku_product->product_id,
                'qty' => $difference,
                'sales' => $difference,
                'total' => $inventory->qty,
                'order_id' => $orderId,
                'sku_id' => $sku_product->sku_id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
              ]);
              $customerHasProducts = CustomerHasProduct::where('customer_id', $request->input('customer'))->where('brand_id', $request->input('brand'))->where('product_id', $sku_product->product_id)->first();
              $customerProducts = CustomerProduct::where('customer_id', $request->input('customer'))->where('product_id', $sku_product->product_id)->first();
              if (isset($customerHasProducts)) {
                $res = $customerHasProducts->label_qty - $difference;
                $customerHasProducts->label_qty = $res;
                $customerHasProducts->save();
              }
              if (isset($customerProducts)) {
                $res2 = $customerProducts->label_qty - $difference;
                $customerProducts->label_qty = $res2;
                $customerProducts->save();
              }
              CustomerProductLabel::create([
                'user_id' => 1,
                'customer_id' => $request->input('customer'),
                'product_id' => $sku_product->product_id,
                'brand_id' => $request->input('brand'),
                'label_qty' => -$difference
              ]);
          // }
        } else if ($request->qty[$sku_key] < $orderDetails->qty) {
          $difference = $orderDetails->qty - $request->qty[$sku_key];
          $inventory->qty = $inventory->qty + ($sku_product->quantity * $difference);
          $inventory->save();
          InventoryHistory::create([
            'product_id' => $sku_product->product_id,
            'qty' => $difference,
            'sales' => 0,
            'total' => $inventory->qty,
            'order_id' => $orderId,
            'sku_id' => $sku_product->sku_id,
            'edit_batch_qty' => $difference,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
          ]);
          $customerHasProducts = CustomerHasProduct::where('customer_id', $request->input('customer'))->where('brand_id', $request->input('brand'))->where('product_id', $sku_product->product_id)->first();
          $customerProducts = CustomerProduct::where('customer_id', $request->input('customer'))->where('product_id', $sku_product->product_id)->first();
          if (isset($customerHasProducts)) {
            $res = $customerHasProducts->label_qty + $difference;
            $customerHasProducts->label_qty = $res;
            $customerHasProducts->save();
          }
          if (isset($customerProducts)) {
            $res2 = $customerProducts->label_qty + $difference;
            $customerProducts->label_qty = $res2;
            $customerProducts->save();
          }
          CustomerProductLabel::create([
            'user_id' => 1,
            'customer_id' => $request->input('customer'),
            'product_id' => $sku_product->product_id,
            'brand_id' => $request->input('brand'),
            'label_qty' => $difference
          ]);
        }
      }
    }
  }
  public function updateProductOrderDetails($request, $skuProduct, $orderId, $qty, $key)
  {
    foreach ($skuProduct as $skey => $value) {
      $custProd = CustomerProduct::where('customer_id', $request->customer)->where('product_id', $skuProduct[$skey]->product_id)->first();
      $custhasProd = CustomerHasProduct::where('customer_id', $request->customer)
        ->where('brand_id', $request->brand)
        ->where('product_id', $skuProduct[$skey]->product_id)
        ->first();
      if (isset($custProd)) // check if customer has this product
      {
        if (isset($custhasProd)) // check if customer's brand has this product
        {
          if (isset($orderDetails)) // check if this order exists
          {
            if ($qty == $orderDetails->qty) {
              if ($qty > 0) {
                if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                  $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                    'order_id' => $orderId,
                    'sku_id' => $request->sku[$key],
                    'product_id' => $skuProduct[$skey]->product_id,
                    'seller_cost_status' => $custProd->seller_cost_status
                  ]);
                } else {
                  $productOrderDetails = ProductOrderDetail::create([
                    'order_id' => $orderId,
                    'sku_id' => $request->sku[$key],
                    'product_id' => $skuProduct[$skey]->product_id,
                    'seller_cost_status' => $custProd->seller_cost_status
                  ]);
                }
              }
            } else if ($qty < $orderDetails->qty) {
              $difference = $orderDetails->qty - $qty;
              if (MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty + $difference;
                  $mergedQty->merged_qty = $res;
                  $mergedQty->save();
                }
              } else if (MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty + $difference;
                  $mergedQty->merged_qty = $res;
                  $mergedQty->save();
                }
              } else {
                $res = $custhasProd->label_qty + $difference;
                $custhasProd->label_qty = $res;
              }
              $custhasProd->save();
              CustomerProductLabel::create([
                'user_id' => Auth::user()->id,
                'customer_id' => $request->customer,
                'brand_id' => $request->brand,
                'product_id' => $skuProduct[$skey]->product_id,
                'label_qty' => $res
              ]);
              $custProd->label_qty = $res;
              $custProd->save();
              if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                  'order_id' => $orderId,
                  'sku_id' => $request->sku[$key],
                  'product_id' => $skuProduct[$skey]->product_id,
                  'seller_cost_status' => $custProd->seller_cost_status
                ]);
              } else {
                $productOrderDetails = ProductOrderDetail::create([
                  'order_id' => $orderId,
                  'sku_id' => $request->sku[$key],
                  'product_id' => $skuProduct[$skey]->product_id,
                  'seller_cost_status' => $custProd->seller_cost_status
                ]);
              }
            } else if ($qty > $orderDetails->qty) {
              $difference = $qty - $orderDetails->qty;
              if (MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty - $difference;
                  $mergedQty->merged_qty = $res;
                  $mergedQty->save();
                }
                // $res = $custhasProd->merged_qty - $qty;
                // $custhasProd->merged_qty = $res;
              } else if (MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                if (isset($mergedQty)) {
                  $res = $mergedQty->merged_qty - $difference;
                  $mergedQty->merged_qty = $res;
                  $res2 = $custhasProd->label_qty - $difference;
                  $custhasProd->label_qty = $res2;
                  $custhasProd->save();
                  $mergedQty->save();
                }
                // $res = $custhasProd->merged_qty - $qty;
                // $custhasProd->merged_qty = $res;
              } else {
                $res = $custhasProd->label_qty - $difference;
                $custhasProd->label_qty = $res;
                $custhasProd->save();
              }
              // $res = $custhasProd->label_qty - $difference;
              // $custhasProd->label_qty = $res;
              if ($custhasProd->is_active == 0) {
                ProductLabelOrder::create([
                  'customer_id' => $request->customer,
                  'brand_id' => $request->brand,
                  'product_id' => $skuProduct[$skey]->product_id,
                  'label_deduction' => $difference,
                  'order_id' => $orderId,
                  'sku_id' => $request->sku[$key]
                ]);
              }
              $custProd->label_qty = $res;
              $custProd->save();
              if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                  'order_id' => $orderId,
                  'sku_id' => $request->sku[$key],
                  'product_id' => $skuProduct[$skey]->product_id,
                  'seller_cost_status' => $custProd->seller_cost_status
                ]);
              } else {
                $productOrderDetails = ProductOrderDetail::create([
                  'order_id' => $orderId,
                  'sku_id' => $request->sku[$key],
                  'product_id' => $skuProduct[$skey]->product_id,
                  'seller_cost_status' => $custProd->seller_cost_status
                ]);
              }
            }
          }
        }
      }
    }
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $orderId)
  {
    $validatedData = $request->validate([
      'customer'     => 'required',
      "sku"          => "required",
      "qty"          => "required",
    ]);
    DB::beginTransaction();
    try {
      $checkOrderStatuses = Orders::where('id', $orderId)->first();
      if ($checkOrderStatuses->status == 4 || $checkOrderStatuses->merged == 1) {
        // return redirect()->back()->withError('Batch is Cancelled OR Merged');
      }
      $qtys = $request->qty;
      foreach ($request->sku as $key => $value) {
        $qty = $qtys[$key];
        $skuProduct = SkuProducts::where('sku_id', $request->sku[$key])->get();
        $skuProduct = SkuOrderDetails::where('sku_id', $request->sku[$key])->where('order_id', $orderId)->get();
        $orderDetails = OrderDetails::where('order_id', $orderId)->where('sku_id', $request->sku[$key])->first();
        $this->updateProductOrderDetails($request, $skuProduct, $orderId, $qty, $key);
      }
      $order = $this->updateOrder($request, $orderId);
      ////////////////adding invoce data/////////////////
      $invoiceData = Invoices::where('order_id', $orderId)->first();
      if (isset($invoiceData)) {
        $invoice = Invoices::where('order_id', $orderId)->update([
          'order_id' => $orderId,
          'invoice_number' => $orderId,
          'subtotal' => null,
          'tax' => null,
          'grand_total' => $request->grand_total_price != NULL ? $request->grand_total_price : 0.00,
          'customer_id' => $request->input('customer'),
          'is_paid' => '0',
          'status' => '0',
        ]);
      }
      ////////////////adding orders details data/////////////////
      foreach ($request->sku as $sku_key => $id) {
        $sku = SkuOrder::with('sku_product')->where('sku_id', $id)->where('order_id', $orderId)->first();
        if (!(isset($sku))) {
          $sku = Sku::with('sku_product')->where('id', $id)->first();
        }
        if (isset($sku)) {
          $service_charges_details = $this->updateServiceChargesDetails($request, $sku, $sku_key, $orderId);
          $orderDetails = '';
          $orderDetails = OrderDetails::where('sku_id', $id)->where('order_id', $orderId)->first();
          $this->updateOrderDetails($request, $id, $orderId, $sku, $sku_key);
          $this->updateInvoiceDetails($request, $invoiceData, $id, $sku_key, $sku, $orderId);
          $this->updateSkuProductDetails($request, $sku, $sku_key, $orderDetails, $orderId);
        }
      }
      DB::commit();
      return redirect('/orders')->withSuccess('Batch Updated Successfully');
    } catch (\Exception $e) {
      dd($e);
      DB::rollBack();
      return view('admin.server_error');
      return redirect()->back()->withError('Something went wrong');
    }
  }
  public function update2(Request $request, $orderId)
  {
    // dd($request->all());
    $validatedData = $request->validate([
      'customer'     => 'required',
      "sku"          => "required",
      "qty"          => "required",
    ]);
    DB::beginTransaction();
    try {
      $qtys = $request->qty;
      foreach ($request->sku as $key => $value) {
        $qty = $qtys[$key];
        $skuProduct = SkuProducts::where('sku_id', $request->sku[$key])->get();
        $orderDetails = OrderDetails::where('order_id', $orderId)->where('sku_id', $request->sku[$key])->first();
        foreach ($skuProduct as $skey => $value) {
          $custProd = CustomerProduct::where('customer_id', $request->customer)->where('product_id', $skuProduct[$skey]->product_id)->where('is_active', 0)->first();
          $custhasProd = CustomerHasProduct::where('customer_id', $request->customer)
            ->where('brand_id', $request->brand)
            ->where('product_id', $skuProduct[$skey]->product_id)
            ->where('is_active', 0)
            ->first();
          if (isset($custProd)) // check if customer has this product
          {
            if (isset($custhasProd)) // check if customer's brand has this product
            {
              if (isset($orderDetails)) // check if this order exists
              {
                if ($qty == $orderDetails->qty) {
                  if ($qty > 0) {
                    if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                      $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                        'order_id' => $orderId,
                        'sku_id' => $request->sku[$key],
                        'product_id' => $skuProduct[$skey]->product_id,
                        'seller_cost_status' => $custProd->seller_cost_status
                      ]);
                    } else {
                      $productOrderDetails = ProductOrderDetail::create([
                        'order_id' => $orderId,
                        'sku_id' => $request->sku[$key],
                        'product_id' => $skuProduct[$skey]->product_id,
                        'seller_cost_status' => $custProd->seller_cost_status
                      ]);
                    }
                  }
                } else if ($qty < $orderDetails->qty) {
                  $difference = $orderDetails->qty - $qty;
                  if (MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                    if (isset($mergedQty)) {
                      $res = $mergedQty->merged_qty + $difference;
                      $mergedQty->merged_qty = $res;
                      $mergedQty->save();
                    }
                  } else if (MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                    if (isset($mergedQty)) {
                      $res = $mergedQty->merged_qty + $difference;
                      $mergedQty->merged_qty = $res;
                      $mergedQty->save();
                    }
                  } else {
                    $res = $custhasProd->label_qty + $difference;
                    $custhasProd->label_qty = $res;
                  }
                  $custhasProd->save();
                  CustomerProductLabel::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'brand_id' => $request->brand,
                    'product_id' => $skuProduct[$skey]->product_id,
                    'label_qty' => $res
                  ]);
                  $custProd->label_qty = $res;
                  $custProd->save();
                  if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                      'order_id' => $orderId,
                      'sku_id' => $request->sku[$key],
                      'product_id' => $skuProduct[$skey]->product_id,
                      'seller_cost_status' => $custProd->seller_cost_status
                    ]);
                  } else {
                    $productOrderDetails = ProductOrderDetail::create([
                      'order_id' => $orderId,
                      'sku_id' => $request->sku[$key],
                      'product_id' => $skuProduct[$skey]->product_id,
                      'seller_cost_status' => $custProd->seller_cost_status
                    ]);
                  }
                } else if ($qty > $orderDetails->qty) {
                  $difference = $qty - $orderDetails->qty;
                  if (MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('merged_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                    if (isset($mergedQty)) {
                      $res = $mergedQty->merged_qty - $difference;
                      $mergedQty->merged_qty = $res;
                      $mergedQty->save();
                    }
                    // $res = $custhasProd->merged_qty - $qty;
                    // $custhasProd->merged_qty = $res;
                  } else if (MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer)->where('selected_brand', $request->brand)->where('product_id', $skuProduct[$skey]->product_id)->first();
                    if (isset($mergedQty)) {
                      $res = $mergedQty->merged_qty - $difference;
                      $mergedQty->merged_qty = $res;
                      $res2 = $custhasProd->label_qty - $difference;
                      $custhasProd->label_qty = $res2;
                      $custhasProd->save();
                      $mergedQty->save();
                    }
                    // $res = $custhasProd->merged_qty - $qty;
                    // $custhasProd->merged_qty = $res;
                  } else {
                    $res = $custhasProd->label_qty - $difference;
                    $custhasProd->label_qty = $res;
                    $custhasProd->save();
                  }
                  // $res = $custhasProd->label_qty - $difference;
                  // $custhasProd->label_qty = $res;
                  ProductLabelOrder::create([
                    'customer_id' => $request->customer,
                    'brand_id' => $request->brand,
                    'product_id' => $skuProduct[$skey]->product_id,
                    'label_deduction' => $res
                  ]);
                  $custProd->label_qty = $res;
                  $custProd->save();
                  if (ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->exists()) {
                    $productOrderDetails = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $skuProduct[$skey]->product_id)->update([
                      'order_id' => $orderId,
                      'sku_id' => $request->sku[$key],
                      'product_id' => $skuProduct[$skey]->product_id,
                      'seller_cost_status' => $custProd->seller_cost_status
                    ]);
                  } else {
                    $productOrderDetails = ProductOrderDetail::create([
                      'order_id' => $orderId,
                      'sku_id' => $request->sku[$key],
                      'product_id' => $skuProduct[$skey]->product_id,
                      'seller_cost_status' => $custProd->seller_cost_status
                    ]);
                  }
                }
              }
            }
          }
        }
      }
      $order = Orders::where('id', $orderId)->where('status', '!=', 4)->where('customer_id', $request->customer)->where('brand_id', $request->brand)->update([
        'customer_id' => $request->input('customer'),
        'brand_id' => $request->input('brand'),
        'total_cost' => $request->grand_total_price,
        'freight_cost' => '0',
        'duty_fee' => '0',
        'selling_price' => '0',
        'margin' => '0',
        'status' => '0',
        'labelqty' => $request->labelqty,
        'pickqty' => $request->pickqty,
        'packqty' => $request->packqty,
        'mailerqty' => $request->mailerqty,
        'postageqty' => $request->postageqty,
        'pick_pack_flat_qty' => $request->pickpackflatqty,
        'pick_pack_flat_price' => $request->grandTotalPickPackFlatPrice,
      ]);
      ////////////////adding invoce data/////////////////
      $invoiceData = Invoices::where('order_id', $orderId)->first();
      if (isset($invoiceData)) {
        $invoice = Invoices::where('order_id', $orderId)->update([
          'order_id' => $orderId,
          'invoice_number' => $orderId,
          'subtotal' => null,
          'tax' => null,
          'grand_total' => $request->grand_total_price,
          'customer_id' => $request->input('customer'),
          'is_paid' => '0',
          'status' => '0',
        ]);
      }
      ////////////////adding orders details data/////////////////
      foreach ($request->sku as $sku_key => $id) {
        $sku = Sku::with('sku_product', 'brand')->where('id', $id)->first();
        $service_charges_details = [];
        array_push($service_charges_details, ['slug' => 'labels_price', 'name' => 'Labels Price', 'price' => $request->_total_label_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'pick_price', 'name' => 'Pick Price', 'price' => $request->_total_pick_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'pack_price', 'name' => 'Pack Price', 'price' => $request->_total_pack_charges[$sku_key]]);
        array_push($service_charges_details, ['slug' => 'mailer_price', 'name' => 'Mailer Price', 'price' => $request->mailer_costNew]);
        array_push($service_charges_details, ['slug' => 'postage_price', 'name' => 'Postage Price', 'price' => $request->total_postage_price]);
        $orderDetails = '';
        $orderDetails = OrderDetails::where('sku_id', $id)->where('order_id', $orderId)->first();
        $orderDetail = OrderDetails::where('sku_id', $id)->where('order_id', $orderId)->update([
          'sku_id' => $id,
          'order_id' => $orderId,
          'qty' => $request->qty[$sku_key],
          'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
          'sku_purchasing_cost' => $sku->purchasing_cost,
          'sku_selling_cost' => $request->sku_selling_cost[$sku_key],
          'service_charges' => $request->_total_service_charges[$sku_key],
          'service_charges_detail' => json_encode($service_charges_details),
        ]);
        $invoiceDetail = InvoiceDetails::where('invoice_id', $invoiceData->id)->where('sku_id', $id)->update([
          'invoice_id' => $invoiceData->id,
          'sku_id' => $id,
          'qty' => $request->qty[$sku_key],
          'cost_of_good' => $sku->selling_cost * $request->qty[$sku_key],
          'service_charges' => $request->_total_service_charges[$sku_key],
          'service_charges_detail' => json_encode($service_charges_details),
        ]);
        // update inventory and labels
        foreach ($sku->sku_product as $prod_key => $sku_product) {
          $sku_product = $sku_product->load('product');
          $inventory = Inventory::where('product_id', $sku_product->product_id)->first();
          $ifProductInventoryHistoryExists = InventoryHistory::where('product_id', $sku_product->product_id)
            ->whereDate('created_at', Carbon::now())
            ->orderBy('created_at', 'DESC');
          if (isset($inventory)) {
            if ($request->qty[$sku_key] == $orderDetails->qty) {
              //
            } else if ($request->qty[$sku_key] > $orderDetails->qty) {
              $difference = $request->qty[$sku_key] - $orderDetails->qty;
              $inventory->qty = $inventory->qty - ($sku_product->quantity * $difference);
              $inventory->save();
              if ($ifProductInventoryHistoryExists->exists()) {
                $getProductInventoryHistory = $ifProductInventoryHistoryExists->take(1)->first();
                $ifProductInventoryHistoryExists->update([
                  'qty' => ($getProductInventoryHistory->qty)-($difference),
                  'sales' => $getProductInventoryHistory->sales + $difference,
                  'total' => $inventory->qty
                ]);
              } else {
                  InventoryHistory::create([
                    'product_id' => $sku_product->product_id,
                    'qty' => -($request->qty[$sku_key]),
                    'sales' => $request->qty[$sku_key],
                    'total' => $inventory->qty,
                    'order_id' => $orderId,
                    'sku_id' => $sku_product->sku_id,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                  ]);
              }
            } else if ($request->qty[$sku_key] < $orderDetails->qty) {
              $difference = $orderDetails->qty - $request->qty[$sku_key];
              $inventory->qty = $inventory->qty + ($sku_product->quantity * $difference);
              $inventory->save();
              if ($ifProductInventoryHistoryExists->exists()) {
                $getProductInventoryHistory = $ifProductInventoryHistoryExists->take(1)->first();
                $ifProductInventoryHistoryExists->update([
                  'qty' => -(-($getProductInventoryHistory->qty)-($difference)),
                  'sales' => $getProductInventoryHistory->sales - $difference,
                  'total' => $inventory->qty,
                  'manual_add' => $difference
                ]);
              } else {
                  InventoryHistory::create([
                    'product_id' => $sku_product->product_id,
                    'qty' => -($request->qty[$sku_key]),
                    'sales' => $request->qty[$sku_key],
                    'total' => $inventory->qty,
                    'order_id' => $orderId,
                    'sku_id' => $sku_product->sku_id,
                    'manual_add' => $difference,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                  ]);
              }
            }
          }
        }
      }
      DB::commit();
      return redirect('/orders')->withSuccess('Batch Updated Successfully');
    } catch (\Exception $e) {
      return view('admin.server_error');
      DB::rollBack();
      return redirect()->back()->withError('Something went wrong');
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
    $order = Orders::where('id', $id)->where('status', '=', 4)->delete();
    $invoice = Invoices::where('order_id', $id)->delete();
    return response()->json(['status' => true, 'msg' => 'Deleted Successfully']);
  }
  public function getBatchNumber()
  {
    $batch_numbers = Orders::groupBy('batch_no')->where('batch_no', '!=', null)->where('status', '!=', 4)->orderBy('batch_no', 'ASC')->pluck('batch_no')->toArray();
    if (sizeof($batch_numbers) > 0) {
      return response()->json([
        'status' => 'success',
        'data' => $batch_numbers
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'No Batch Number found'
      ], 200);
    }
  }
  public function orderReturn(Request $request)
  {
    if ($request->ajax()) {
      $data = OrderReturn::with('customer.service_charges', 'brand', 'orderReturnDetails.product', 'orderReturnedDetails.product', 'order_return_details_with_trashed.product')->where('total_qty', '!=', 0);
      if (Auth::user()->hasRole(['admin', 'Returns'])) {
        if ($request->customer != NULL && $request->customer != 'all') {
          $data = $data->where('customer_id', $request->customer);
          if ($request->brand != NULL && $request->brand != 'all') {
            $data = $data->where('customer_id', $request->customer)->where('brand_id', $request->brand);
          }
        } else {
          if ($request->brand != NULL && $request->brand != 'all' && $request->customer == 'all') {
            $data = $data->where('brand_id', $request->brand);
          }
        }
      } else {
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $data = $data->where('customer_id', $customerId);
        if (!empty($request->brand)) {
          if ($request->brand != 'all') {
            $data = $data->where('brand_id', $request->brand);
          }
        }
      }
      if ($request->po_box_number != Null && $request->po_box_number != 'all') {
        $po_box_number = $request->po_box_number;
        $customerIds = Customers::all()->where('po_box_number', $po_box_number)->pluck('id');
        $data = $data->whereIn('customer_id', $customerIds);
      }
      if (!empty($request->order_number)) {
        $data = $data->where('order_number', $request->order_number);
      }
      if (!empty($request->min_date)) {
        $max = $request->max_date;
        if ($max == '') {
          $max = Carbon::now();
        }
        $data = $data->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($max)->format('Y-m-d'));
      }
      $data = $data->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('btn', function ($row) {
            return '<center><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></center>';
        })
        ->addColumn('date', function ($row) {
          return '<span title="Created by: '.$row->user_name.'">'.date("m/d/Y", strtotime($row->created_at)).'</span>';
        })
        ->addColumn('brand_name', function ($row) {
          if (isset($row->brand)) {
            return ucwords($row->brand->brand);
          } else {
            return '';
          }
        })
        ->addColumn('order_number', function ($row) {
          if ($row->order_number == '') {
            return ucwords('-- NIL --');
          } else {
            return ucwords($row->order_number);
          }
        })
        ->addColumn('damage', function ($row) {
          return ucwords(' ');
        })
        ->addColumn('status', function ($row) {
          if ($row->status == 1) {
            return 'Returned';
          } else if ($row->status == 2) {
            return 'Damaged';
          } else if ($row->status == 3) {
            return 'Opened';
          } else if ($row->status == 4) {
            return 'Invalid Address';
          }
        })
        ->addColumn('cs_status', function ($row) {
          $btn = '';
          $btn .= '<select ' . ($row->cs_status == 1 ? " style='background-color: lightgrey; width: 100%' " : " style='background-color: white; width: 100%' ") . ' name="cs_status" data-order-id="' . $row->id . '" class="form-control-sm cs_status">
                        <option class="cs-item" ' . ($row->cs_status == 0 ? "selected" : "") . ' value="0">Select CS Status</option>
                          <option class="cs-item" ' . ($row->cs_status == 1 ? "selected" : "") . ' value="1">Handled</option>
                          <option class="cs-item" ' . ($row->cs_status == 2 ? "selected" : "") . ' value="2">Refunded</option>
                          <option class="cs-item" ' . ($row->cs_status == 3 ? "selected" : "") . ' value="3">Partial Refunded</option>
                          <option class="cs-item" ' . ($row->cs_status == 4 ? "selected" : "") . ' value="4">Pending</option>
                          <option class="cs-item" ' . ($row->cs_status == 5 ? "selected" : "") . ' value="5">Unable to find Customer</option>
                        </select>';
          return $btn;
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('state', function ($row) {
          return ucwords($row->state);
        })
        ->addColumn('cs_completed', function ($row) {
          return ucwords(' ');
        })
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('product_counts', function ($row) {
          if (isset($row->order_return_details_with_trashed)) {
            return $row->order_return_details_with_trashed->count();
          }
        })
        ->addColumn('selling_cost', function ($row) {
          if ($row->status == 2) {
            return 'Damaged';
          } else if ($row->status == 3) {
            return 'Opened';
          } else {
            $totalSellingCost = $row->total_selling_cost;
            return $totalSellingCost;
          }
        })
        ->addColumn('return_service_charges', function ($row) {
          return $row->cust_return_charges;
        })
        ->addColumn('cog_credit', function ($row) {
          if ($row->status == 2) {
            return (0 - $row->cust_return_charges);
          } else if ($row->status == 3) {
            return (0 - $row->cust_return_charges);
          } else {
            $totalSellingCost = $row->total_selling_cost - $row->cust_return_charges;
          }
          return $totalSellingCost;
        })
        ->addColumn('owe', function ($row) {
          if ($row->status == 2) {
            $totalSellingCost = $row->cust_return_charges;
          } else if ($row->status == 3) {
            $totalSellingCost = $row->cust_return_charges;
          } else {
            $totalSellingCost = $row->cust_return_charges;
          }
          return $totalSellingCost;
        })
        ->addColumn('price', function ($row) {
          if ($row->status == 1) {
            return $row->total_price;
          } else {
            return 0;
          }
        })
        ->addColumn('cog', function ($row) {
          return $row->cost_of_goods;
        })
        ->addColumn('total_price', function ($row) {
          return $row->total_price;
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->orderReturnDetails->sum('qty'));
        })
        ->addColumn('notes', function ($row) {
          return $row->notes;
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu order-return-menu">';
          // $btn .= '<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addNotes"><i data-feather="plus"></i><span>Add Notes</span></a>';
          $btn .= '<a class="dropdown-item" target="_blank" href="/view_returned_products/' . $row->id . '">
                      <i data-feather="eye"></i>
                      <span>
                        View Products
                      </span>
                    </a>';
          if (Auth::user()->hasRole(['admin', 'Returns'])) {
            $btn .= '<a class="dropdown-item" target="_blank" href="/return-order/edit/' . $row->id . '">
              <i data-feather="edit"></i>
              <span>
                Edit
              </span>
            </a>';
            $btn .= '<a class="dropdown-item" href="/return-order/delete/' . $row->id . '/'.$row->customer_id.'/delete_return">
              <i data-feather="trash"></i>
              <span>
                Delete
              </span>
            </a>';
          }
          $btn .= '</div>
                  </div>';
          return $btn;
        })
        ->rawColumns(['date', 'action', 'cs_status', 'btn'])
        ->make(true);
    }
    //
    if (Auth::user()->hasRole('admin')) {
      $orders = OrderReturn::with('customer', 'brand', 'orderReturnDetails')->get();
      $customers = Customers::get();
      $po_box_numbers = Customers::all()->where('po_box_number', '!=', Null)->pluck('po_box_number')->toArray();
      $po_box_numbers = array_unique($po_box_numbers);
      $brands = Labels::get();
    } else if (!Auth::user()->hasRole('admin')) {
      if (Auth::user()->can('return_order_view_all')) {
        $orders = OrderReturn::with('customer', 'brand', 'orderReturnDetails')->get();
        $customers = Customers::get();
        $po_box_numbers = Customers::all()->where('po_box_number', '!=', Null)->pluck('po_box_number')->toArray();
        $po_box_numbers = array_unique($po_box_numbers);
        $brands = Labels::get();
      } else {
        if (Auth::user()->can('return_order_view')) {
          $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
          $customerId = Auth::user()->id;
          if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
          }
          $orders = OrderReturn::with('customer', 'brand', 'orderReturnDetails')->where('customer_id', $customerId)->get();
          $customers = Customers::where('id', $customerId)->get();
          $po_box_numbers = Customers::all()->where('id', $customerId)->where('po_box_number', '!=', Null)->pluck('po_box_number')->toArray();
          $po_box_numbers = array_unique($po_box_numbers);
          $brands = Labels::where('customer_id', $customerId)->get();
        }
      }
    }
    return view('admin.orders.order_return', compact('customers', 'orders', 'po_box_numbers', 'brands'));
  }

  public function createReturnOrder(Request $request)
  {
    if (Auth::user()->can('return_order_create')) {
      $customers = Customers::get();
      $storagePath = url('public/');
    } else {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $customers = Customers::where('id', $customerId)->get();
      $storagePath = url('public');
    }
    return view('admin.orders.create_return_order', compact('customers', 'storagePath'));
  }

  public function checkOrderNumber(Request $request)
  {
    if (OrderReturn::where('order_number', $request->order_number)->exists()) {
      return response()->json(['msg' => 'error']);
    } else {
      return response()->json(['msg' => 'success']);
    }
  }

  public function saveReturnOrder(Request $request)
  {
    DB::beginTransaction();
    try {
      $brands = $request->input('brand');
      $customerReturnServiceCharges = ServiceCharges::where('customer_id', $request->customer)->first();
      $returnServiceChargesAmount = 0;
      if (isset($customerReturnServiceCharges)) {
        $returnServiceChargesAmount = $customerReturnServiceCharges->return_service_charges;
      }
      for ($i = 0; $i < count($brands); $i++) {
        $products = $request->input('product_id' . $i);
        $qty = $request->input('return_qty' . $i);
        $deductedPrice = $request->input('deducted_price' . $i);
        $count = 0;
        $totalDeductedPrice = 0;
        $totalSalePrice = 0;
        if ($qty != null) {
          for ($j = 0; $j < count($qty); $j++) {
            if ($qty[$j] > 1000) {
              return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
            }
            if (isset($products[$j])) {
              $customerHasProduct = CustomerHasProduct::where('customer_id', $request->customer)
                                    ->where('brand_id', $request->brand[$i])
                                    ->where('product_id', $products[$j])
                                    ->first();
            }
            $count = $count + $qty[$j];
            $totalDeductedPrice = $totalDeductedPrice + $deductedPrice[$j];
            $totalSalePrice = ($totalSalePrice) + (isset($customerHasProduct) ? $customerHasProduct->selling_price : '0.00');
          }
        }
        if ($count > 0) {
          $orderReturn = OrderReturn::create([
            'status' => $request->item_status[$i],
            'name' => $request->name[$i],
            'state' => $request->state[$i],
            'notes' => $request->description[$i],
            'order_id' => '0',
            'order_number' => $request->order_number[$i],
            'customer_id' => $request->customer,
            'brand_id' => $request->brand[$i],
            'cust_return_charges' => $returnServiceChargesAmount,
            'cost_of_goods' => $totalDeductedPrice,
            'total_selling_cost' => $totalSalePrice,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
          ]);
          $qtyCount = 0;
          if ($products != null) {
            for ($j = 0; $j < count($products); $j++) {
              if ($qty[$j] > 1000) {
                return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
              }
              $customerHasProduct = CustomerHasProduct::where('customer_id', $request->customer)
                                                        ->where('brand_id', $orderReturn->brand_id)
                                                        ->where('product_id', $products[$j])
                                                        ->first();
              $orderReturnDetails = OrderReturnDetail::create([
                'order_return_id' => $orderReturn->id,
                'brand_id' => $orderReturn->brand_id,
                'product_id' => $products[$j],
                'price' => $deductedPrice[$j],
                'selling_cost' => isset($customerHasProduct) ? $customerHasProduct->selling_price * $qty[$j] : '0.00',
                'qty' => $qty[$j],
                'item_status' => $orderReturn->status,
                'order_number' => $orderReturn->order_number,
                'name' => $orderReturn->name,
                'state' => $orderReturn->state,
                'status' => $orderReturn->status,
                'description' => '',
              ]);
              $qtyCount = $qtyCount + $qty[$j];
            }
          }
          $customerLastRecord = CustomerLedger::where('customer_id', $request->customer)->orderBy('id', 'DESC')->first();
          if ($orderReturn->status == 1 || $orderReturn->status == 4) {
            if ($customerLastRecord) {
              $lastBalance = $customerLastRecord['balance'];
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  $updated_balance = ($lastBalance + $deductedPrice[$j]) - $returnServiceChargesAmount;
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'balance' => $updated_balance,
                    'date' => date('Y-m-d')
                  ]);
                  // updating inventory quantity
                  $this->updateInventoryQuantity('create', $products[$j], $qty[$j], date('Y-m-d'));
                  // creating inventory backup record
                  $this->createInventoryHistory('create', $products[$j], $qty[$j], date('Y-m-d'), $request->customer, $orderReturn->id, $orderReturn->status);
                }
              }
            } else {
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'balance' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'date' => date('Y-m-d')
                  ]);
                  // updating inventory quantity
                  $this->updateInventoryQuantity('create', $products[$j], $qty[$j], date('Y-m-d'));
                  // creating inventory backup record
                  $this->createInventoryHistory('create', $products[$j], $qty[$j], date('Y-m-d'), $request->customer, $orderReturn->id, $orderReturn->status);
                }
              }
            }
            $orderReturn->total_qty = $qtyCount;
            $orderReturn->total_price = $totalDeductedPrice - $returnServiceChargesAmount;
            $orderReturn->save();
          } else {
            if ($customerLastRecord) {
              $lastBalance = $customerLastRecord->balance;
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  $updated_balance = $lastBalance - $returnServiceChargesAmount;
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => '0',
                    'balance' => $updated_balance,
                    'date' => date('Y-m-d')
                  ]);
                }
              }
            }
            $orderReturn->total_qty = $qtyCount;
            $orderReturn->total_price = $returnServiceChargesAmount;
            $orderReturn->save();
          }
        }
      }
      DB::commit();
      return response()->json(['status' => true, 'msg' => 'Data saved successfully']);
      // return redirect('/order_return')->withSuccess('Order Returned Successfully');
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['status' => false, 'msg' => 'Something went wrong']);
      dd($e);
      return view('admin.server_error');
      return redirect()->back()->withError('Something went wrong');
    }
  }

  public function viewReturnedProducts(Request $request, $id)
  {
    if ($request->ajax()) {
      if (!Auth::user()->hasRole('admin')) {
        if (Auth::user()->can('return_order_view_all')) {
          $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('order_return_id', $id)->where('qty', '>', 0)->groupBy('product_id')->get();
        } else {
          if (Auth::user()->can('return_order_view')) {
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
              $customerId = $customerUser->customer_id;
            }
            if (OrderReturn::where('id', $id)->where('customer_id', $customerId)->exists()) {
              $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('order_return_id', $id)->where('qty', '>', 0)->groupBy('product_id')->get();
            } else {
              $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('order_return_id', 0)->where('qty', '>', 0)->groupBy('product_id')->get();
            }
          }
        }
      } else {
        $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('order_return_id', $id)->where('qty', '>', 0)->groupBy('product_id')->get();
      }
      if ($request->status != NULL && $request->status != 'all') {
        $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('item_status', $request->status)->where('order_return_id', $id)->where('qty', '>', 0)->groupBy('product_id')->get();
      }
      if ($request->min_date != NULL) {
        $max = $request->max_date;
        if ($max == '') {
          $max = Carbon::now();
        }
        $data = OrderReturnDetail::with('orderReturn', 'brand', 'product')->where('order_return_id', $id)
        ->whereDate('created_at', '>=', Carbon::parse($request->min_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($max)->format('Y-m-d'))->where('qty', '>', 0)->get();
      }
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('date', function ($row) {
          return date("m/d/Y", strtotime($row->created_at));
        })
        ->addColumn('order_number', function ($row) {
          return ucwords($row->order_number);
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('state', function ($row) {
          return ucwords($row->state);
        })
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->orderReturn->customer->customer_name);
        })
        ->addColumn('brand_name', function ($row) {
          return ucwords($row->orderReturn->brand->brand);
        })
        ->addColumn('product_name', function ($row) {
          return ucwords($row->product->name);
        })
        ->addColumn('price', function ($row) {
          return '$' . number_format($row->price, 2);
        })
        ->addColumn('selling_cost', function ($row) {
          return '$' . number_format($row->selling_cost, 2);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('image', function ($row) {
          $url = asset('images/products/' . $row->product->image);
          return '<img src="' . $url . '" border="0" width="40" class="img-rounded" align="center" />';
        })
        ->addColumn('status', function ($row) {
          if ($row->item_status == 1) {
            return 'Return to Sender';
          } else if ($row->item_status == 2) {
            return 'Damaged';
          } else if ($row->item_status == 3) {
            return 'Opened';
          } else {
            return 'No Status';
          }
        })
        ->addColumn('description', function ($row) {
          return $row->description;
        })
        ->addColumn('action', function ($row) use ($id) {
          $btn = '<div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu">';
          // $btn .= '<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadFile">
          //                   <i data-feather="file"></i>
          //                   <span>Add Notes</span>
          //               </a>';
          $btn .= '<a href="#" class="dropdown-item addNoteButton" data-product-id="'. $row->product->id .'" data-bs-toggle="modal" data-bs-target="#addNotes"><i data-feather="plus"></i><span>Add Notes</span></a>';
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'status', 'image'])
        ->make(true);
    }
    $orders = OrderReturnDetail::groupBy('status')->get();
    $customers = Customers::get();
    return view('admin.orders.view_returned_products', compact('id', 'orders', 'customers'));
  }

  public function productNotes(Request $request, $orderId)
  {
    if ($request->ajax()) {
      $productNotes = ProductNotes::where('order_id', $orderId)->get();
      // dd($productNotes);
      return Datatables::of($productNotes)
        ->addIndexColumn()
        ->addColumn('order_number', function ($row) use ($orderId) {
          $order = OrderReturnDetail::where('order_return_id', $orderId)->first();
          if (isset($order)) {
            return ucwords($order->order_number);
          }
        })
        ->addColumn('notes', function ($row) use ($orderId) {
          return ucwords($row->notes);
        })
        // ->rawColumns(['status', 'input_field', 'product', 'price_field', 'remaining_qty', 'order_status', 'description', 'image', 'action', 'item_status', 'order_number', 'name', 'state'])
        ->make(true);
    }
    $customers = Customers::get();
    return view('admin.orders.product_notes', compact('orderId', 'customers'));
  }

  public function updateProductNotes(Request $request)
  {
    DB::beginTransaction();
    try {
      // ProductNotes::create([
      //   'order_id' => $request->order_id,
      //   'customer_id' => Auth::user()->id,
      //   'user_id' => Auth::user()->id,
      //   'notes' => $request->notes
      // ]);
      $orderReturnDetails = OrderReturnDetail::where('order_return_id', $request->order_id)->where('product_id', $request->product_id)->update([
        'description' => $request->description
      ]);
      DB::commit();
      // return redirect()->back()->withSuccess('Added Successfully');
      if ($request->ajax()) {
        return response()->json(['status' => true]);
      } else {
        return redirect()->back();
      }
    } catch (\Exception $e) {
      return view('admin.server_error');
      DB::rollBack();
      // return redirect()->back()->withError('Something went wrong');
      return response()->json(['status' => false]);
    }
  }

  public function editReturnOrder(Request $request, $id)
  {
    $returnOrder = OrderReturn::with('order_return_details', 'customer')->where('id', $id)->first();
    $storagePath = url('public/');
    $brands = Labels::where('customer_id', $returnOrder->customer_id)->get();
    return view('admin.orders.edit_return_order', compact('id', 'returnOrder', 'storagePath', 'brands'));
  }

  public function deleteReturnOrder($id, $customerId, $delete=null)
  {
    $orderReturn = OrderReturn::where('id', $id)->first();
    $orderReturnDetails = OrderReturnDetail::where('order_return_id', $id)->get();
    $customerReturnServiceCharges = ServiceCharges::where('customer_id', $customerId)->first();
    foreach ($orderReturnDetails as $key => $product) {
      if (isset($product)) {
        $customerLastRecord = CustomerLedger::where('customer_id', $customerId)->orderBy('id', 'DESC')->first();
        if ($orderReturn->status == 1 || $orderReturn->status == 4) {
          $lastBalance = $customerLastRecord->balance;
          $updated_balance = ($lastBalance - $product->price) + $customerReturnServiceCharges->return_service_charges;
          CustomerLedger::create([
            'user_id' => Auth::user()->id,
            'customer_id' => $customerId,
            'order_id' => $orderReturn->id,
            'order_number' => $orderReturn->order_number,
            'product_id' => $product->product_id,
            'qty' => $product->qty,
            'item_status' => $orderReturn->status,
            'return_service_charges' => $customerReturnServiceCharges->return_service_charges,
            'debit' => ($product->price * $product->qty) - $customerReturnServiceCharges->return_service_charges,
            'credit' => $customerReturnServiceCharges->return_service_charges,
            'balance' => $updated_balance,
            'date' => date('Y-m-d')
          ]);
          // updating inventory quantity
          $this->updateInventoryQuantity('edit', $product->product_id, ($product->qty), date('Y-m-d'));
          // creating inventory backup record
          $this->createInventoryHistory('edit', $product->product_id, ($product->qty), date('Y-m-d'), $customerId, $orderReturn->id, $orderReturn->status);
        }
      }
    }
    if ($delete != null) {
      if ($delete == 'delete_return') {
        OrderReturn::where('id', $id)->delete();
        OrderReturnDetail::where('order_return_id', $orderReturn->id)->delete();
        return redirect()->back()->with('success', 'Deleted Successfully');
      }
    } else {
      OrderReturn::where('id', $id)->forceDelete();
      OrderReturnDetail::where('order_return_id', $orderReturn->id)->forceDelete();
    }
  }

  public function updateReturnOrder(Request $request, $id)
  {
    DB::beginTransaction();
    try {
      $customerLastRecord = CustomerLedger::where('customer_id', $request->customer)->orderBy('id', 'DESC')->first();
      $brands = $request->input('brand');
      $customerReturnServiceCharges = ServiceCharges::where('customer_id', $request->customer)->first();
      $returnServiceChargesAmount = 0;
      if (isset($customerReturnServiceCharges)) {
        $returnServiceChargesAmount = $customerReturnServiceCharges->return_service_charges;
      }
      $this->deleteReturnOrder($id, $request->customer, $returnServiceChargesAmount);
      for ($i = 0; $i < count($brands); $i++) {
        $products = $request->input('product_id' . $i);
        $qty = $request->input('return_qty' . $i);
        $deductedPrice = $request->input('deducted_price' . $i);
        $count = 0;
        $totalDeductedPrice = 0;
        $totalSalePrice = 0;
        if ($qty != null) {
          for ($j = 0; $j < count($qty); $j++) {
            if ($qty[$j] > 1000) {
              return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
            }
            if (isset($products[$j])) {
              $customerHasProduct = CustomerHasProduct::where('customer_id', $request->customer)
                                    ->where('brand_id', $request->brand[$i])
                                    ->where('product_id', $products[$j])
                                    ->first();
            }
            $count = $count + $qty[$j];
            $totalDeductedPrice = $totalDeductedPrice + $deductedPrice[$j];
            $totalSalePrice = ($totalSalePrice) + (isset($customerHasProduct) ? $customerHasProduct->selling_price : '0.00');
          }
        }
        if ($count > 0) {
          $orderReturn = OrderReturn::create([
            'status' => $request->item_status[$i],
            'name' => $request->name[$i],
            'state' => $request->state[$i],
            'notes' => $request->description[$i],
            'order_id' => '0',
            'order_number' => $request->order_number[$i],
            'customer_id' => $request->customer,
            'brand_id' => $request->brand[$i],
            'cust_return_charges' => $returnServiceChargesAmount,
            'cost_of_goods' => $totalDeductedPrice,
            'total_selling_cost' => $totalSalePrice,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
          ]);
          $qtyCount = 0;
          if ($products != null) {
            for ($j = 0; $j < count($products); $j++) {
              if ($qty[$j] > 1000) {
                return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
              }
              $customerHasProduct = CustomerHasProduct::where('customer_id', $request->customer)
                                                        ->where('brand_id', $orderReturn->brand_id)
                                                        ->where('product_id', $products[$j])
                                                        ->first();
              $orderReturnDetails = OrderReturnDetail::create([
                'order_return_id' => $orderReturn->id,
                'brand_id' => $orderReturn->brand_id,
                'product_id' => $products[$j],
                'price' => $deductedPrice[$j],
                'selling_cost' => isset($customerHasProduct) ? $customerHasProduct->selling_price * $qty[$j] : '0.00',
                'qty' => $qty[$j],
                'item_status' => $orderReturn->status,
                'order_number' => $orderReturn->order_number,
                'name' => $orderReturn->name,
                'state' => $orderReturn->state,
                'status' => $orderReturn->status,
                'description' => '',
              ]);
              $qtyCount = $qtyCount + $qty[$j];
            }
          }
          if ($orderReturn->status == 1 || $orderReturn->status == 4) {
            if ($customerLastRecord) {
              $lastBalance = $customerLastRecord['balance'];
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  $updated_balance = ($lastBalance + $deductedPrice[$j]) - $returnServiceChargesAmount;
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'balance' => $updated_balance,
                    'date' => date('Y-m-d')
                  ]);
                  // updating inventory quantity
                  $this->updateInventoryQuantity('create', $products[$j], $qty[$j], date('Y-m-d'));
                  // creating inventory backup record
                  $this->createInventoryHistory('create', $products[$j], $qty[$j], date('Y-m-d'), $request->customer, $orderReturn->id, $orderReturn->status);
                }
              }
            } else {
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'balance' => ($deductedPrice[$j] * $qty[$j]) - $returnServiceChargesAmount,
                    'date' => date('Y-m-d')
                  ]);
                  // updating inventory quantity
                  $this->updateInventoryQuantity('create', $products[$j], $qty[$j], date('Y-m-d'));
                  // creating inventory backup record
                  $this->createInventoryHistory('create', $products[$j], $qty[$j], date('Y-m-d'), $request->customer, $orderReturn->id, $orderReturn->status);
                }
              }
            }
            $orderReturn->total_qty = $qtyCount;
            $orderReturn->total_price = $totalDeductedPrice - $returnServiceChargesAmount;
            $orderReturn->save();
          } else {
            if ($customerLastRecord) {
              $lastBalance = $customerLastRecord->balance;
              if ($products != null) {
                for ($j = 0; $j < count($products); $j++) {
                  if ($qty[$j] > 1000) {
                    return response()->json(['status' => false, 'msg' => 'Quantity cannot be greater than 1000']);
                  }
                  $updated_balance = $lastBalance - $returnServiceChargesAmount;
                  CustomerLedger::create([
                    'user_id' => Auth::user()->id,
                    'customer_id' => $request->customer,
                    'order_id' => $orderReturn->id,
                    'order_number' => $orderReturn->order_number,
                    'product_id' => $products[$j],
                    'qty' => $qty[$j],
                    'item_status' => $request->item_status[$i],
                    'return_service_charges' => $returnServiceChargesAmount,
                    'debit' => $returnServiceChargesAmount,
                    'credit' => '0',
                    'balance' => $updated_balance,
                    'date' => date('Y-m-d')
                  ]);
                }
              }
            }
            $orderReturn->total_qty = $qtyCount;
            $orderReturn->total_price = $returnServiceChargesAmount;
            $orderReturn->save();
          }
        }
      }
      DB::commit();
      return response()->json(['status' => true, 'msg' => 'Data saved successfully']);
      // return redirect('/order_return')->withSuccess('Order Returned Successfully');
    } catch (\Exception $e) {
      dd($e);
      DB::rollback();
      return response()->json(['status' => false, 'msg' => 'Something went wrong']);
      return view('admin.server_error');
      return redirect()->back()->withError('Something went wrong');
    }
  }

  // update inventory quantity
  public function updateInventoryQuantity($type, $product_id, $qty, $date)
  {
    DB::beginTransaction();
    try {
      // inventory quantity
      $currentInventoryQuantity = Inventory::where('product_id', $product_id)->first();
      if ($currentInventoryQuantity) {
        if ($type == 'edit') {
          $updatedInventoryQuantity = 0;
          $updatedInventoryQuantity = $currentInventoryQuantity->qty - $qty;
          Inventory::where('product_id', $product_id)->update(['qty' => $updatedInventoryQuantity]);
        } else if ($type == 'create') {
          $updatedInventoryQuantity = 0;
          $updatedInventoryQuantity = $currentInventoryQuantity->qty + $qty;
          Inventory::where('product_id', $product_id)->update(['qty' => $updatedInventoryQuantity]);
        }
      } else {
        Inventory::create([
          'product_id' => $product_id,
          'qty' => $qty,
          'date' => $date
        ]);
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      dd($e);
      return view('admin.server_error');
      DB::rollBack();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  // inventory history
  public function createInventoryHistory($checktype = null, $product_id, $qty, $date, $customer_id, $return_order_id, $item_status)
  {
    $inventory = Inventory::where('product_id', $product_id)->first();
    $inventoryHistory = InventoryHistory::create([
      'product_id' => $product_id,
      'item_status' => $item_status,
      'qty' => !is_null($checktype) ? ($checktype == 'edit' ? -$qty : $qty) : $qty,
      'date' => $date,
      'customer_id' => $customer_id,
      'return_order_id' => $return_order_id,
      'return_add' => !is_null($checktype) ? ($checktype == 'edit' ? 0 : $qty) : $qty,
      'return_edited' => !is_null($checktype) ? ($checktype == 'edit' ? $qty : 0) : 0,
      'total' => $inventory->qty,
      'user_id' => Auth::user()->id,
      'user_name' => Auth::user()->name,
    ]);
    if ($checktype == 'edit') {
      // $inventoryHistory->delete();
    }
  }
  public function updateCsStatus(Request $request, $id)
  {
    DB::beginTransaction();
    try {
      OrderReturn::where('id', $id)->update([
        'cs_status' => $request->cs_status,
      ]);
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      return view('admin.server_error');
      DB::rollBack();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function setTables()
  {
    OrderDetails::where('qty', '=', '')->update([
      'qty' => 0
    ]);
    OrderDetails::where('sku_selling_cost', '=', '')->update([
      'sku_selling_cost' => '0.00'
    ]);
    Invoices::where('grand_total', '=', '')->update([
      'grand_total' => '0.00'
    ]);
    InvoiceDetails::where('qty', '=', '')->update([
      'qty' => 0
    ]);
    return 'Done';
  }
  public function setInventoryHistory()
  {
    dd('stop');
    InventoryHistory::withTrashed()->where('qty', 0)->where('manual_add', 0)->where('supplier_inventory_received', 0)->where('return_add', 0)->where('manual_reduce', 0)->where('sales', 0)->forceDelete();
    InventoryHistory::where('sales', '>', 0)->whereDate('created_at', '2022-07-19')->delete();

    $orderDetails = OrderDetails::with('skuproduct')->whereDate('created_at', '2022-07-19')->get();
    foreach ($orderDetails as $detailkey => $detail) {
      if (isset($detail)) {
        foreach ($detail->skuproduct as $prod_key => $sku_product) {
          if ($detail->qty > 0) {

              $inventory = InventoryHistory::where('product_id', $sku_product->product_id)->whereDate('created_at', '=', '2022-07-19')->latest()->take(1)->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();

              if (isset($inventory)) {
                if ($inventory->manual_add > 0 || $inventory->supplier_inventory_received > 0 || $inventory->return_add > 0 || $inventory->manual_reduce > 0) {
                  InventoryHistory::create([
                    'product_id' => $sku_product->product_id,
                    'qty' => -($detail->qty),
                    'sales' => $detail->qty,
                    'total' => $inventory->total,
                    'order_id' => $detail->order_id,
                    'sku_id' => $sku_product->sku_id,
                    'created_at' => $detail->created_at,
                    'updated_at' => $detail->created_at,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                  ]);
                } else {
                  InventoryHistory::create([
                    'product_id' => $sku_product->product_id,
                    'qty' => -($detail->qty),
                    'sales' => $detail->qty,
                    'total' => $inventory->total - $detail->qty,
                    'order_id' => $detail->order_id,
                    'sku_id' => $sku_product->sku_id,
                    'created_at' => $detail->created_at,
                    'updated_at' => $detail->created_at,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                  ]);
                }
              } else {
                $inventory = InventoryHistory::where('product_id', $sku_product->product_id)->whereDate('created_at', '<', '2022-07-19')->latest()->take(1)->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();
                // dd($inventory->total, $detail->qty);
                InventoryHistory::create([
                  'product_id' => $sku_product->product_id,
                  'qty' => -($detail->qty),
                  'sales' => $detail->qty,
                  'total' => $inventory->total - $detail->qty,
                  'order_id' => $detail->order_id,
                  'sku_id' => $sku_product->sku_id,
                  'created_at' => $detail->created_at,
                  'updated_at' => $detail->created_at,
                  'user_id' => Auth::user()->id,
                  'user_name' => Auth::user()->name,
                ]);
              }
          }
        }
      }
    }

    InventoryHistory::where('sales', '>', 0)->whereDate('created_at', '2022-07-20')->delete();

    $orderDetails = OrderDetails::with('skuproduct')->whereDate('created_at', '2022-07-20')->get();
    foreach ($orderDetails as $detailkey => $detail) {
      if (isset($detail)) {
        foreach ($detail->skuproduct as $prod_key => $sku_product) {
          if ($detail->qty > 0) {
            $inventory = InventoryHistory::where('product_id', $sku_product->product_id)->whereDate('created_at', '=', '2022-07-20')->latest()->take(1)->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();
            if (isset($inventory)) {
              if ($inventory->manual_add > 0 || $inventory->supplier_inventory_received > 0 || $inventory->return_add > 0 || $inventory->manual_reduce > 0) {
                InventoryHistory::create([
                  'product_id' => $sku_product->product_id,
                  'qty' => -($detail->qty),
                  'sales' => $detail->qty,
                  'total' => $inventory->total,
                  'order_id' => $detail->order_id,
                  'sku_id' => $sku_product->sku_id,
                  'created_at' => $detail->created_at,
                  'updated_at' => $detail->created_at,
                  'user_id' => Auth::user()->id,
                  'user_name' => Auth::user()->name,
                ]);
              } else {
                InventoryHistory::create([
                  'product_id' => $sku_product->product_id,
                  'qty' => -($detail->qty),
                  'sales' => $detail->qty,
                  'total' => $inventory->total - $detail->qty,
                  'order_id' => $detail->order_id,
                  'sku_id' => $sku_product->sku_id,
                  'created_at' => $detail->created_at,
                  'updated_at' => $detail->created_at,
                  'user_id' => Auth::user()->id,
                  'user_name' => Auth::user()->name,
                ]);
              }
            } else {
              $inventory = InventoryHistory::where('product_id', $sku_product->product_id)->whereDate('created_at', '<', '2022-07-20')->latest()->take(1)->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();
              InventoryHistory::create([
                'product_id' => $sku_product->product_id,
                'qty' => -($detail->qty),
                'sales' => $detail->qty,
                'total' => $inventory->total - $detail->qty,
                'order_id' => $detail->order_id,
                'sku_id' => $sku_product->sku_id,
                'created_at' => $detail->created_at,
                'updated_at' => $detail->created_at,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
              ]);
            }
          }
        }
      }
    }

    InventoryHistory::onlyTrashed()->forceDelete();
    dd('Updated');
  }
  public function addCustomerReturnCharges()
  {
    $orderReturns = OrderReturn::get();
    foreach ($orderReturns as $key => $order) {
      $customerReturnCharges = ServiceCharges::where('customer_id', $order->customer_id)->first();
      if (isset($customerReturnCharges)) {
        $order->cust_return_charges = $customerReturnCharges->return_service_charges;
        $order->save();
      }
    }
  }

}
