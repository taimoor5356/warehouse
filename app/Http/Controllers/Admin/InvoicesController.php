<?php

namespace App\Http\Controllers\Admin;

use Session;
use Redirect;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SkuOrder;
use PDF;
use App\AdminModels\Cities;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\AdminModels\States;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Invoices;
use App\AdminModels\Products;
use App\Jobs\MergeInvoiceJob;
use App\Models\MergedInvoice;
use App\AdminModels\Countries;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\InvoicePayment;
use App\Models\InvoicesMerged;
use App\Models\CustomerProduct;
use App\Models\SkuOrderDetails;
use App\AdminModels\OrderDetails;
use App\Models\ProductOrderDetail;
use App\Traits\MergeInvoicesTrait;
use Illuminate\Support\Facades\DB;
use App\AdminModels\InvoiceDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\AdminModels\OrderShippingInfo;
use App\Jobs\MergeCustomerAllInvoicesJob;
use App\Models\OrderReturnDetail;

class InvoicesController extends Controller
{
  use MergeInvoicesTrait;
  public $balance = 0;
  public $totalAmountNow = 0;
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
      if (Auth::user()->can('invoices_view')) {
        // $query = Invoices::orderBy('created_at', 'DESC')->with('orders')->select(['invoices.*']);
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
        $totalRecords = Invoices::with('orders')
                        ->select('count(*) as allcount')
                        ->whereHas('orders', function ($q) {
                          $q->where('status', '!=', 4);
                        })
                        ->count();
        $totalRecordswithFilter = Invoices::with('orders')
                                  ->whereHas('orders', function ($q) {
                                    $q->where('status', '!=', 4);
                                  })
                                  ->select('count(*) as allcount');
        // Fetch records
        $query = Invoices::with('orders','Customer', 'brand_data')
                ->whereHas('orders', function ($q) {
                  $q->where('status', '!=', 4);
                })
                ->whereHas('brand_data', function($q){
                  $q->withTrashed();
                });
        $keyword = $request->get('search')['value'];
        if (!empty($request->from_date)) {
          if (!empty($request->to_date)) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
            $totalRecordswithFilter = $totalRecordswithFilter->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'))->whereDate('created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
          } else {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
            $totalRecordswithFilter = $totalRecordswithFilter->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
          }
        }
        if (!empty($request->batch_no)) {
          $query = $query->where('order_id', $request->batch_no);
          $totalRecordswithFilter = $totalRecordswithFilter->where('order_id', $request->batch_no);
        }
        if (!empty($request->customer)) {
          $query->whereHas("Customer", function ($q) use ($request) {
            $q->where("id", "=", $request->customer);
          });
          $totalRecordswithFilter->whereHas("Customer", function ($q) use ($request) {
            $q->where("id", "=", $request->customer);
          });
        }
        if (!empty($request->brand)) {
          $order = Orders::where('brand_id', $request->brand)->where('status', '!=', 4)->get()->pluck('id');
          if (isset($order)) {
            $query = $query->whereIn('order_id', $order);
            $totalRecordswithFilter = $totalRecordswithFilter->whereIn('order_id', $order);
          }
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
        $query = $query->select(['invoices.*'])
        ->skip($start)
        ->take($rowperpage);
      } else {
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $query = Invoices::with('orders')->where('customer_id', $customerId);
      }
      $data = $query->get();
      $data_arr = [];
      $sno = $start+1;
      foreach($data as $record){
        $id = $record->id;
        $date = $record->created_at;
        $brand = Labels::where('id', $record->orders->brand_id)->first();
        if (isset($brand)) {
          $brand_name = $brand->brand;
        } else {
          $brand_name = '';
        }
        $customer_name = isset($record->Customer) ? $record->Customer->customer_name : '';
        $brandName = $brand_name;
        $invoiceNumber = $record->customer_name.'-invoice-no-'.$record->inv_no;
        $totalCost = $record->grand_total;
        $paidAmount = $record->paid;
        $status = '';
        if (Orders::where('merged', 0)->where('merge_running', 1)->where('id', $record->order_id)->exists()) {
          $checkbox = '<div class="removeable"><div class="merging-loader" data-loader-type="merging"></div></div>';
        } else {
          $checkbox = '<input type="checkbox" ';
            if (InvoicesMerged::where('invoice_id', $record->id)->exists()) {
              $checkbox .= 'checked disabled title="Already Merged" class="mergedToolTip" style="border: 1px solid red"';
              $status = 'merged';
            } else if (Invoices::where('id', $record->id)->where('merged', 1)->exists()) {
              $checkbox .= 'checked disabled title="Already Merged" class="mergedToolTip" style="border: 1px solid red"';
              $status = 'merged';
            } else {
              if ($record->order_status == 4) {
                $checkbox .= 'disabled title="Already Merged" class="mergedToolTip" style="border: 1px solid red"';
                $status = 'merged';
              } else {
                $checkbox .= 'data-invoice-id="' . $record->id . '" data-singleorder-id="' . $record->id . '" data-invoice-order-id="' . $record->order_id . '" data-invoice-customer-id="' . $record->customer_id . '" data-invoice-number="' . $record->invoice_number . '" data-inv-no="'. $record->inv_no .'" class="singleOrderCheck" ';
              }
            }
            $checkbox .= ' />';
        }
          
          $btn = '<div class="dropdown">
                  <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i data-feather="more-vertical"></i>
                  </button>
                  <div class="dropdown-menu">';
                  if ($record->order_status == 4) {
                    $btn .= '<a class="dropdown-item" data-status="'. $record->is_paid .'">
                                  <i data-feather="eye"></i>
                                  <span>Partial Payments</span>
                              </a>';
                  } else {
                    $btn .= '<a class="dropdown-item" data-status="'. $record->is_paid .'" href="partial-payments/' . $record->id . '">
                                  <i data-feather="eye"></i>
                                  <span>Partial Payments</span>
                              </a>';
                  }
          $btn .= '</div></div>';
        $invoiceNumberData = '';
        if ($record->order_status == 4) {
          $invoiceNumberData .= '<a href="#" class="text-center py-2">'.$customer_name.'-invoice-no-'.$record->order_id.'</a>';
        } else {
          $invoiceNumberData .= '<a href="#" class="text-center py-2" onclick="window.open(`invoice/order/details/'.$record->order_id.'`, ``, `width=1300, height=700`)";>'.$customer_name.'-invoice-no-'.$record->order_id.'</a>';
        }
        // is paid btn
        $isPaidbtn = '';
        if (Auth::user()->can('invoices_view')) {
          $isPaidbtn .= '
                  <div style="display: inline-flex">
                    <select name="is_paid" data-invoice-id="' . $record->id . '"';
                    if ($record->order_status == 4) {
                      $isPaidbtn .= ' id="invoice_is_paid" class="form-select" disabled>
                      <option selected value="0">Cancelled</option>
                        <option ' . ($record->is_paid == 0 ? "" : "") . ' value="0">Un Paid</option>
                        <option ' . ($record->is_paid == 1 ? "" : "") . ' value="1">Paid</option>
                        <option ' . ($record->is_paid == 2 ? "" : "") . ' value="2" class="is-paid" data-bs-toggle="modal" data-bs-target="#isPaidModal" data-invoice-id="'. $record->id .'" data-customer-id="'. $record->customer_id .'">Partially Paid</option>
                      </select>';
                    } else {
                      $isPaidbtn .= ' id="invoice_is_paid" class="form-select" data-total-cost="'. number_format($record->grand_total, 2) .'">
                        <option ' . ($record->is_paid == 0 ? "selected" : "") . ' value="0">Un Paid</option>
                        <option ' . ($record->is_paid == 1 ? "selected" : "") . ' value="1">Paid</option>
                        <option ' . ($record->is_paid == 2 ? "selected" : "") . ' value="2" class="is-paid" data-bs-toggle="modal" data-bs-target="#isPaidModal" data-invoice-id="'. $record->id .'" data-customer-id="'. $record->customer_id .'">Partially Paid</option>
                      </select>';
                    }
                    if ($record->is_paid == 2) {
                      $isPaidbtn .= '<button type="button" value="'.$record->is_paid.'" data-total-cost="'. number_format($record->grand_total) .'" href="#" title="Add Partial Payments" class="btn btn-primary add-partial-payment" data-bs-toggle="modal" data-bs-target="#isPaidModal" data-invoice-id="'. $record->id .'" data-customer-id="'. $record->customer_id .'" style="margin: 10px 2px; padding: 5px"><i data-feather="plus"></i></button>';
                    } else {
                      $isPaidbtn .= '';
                    }
                  $isPaidbtn .= '</div>';
        } else {
          $opt = '';
          if ($record->is_paid == 0) {
            $opt = 'Un Paid';
          } else if ($record->is_paid == 1) {
            $opt = 'Paid';
          }
          $isPaidbtn .= '<select name="is_paid" data-invoice-id="' . $record->id . '" id="invoice_is_paid" class="form-select" >';
          $isPaidbtn .= '<option ' . ($record->is_paid == 0 ? "selected" : "") . ' value="' . $record->is_paid . '">' . $opt . '</option>';
          $isPaidbtn .= '</select>';
        }
        $data_arr[] = array(
          'id' => $id,
          'check_box' => $checkbox,
          'created_at' => Carbon::parse($record->created_at)->format('m/d/Y'),
          'order_status' => $record->order_status,
          'order_id' => $record->order_id,
          'customer_id' => $record->customer_id,
          'customer_name' => $customer_name,
          'brand_name' => $brandName,
          'invoice_number' => $invoiceNumberData,
          'inv_no' => $record->inv_no,
          'grand_total' => $record->grand_total,
          'paid' => $record->paid,
          'remaining' => $record->remaining,
          'owe' => 'owe',
          'paid_date' => Carbon::parse($record->paid_date)->format('m/d/Y'),
          'is_paid' => $isPaidbtn,
          'action' => $btn,
          'rowClass' => $status
        );
      }
      return Datatables::of($data_arr)
        ->addIndexColumn()
        ->addColumn('rowClass', function ($row) {
          return $row['rowClass'];
        })
        ->addColumn('check_box', function ($row) {
          return $row['check_box'];
        })
        ->addColumn('created_at', function ($row) {
          return $row['created_at'];
        })
        ->addColumn('order_number', function ($row) {
          return $row['order_id'];
        })
        // ->addColumn('batch_no', function ($row) {
        //   return $row->orders->batch_no;
        // })
        ->addColumn('invoice_number', function ($row) {
          if ($row['order_status'] == 4) {
            return '<a href="#" class="text-center py-2">'.$row['customer_name'].'-invoice-no-'.$row['order_id'].'</a>';
          } else {
            return '<a href="#" class="text-center py-2" onclick="window.open(`invoice/order/details/'.$row['order_id'].'`, ``, `width=1300, height=700`)";>'.$row['customer_name'].'-invoice-no-'.$row['order_id'].'</a>';
          }
        })
        ->addColumn('customer_name', function ($row) {
          return $row['customer_name'];
        })
        ->addColumn('brand_name', function ($row) {
          return $row['brand_name'];
        })
        ->addColumn('grand_total', function ($row) {
          return ("$" . number_format($row['grand_total'], 2));
        })
        ->addColumn('paid', function ($row) {
          return ("$" . number_format($row['paid']. 2));
        })
        ->addColumn('remaining', function ($row) {
          return ("$" . number_format($row['remaining'], 2));
        })
        ->addColumn('paid_date', function ($row) {
          if ($row['paid_date'] != NULL) {
            return $row['paid_date'];
          } else {
            return '';
          }
        })
        ->addColumn('is_paid', function ($row) {
          return $row['is_paid'];
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
        ->rawColumns(['check_box', 'batch_no', 'order_number', 'invoice_number', 'is_paid', 'action', 'invoice_checkbox', 'status'])
        ->make(true);
    }
    $batch_nos = Orders::where('status', '!=', 4)->get();
    $customers = Customers::get();
    $brands = Labels::get();
    return view('admin.invoices.invoice', compact('batch_nos', 'customers', 'brands'));
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
  public function updateInvoiceStatus(Request $request)
  {
    $invoiceDetail = Invoices::where('id', $request->invoice_id)->first();
    if (isset($invoiceDetail)) {
      if ($request->is_paid == 0) {
        if ($invoiceDetail->paid != $invoiceDetail->grand_total) {
          $prevPaidAmount = $invoiceDetail->paid;
          $totalAmount = $invoiceDetail->grand_total;
          $newPaidAmount = 0;
          $invoiceDetail->is_paid = 0;
          $invoiceDetail->paid = 0;
          $invoiceDetail->remaining = 0;
          $invoiceDetail->paid_date = NULL;
          $invoiceDetail->save();
          InvoicePayment::create([
            'invoice_id' => $request->invoice_id,
            'paid' => $newPaidAmount,
            'remaining' => $totalAmount,
            'paid_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'paid_status' => '0' // only when changed to un paid else 1
          ]);
          return response()->json(['status' => true, 'message' => 'Updated Successfully']);
        } else if ($invoiceDetail->paid == $invoiceDetail->grand_total) {
          InvoicePayment::create([
            'invoice_id' => $request->invoice_id,
            'paid' => 0,
            'remaining' => $invoiceDetail->grand_total,
            'paid_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'paid_status' => '0' // only when changed to un paid else 1
          ]);
          $invoiceDetail->is_paid = 0;
          $invoiceDetail->paid = 0;
          $invoiceDetail->paid_date = NULL;
          $invoiceDetail->remaining = 0;
          $invoiceDetail->save();
          return response()->json(['status' => true, 'message' => 'Updated Successfully']);
        }
      } else if ($request->is_paid == 1) {
        if ($invoiceDetail->is_paid != 1) {
          if ($invoiceDetail->paid != $invoiceDetail->grand_total) {
            $prevPaidAmount = $invoiceDetail->paid;
            $totalAmount = $invoiceDetail->grand_total;
            $newPaidAmount = $totalAmount - $prevPaidAmount;
            $invoiceDetail->is_paid = $request->is_paid;
            $invoiceDetail->paid = $totalAmount;
            $invoiceDetail->remaining = $totalAmount - ($prevPaidAmount + $newPaidAmount);
            $invoiceDetail->paid_date = Carbon::now()->format('Y-m-d');
            $invoiceDetail->save();
            InvoicePayment::create([
              'invoice_id' => $request->invoice_id,
              'paid' => $newPaidAmount,
              'remaining' => $totalAmount - ($prevPaidAmount + $newPaidAmount),
              'paid_date' => Carbon::now()->format('Y-m-d')
            ]);
            return response()->json(['status' => true, 'message' => 'Updated Successfully']);
          } else if ($invoiceDetail->paid == $invoiceDetail->grand_total) {
            $invoiceDetail->is_paid = $request->is_paid;
            $invoiceDetail->save();
            return response()->json(['status' => true, 'message' => 'Updated Successfully']);
          }
        } else {
          return response()->json(['status' => false, 'message' => 'Already Paid']);
        }
      } else if ($request->is_paid == 2) {
        if ($invoiceDetail->is_paid != 1) {
          if (($request->amount + $invoiceDetail->paid) <= $invoiceDetail->grand_total) {
            $prevPaidAmount = $invoiceDetail->paid;
            $totalAmount = $invoiceDetail->grand_total;
            $newPaidAmount = $request->amount;
            $invoiceDetail->is_paid = $request->is_paid;
            $invoiceDetail->paid = $newPaidAmount + $prevPaidAmount;
            $invoiceDetail->remaining = $totalAmount - ($prevPaidAmount + $newPaidAmount);
            $invoiceDetail->paid_date = Carbon::now()->format('Y-m-d');
            if (($newPaidAmount + $prevPaidAmount) == $totalAmount) {
              $invoiceDetail->is_paid = 1;
            }
            $invoiceDetail->save();
            InvoicePayment::create([
              'invoice_id' => $request->invoice_id,
              'paid' => $newPaidAmount,
              'remaining' => $totalAmount - ($prevPaidAmount + $newPaidAmount),
              'paid_date' => Carbon::now()->format('Y-m-d')
            ]);
            return response()->json(['status' => true, 'message' => 'Updated Successfully']);
          } else {
            return response()->json(['status' => false, 'message' => 'Check Amounts']);
          }
        } else {
          return response()->json(['status' => false, 'message' => 'Already Paid']);
        }
      }
    }
  }
  public function updatePartialPayments(Request $request)
  {
    try {
      $isPaidStatus = $request->is_paid;
      $amount = $request->amount;
      $newPaidAmount = 0;
      $deletePrevCheck = $request->del_prev_amounts;
      $getPassword = User::find(1);
      $partialPayment = InvoicePayment::where('id', $request->row_id)->first();
      $totalAmount = Invoices::where('id', $request->invoice_id)->first();
      if (Hash::check($request->password, $getPassword->password)) {
        if (isset($partialPayment)) {
          if (isset($totalAmount)) {
            if (($request->amount) > $totalAmount->grand_total) {
              return response()->json(['status' => false, 'message' => 'Check Payments']);
            } else {
              $alreadyPaid = $partialPayment->paid;
              if ($request->amount > $alreadyPaid) {
                $newPaidAmount = $request->amount - $alreadyPaid;
                $invoiceDetail = Invoices::where('id', $request->invoice_id)->first();
                if (isset($invoiceDetail)) {
                  if ($invoiceDetail->grand_total == $invoiceDetail->paid) {
                    return response()->json(['status' => false, 'message' => 'Already Full Amount Paid']);
                  } else {
                    $prevPaidAmount = $invoiceDetail->paid;
                    $invoiceDetail->paid = $prevPaidAmount + $newPaidAmount;
                    $invoiceDetail->is_paid = $request->is_paid;
                    $invoiceDetail->remaining = $invoiceDetail->remaining - $newPaidAmount;
                    $invoiceDetail->save();
                    $partialPayment->paid = $request->amount;
                    $partialPayment->remaining =  0.00;
                    $partialPayment->save();
                    return response()->json(['status' => true, 'message' => 'Updated successfully']);
                  }
                }
              } else if ($request->amount < $alreadyPaid) {
                $extraPaid = $alreadyPaid - $request->amount;
                $invoiceDetail = Invoices::where('id', $request->invoice_id)->first();
                if (isset($invoiceDetail)) {
                  $prevPaidAmount = $invoiceDetail->paid;
                  $invoiceDetail->paid = $prevPaidAmount - $extraPaid;
                  $invoiceDetail->is_paid = $request->is_paid;
                  $invoiceDetail->remaining = $invoiceDetail->remaining + $extraPaid;
                  $invoiceDetail->save();
                  $partialPayment->paid = $request->amount;
                  $partialPayment->remaining = 0.00;
                  $partialPayment->save();
                  return response()->json(['status' => true, 'message' => 'Updated successfully']);
                } else {
                  return response()->json(['status' => false, 'message' => 'Something went wrong']);
                }
              } else {
                return response()->json(['status' => true, 'message' => 'Updated successfully']);
              }
            }
          }
        }
      } else {
        return response()->json(['status' => 'password_error', 'message' => 'Password did not match']);
      }
    } catch (\Throwable $th) {
      return response()->json(['status' => false, 'message' => 'Something went wrong']);
    }
  }
  public function invoiceDetails($invoiceId)
  {
    $data['invoiceMainData'] = Invoices::where('id', $invoiceId)->first();
    $orderId = $data['invoiceMainData']->order_id;
    $data['orderMainData'] = Orders::where('id', $orderId)->first();
    $data['customerData'] = Customers::where('id', $data['orderMainData']->customer_id)->first();
    $data['labelsData'] = Labels::where('customer_id', $data['orderMainData']->customer_id)->first();
    $data['orderDetailData'] = OrderDetails::where('order_id', $orderId)->with("Product")->get();
    $data['shippingData'] = OrderShippingInfo::where('order_id', $orderId)->first();
    $data['countryName'] = Countries::where('id', $data['shippingData']->country_id)->first();
    $data['stateName'] = States::where('id', $data['shippingData']->state_id)->first();
    $data['cityName'] = Cities::where('id', $data['shippingData']->city_id)->first();
    $data['orderDetailsData'] = OrderDetails::where('order_id', $orderId)->get();
    // return view('admin.invoices.invoiceDetails')->with($data);
  }
  public function orderInvoiceDetails(Request $request, $orderId)
  {
    // dd($orderId);
    $orderInvoiceId = Invoices::where('order_id', $orderId)->first();
    $data['invoiceMainData'] = Invoices::where('invoice_number', $orderInvoiceId->invoice_number)->first();
    $data['orderMainData'] = Orders::where('id', $orderId)->first();
    $brandId = $data['orderMainData']['brand_id'];
    $brand = Labels::withTrashed()->where('id', $data['orderMainData']['brand_id'])->first();
    $brandName = 'Not exists';
    if (isset($brand)) {
      $brandName = $brand->brand;
    }
    $data['customerData'] = Customers::withTrashed()->where('id', $data['orderMainData']->customer_id)->first();
    $data['labelsData'] = Labels::withTrashed()->where('customer_id', $data['orderMainData']->customer_id)->first();
    $query = OrderDetails::where('order_id', $orderId)->where('qty', '!=', '0')->with("sku_order");
    $data['orderDetailData'] = $query->get();
    $orders = OrderDetails::where('order_id', $orderId)
      ->where('qty', '!=', '0')->with("sku_order");
    $orders = $orders->get();
    $skuproducts = array();
    foreach ($orders as $order) {
      if (isset($order)) {
        // $sku_products = $order->sku_order->sku_product;
        $sku_products = SkuOrderDetails::where('sku_id', $order->sku_id)->where('order_id', $order->order_id)->get();
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
          $productOrderDetail = ProductOrderDetail::where('order_id', $orderId)->where('product_id', $u)->first();
          $sellerCostStatus = 0;
          if (isset($productOrderDetail)) {
            if ($productOrderDetail->seller_cost_status == 0) {
              if (isset($order)) {
                // $sku_products = $order->sku_order->sku_product;
                $sku_products = SkuOrderDetails::where('sku_id', $order->sku_id)->where('order_id', $order->order_id)->get();
                foreach ($sku_products as $skuproduct) {
                  if ($skuproduct->product_id == $u) {
                    $qty = $qty + $order->qty;
                    $price = 0;
                    $sellerCostStatus = $skuproduct->seller_cost_status;
                  }
                }
              }
              array_push($quantity, $qty, $price * $qty, $sellerCostStatus);
            } else {
              if (isset($order)) {
                // $sku_products = $order->sku_order->sku_product;
                $sku_products = SkuOrderDetails::where('sku_id', $order->sku_id)->where('order_id', $order->order_id)->get();
                foreach ($sku_products as $skuproduct) {
                  if ($skuproduct->product_id == $u) {
                    $qty = $qty + $order->qty;
                    $price = $skuproduct->selling_cost;
                    $sellerCostStatus = $skuproduct->seller_cost_status;
                  }
                }
              }
              array_push($quantity, $qty, $price * $qty, $sellerCostStatus);
            }
          } else {
            if (isset($order)) {
              // $sku_products = $order->sku_order->sku_product;
              $sku_products = SkuOrderDetails::where('sku_id', $order->sku_id)->where('order_id', $order->order_id)->get();
              foreach ($sku_products as $skuproduct) {
                if ($skuproduct->product_id == $u) {
                  $qty = $qty + $order->qty;
                  $price = $skuproduct->selling_cost;
                  $sellerCostStatus = $skuproduct->seller_cost_status;
                }
              }
            }
            array_push($quantity, $qty, $price * $qty, $sellerCostStatus);
          }
        }
        array_push($products, array_combine($keys, [$product_data->id, $product_data->name, $quantity, $product_data->price]));
      }
    }
    return view('admin.invoices.invoiceDetails', compact('products', 'orderId', 'brandId', 'brandName'))->with($data);
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
  public function edit($id)
  {
    //
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $order_id = $id;
    $status_id = $request->input('status_id');
    $notes = $request->input('order_notes');
    Orders::where('id', $order_id)->where('status', '!=', 4)->update(array('status' => $status_id, 'notes' => $notes));
    if ($status_id == 4) {
      $orderDetailData = OrderDetails::select('*')->where('order_id', $order_id)->get();
      foreach ($orderDetailData as $value) {
        $productInfo = Inventory::select('*')->where('product_id', $value->product_id)->first();
        $newQty = $value->qty + $productInfo->qty;
        Inventory::where('product_id', $value->product_id)->update(array('qty' => $newQty));
      }
    }
    return redirect()->back();
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

  public function createMergeInvoice(Request $request)
  {
    $customerOrders = $request->orderIds;
    $updateRunningStatus = Orders::whereIn('id', $customerOrders)->update([
      'merge_running' => 1,
    ]);

    $invoice = Invoices::whereIn('order_id', $customerOrders)->update([
      'merged' => 1
    ]);
    MergeInvoiceJob::dispatch($request->all());
    return response()->json(['message' => true, 'data' => 'Invoices will be merged shortly']);
  }

  public function mergeCustomerAllInvoices(Request $request)
  {
    $customer_id = $request->customer_id;

    $customerOrders = [];

    if (!empty($request['brand'])) {

      $customerOrders = Orders::where('merged', 0)->where('merge_running', 0)->orderBy('id', 'ASC')->where('customer_id', $customer_id)->where('brand_id', $request['brand'])->where('status', '!=', 4);

    } else {

      $customerOrders = Orders::where('merged', 0)->where('merge_running', 0)->orderBy('id', 'ASC')->where('customer_id', $customer_id)->where('status', '!=', 4);
    }

    if (!empty($request['from'])) {

      $customerOrders = $customerOrders->whereDate('created_at', '>=', Carbon::parse($request['from'])->format('Y-m-d'));

    if (!empty($request['to'])) {

        $customerOrders = $customerOrders->whereDate('created_at', '<=', Carbon::parse($request['to'])->format('Y-m-d'));

    } else {

        $customerOrders = $customerOrders->whereDate('created_at', '<=', Carbon::now()->format('Y-m-d'));

    }
    }
    $customerOrders = $customerOrders->get()->pluck('id')->toArray();

    $updateRunningStatus = Orders::whereIn('id', $customerOrders)->update([
      'merge_running' => 1,
    ]);

    $invoice = Invoices::whereIn('order_id', $customerOrders)->update([
      'merged' => 1
    ]);

    $allRequests = $request->all();
    $allRequests['all_customer_orders'] = $customerOrders;

    MergeCustomerAllInvoicesJob::dispatch($allRequests);

    return response()->json(['message' => true, 'data' => 'Invoices will be merged shortly']);
  }

  public function viewMergedInvoices(Request $request)
  {
    $mergedInvoices = MergedInvoice::orderBy('created_at', 'DESC');
    if (!empty($request->from_date)) {
      $mergedInvoices = $mergedInvoices->whereDate('created_at', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
      if (!empty($request->to_date)) {
        $mergedInvoices = $mergedInvoices->whereDate('created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
      }
    }
    if (!empty($request->customer)) {
      $mergedInvoices = $mergedInvoices->where('customer_id', $request->customer);
    }
    $mergedInvoices = $mergedInvoices->get();
    if ($request->ajax()) {
      return Datatables::of($mergedInvoices)
      ->addIndexColumn()
      ->addColumn('date', function ($row) {
        return $row->created_at->format('m/d/Y');
      })
      ->addColumn('customer_name', function ($row) {
        $customer = Customers::where('id', $row['customer_id'])->first();
        return $customer->customer_name;
      })
      ->addColumn('invoices_dates', function ($row) {
        $totalInvoices = explode(',', $row->invoice_ids);
        $orders = Orders::whereIn('id', $totalInvoices)->get();
        $firstDate = $orders[0]->created_at;
        $lastDate = $orders[count($orders)-1]->created_at;
        return Carbon::parse($firstDate)->format('m/d/Y'). ' - ' .Carbon::parse($lastDate)->format('m/d/Y');
      })
      ->addColumn('total_invoices', function ($row) {
        $totalInvoices = explode(',', $row->invoice_ids);
        return count($totalInvoices);
      })
      ->addColumn('total_cost', function ($row) {
        return '$'.$row->total_cost;
      })
      ->addColumn('paid', function ($row) {
        return '$'.$row->total_cost;
      })
      ->addColumn('remaining', function ($row) {
        return '$'.$row->total_cost;
      })
      ->addColumn('action', function ($row) {
        return '
        <div class="dropdown">
            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i data-feather="more-vertical"></i>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="/merged-invoice-detail/'. $row->id .'">
                    <i data-feather="eye"></i>
                    <span>View Details</span>
                </a>
                <a class="dropdown-item" href="/edit-merged-invoices/'. $row->id .'">
                    <i data-feather="edit"></i>
                    <span>Un Merge</span>
                </a>
            </div>
        </div>';
      })
      ->rawColumns(['action'])
      ->make(true);
    }
    $customers = Customers::get();
    return view('admin.invoices.merged_invoices', compact('mergedInvoices', 'customers'));
  }

  public function mergedInvoiceDetail(Request $request, $id)
  {
    $detail = InvoicesMerged::with('merged_invoice.customer.service_charges', 'product.product_unit', 'invoice')->where('merged_invoice_id', $id)->groupBy('product_id')->where('product_qty', '>', '0')->get();
    return view('admin.invoices.merged_invoice_details', compact('detail', 'id'));
  }

  public function printMergedInvoice(Request $request)
  {
    return view('admin.invoices.print_merged_invoice');
  }

  public function truncateMergedInvoices()
  {
    MergedInvoice::where('id', '>', '43')->delete();
    InvoicesMerged::where('merged_invoice_id', '>', '43')->delete();
    $from = '2023-01-16';
    $to = '2023-01-22';
    Orders::where('merged', 1)->orWhere('merge_running', 1)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->update([
      'merged' => 0,
      'merge_running' => 0
    ]);
    Invoices::where('merged', 1)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->update([
      'merged' => 0
    ]);
    OrderReturnDetail::where('merged', 1)->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->update([
      'merged' => 0
    ]);
    return 'Updated';
    return redirect()->back();
  }

  public function editMergedInvoices(Request $request, $id)
  {
    $mergedInvoices = MergedInvoice::where('id', $id)->first();
    $data = array();
    $keys = array('id', 'order_id', 'invoice_number', 'inv_no', 'grand_total', 'customer_id', 'customer_name', 'is_paid', 'date');
    if (isset($mergedInvoices)) {
      $getIds = $mergedInvoices->invoice_ids;
      $invoiceIds = explode(',', $getIds);
      foreach ($invoiceIds as $ids) {
        $invoice = Invoices::with('Customer')->where('order_id', $ids)->first();
        if (isset($invoice)) {
          $customerName = '';
          if (isset($invoice->Customer)) {
            $customerName = $invoice->Customer->customer_name;
          }
          array_push($data, array_combine($keys, [$invoice->id, $invoice->order_id, $invoice->invoice_number, $mergedInvoices->inv_nos, $invoice->grand_total, $invoice->customer_id, $customerName, $invoice->is_paid, $invoice->created_at]));
        }
      }
    }
    if ($request->ajax()) {
      return Datatables::of($data)
      ->addIndexColumn()
      ->addColumn('invoice_checkbox', function ($row) {
        $checkbox = '<input type="checkbox" ';
          $checkbox .= 'checked title="Already Merged" class="mergedToolTip singleOrderCheck" style="border: 1px solid red"';
          $checkbox .= 'data-invoice-id="' . $row['id'] . '" data-singleorder-id="' . $row['id'] . '" data-invoice-order-id="' . $row['order_id'] . '" data-invoice-customer-id="' . $row['customer_id'] . '" data-invoice-number="' . $row['invoice_number'] . '" data-inv-no="'. $row['inv_no'] .'" ';
        $checkbox .= ' />';
        return $checkbox;
      })
      ->addColumn('date', function ($row) {
        return $row['date']->format('m/d/Y');
      })
      ->addColumn('id', function ($row) {
        return $row['id'];
      })
      ->addColumn('order_id', function ($row) {
        return $row['order_id'];
      })
      ->addColumn('invoice_number', function ($row) {
        return $row['customer_name'].'-invoice-no-'.$row['order_id'];
      })
      ->addColumn('total_cost', function ($row) {
        return $row['grand_total'];
      })
      ->addColumn('customer_name', function ($row) {
        return $row['customer_name'];
      })
      ->addColumn('is_paid', function ($row) {
        return $row['is_paid'];
      })
      ->rawColumns(['invoice_checkbox'])
      ->make(true);
    }
    return view('admin.invoices.edit_merged_invoices', compact('id'));
  }

  public function updateMergedInvoices(Request $request, $id)
  {
    if ($request->all_checked == 0 && $request->invoices_length == 0) {
      $allMergedInvoices = MergedInvoice::where('id', $id)->select(['invoice_ids'])->first();
      if (isset($allMergedInvoices)) {
        Orders::whereIn('id', explode(',', $allMergedInvoices->invoice_ids))->where('merged', 1)->update([
          'merged' => 0,
          'merge_running' => 0
        ]);
        Invoices::whereIn('order_id', explode(',', $allMergedInvoices->invoice_ids))->where('merged', 1)->update([
          'merged' => 0
        ]);
        InvoicesMerged::where('merged_invoice_id', $id)->delete();
        MergedInvoice::where('id', $id)->delete();
      }
      return response()->json(['message' => true, 'data' => 'Updated Successfully']);
    } else {
      if ($request->all_checked == 1 && $request->invoices_length > 0) {
        return response()->json(['message' => true, 'data' => 'Updated Successfully']);
      } else {
        if ((count(array_unique($request->customerIds)) === 1)) {
          DB::beginTransaction();
          try {
            $getLastInvNoOfCustomer = MergedInvoice::orderBy('created_at', 'DESC')->where('customer_id', $request->customerIds[0])->first();
            $custInvNo = 1;
            if (isset($getLastInvNoOfCustomer)) {
              $custInvNo = $getLastInvNoOfCustomer->inv_nos + 1;
            }
            $getDetail = MergedInvoice::where('id', $id)->first();
            if (isset($getDetail)) {
              $invoiceIds = explode(',', $getDetail->invoice_ids);
              for ($ind = 0; $ind < count($invoiceIds); $ind++) {
                InvoicesMerged::where('merged_invoice_id', $id)->delete();
                Orders::where('merged', 1)->where('id', $invoiceIds[$ind])->update([
                  'merged' => 0,
                  'merge_running' => 0
                ]);
                Invoices::where('merged', 1)->where('order_id', $invoiceIds[$ind])->update([
                  'merged' => 0
                ]);
              }
              MergedInvoice::where('id', $id)->delete();
            }
            $data = $request->customerIds;
            $brandName = '';
            $totalCost = 0;
            $labelQty = 0;
            $pickQty = 0;
            $packQty = 0;
            $mailerQty = 0;
            $postageQty = 0;
            $pickPackFlatQty = 0;
            $mailer_Charges = 0;
            $postage_Charges = 0;
            $mergedInvoices = array();
            $keys = array(
              'invoice_id',
              'inv_no',
              'order_id',
              'invoice_number',
              'customer_id',
              'brand_name',
              'total_cost',
              'label_qty',
              'pick_qty',
              'pack_qty',
              'pick_pack_flat_qty',
              'mailer_qty',
              'postage_qty',
              'mailer_charges',
              'postage_charges',
              'invoice_ordered_detai',
              'label_unit_cost',
              'pick_unit_cost',
              'pack_unit_cost',
              'pick_pack_flat_unit_cost',
              'mailer_unit_cost'
            );
            $labelUnitCharges = '';
            $pickUnitCharges = '';
            $packUnitCharges = '';
            $pickPackFlatUnitCharges = '';
            $mailerUnitCharges = '';
            $label_Qty = 0;
            $pick_Qty = 0;
            $pack_Qty = 0;
            $mailer_Qty = 0;
            $postage_Qty = 0;
            $pickPackFlat_Qty = 0;
            for ($i=0; $i < count($data); $i++) {
              $order = Orders::where('merged', 0)->with('Details.sku_order.sku_product')->where('id', $request->orderIds[$i])->where('status', '!=', 4)->first();
              $orderCustomerServiceCharges = json_decode($order->customer_service_charges);
              $getCustomerID = $order->customer_id;
              $is_active = 1;
              // foreach ($orders as $order) {
                $checkIterations = array();
                $checkIterationskeys = array('unit', 'iteration', 'cost');
                if (isset($order)) {
                  $brand = Labels::where('id', $order->brand_id)->first();
                  if (isset($brand)) {
                    $brandName = $brand->brand;
                  }
                  $totalCost = $order->total_cost;
                  $labelQty = $order->labelqty;
                  $pickQty = $order->pickqty;
                  $packQty = $order->packqty;
                  $pickPackFlatQty = $order->pick_pack_flat_qty;
                  $mailerQty = $order->mailerqty;
                  $postageQty = $order->postageqty;
                  // Order Details
                  $orderDetails = $order->Details;
                  $orderDetailArray = array();
                  $orderDetailKeys = array(
                    'sku_id',
                    'sku_order_qty',
                    'sku_purchasing_cost',
                    'sku_selling_cost',
                    'label_charges',
                    'pick_charges',
                    'pack_charges',
                    'pick_pack_flat_charges',
                    // 'mailer_charges',
                    // 'postage_charges',
                    'products'
                  );
                  foreach ($order->Details as $detail) {
                    $skuOrderedQty = 0;
                    $skuPurchasingCost = 0;
                    $skuSellingCost = 0;
                    $labelPrice = '0.00';
                    $pickPrice = '0.00';
                    $packPrice = '0.00';
                    $pickPackFlatPrice = '0.00';
                    if (isset($detail)) {
                      if ($detail->qty > 0) {
                        $skuOrderedQty = $detail->qty;
                        $skuPurchasingCost = $detail->sku_purchasing_cost;
                        $skuSellingCost = $detail->sku_selling_cost;
                        $serviceCharges = json_decode($detail->service_charges_detail);
                        $mailerPrice = 0;
                        $postagePrice = 0;
                        foreach ($serviceCharges as $charge) {
                            if ($charge->slug == 'labels_price') {
                              $labelPrice = $charge->price;
                            }
                            if ($charge->slug == 'pick_price') {
                              $pickPrice = $charge->price;
                            }
                            if ($charge->slug == 'pack_price') {
                              $packPrice = $charge->price;
                            }
                            if ($charge->slug == 'mailer_price') {
                              $mailerPrice = $charge->price;
                            }
                            if ($charge->slug == 'postage_price') {
                              $postagePrice = $charge->price;
                            }
                        }
                        $pickPackFlatPrice = $order->pick_pack_flat_price;
                      }
                      $products = array();
                      $productKeys = array('product_id', 'product_name', 'product_qty', 'selling_cost', 'product_price', 'invoice_id', 'order_id', 'invoice_number', 'label_unit_cost', 'pick_unit_cost', 'pack_unit_cost', 'pick_pack_flat_unit_cost', 'mailer_unit_cost', 'mailer_unit_qty', 'postage_unit_qty');
                      
                      $productQty = 0;
                      $prodSellingRate = 0;
                      $skuOrder = SkuOrder::with('sku_product')->where('sku_id', $detail->sku_id)->where('order_id', $detail->order_id)->first();
                      foreach ($skuOrder->sku_product as $skuProduct) {
                        $getCustomerProduct = CustomerProduct::where('customer_id', $getCustomerID)->where('product_id', $skuProduct->product_id)->first();
                        if (isset($getCustomerProduct)) {
                          $is_active = $getCustomerProduct->is_active;
                        }
                        $productName = '';
                        $product = Products::where('id', $skuProduct->product_id)->first();
                        if (isset($product)) {
                          $productName = $product->name;
                        }
                        $productQty = $skuOrderedQty * $skuProduct->quantity;
                        $prodSellingRate = $skuProduct->selling_cost;
    
                        $unitLabel = 0;
                        $unitPick = 0;
                        $unitPack = 0;
                        $unitPickPackFlat = 0;
                        $unitMailer = 0;
                        $unitMailerQty = 0;
                        $unitPostageQty = 0;
    
                        if ($detail->qty > 0) {
                          if ($is_active == 0) {
                            $unitLabel = $orderCustomerServiceCharges->labels * $detail->qty;
                          } else {
                            $unitLabel = 0.00;
                          }
                          $unitPick = $skuProduct->pick * $detail->qty;
                          $unitPack = $skuProduct->pack * $detail->qty;
                          $unitPickPackFlat = $order->pick_pack_flat_price;
                          $unitMailer = $skuOrder->mailer_cost;
                          $unitMailerQty = $order->mailerqty;
                          $unitPostageQty = $order->postageqty;
                        }
                        array_push($products, array_combine($productKeys, [
                          $skuProduct->product_id,
                          $productName,
                          $productQty,
                          $prodSellingRate,
                          ($skuProduct->selling_cost) * ($productQty),
                          $request->invoiceIds[$i],
                          $request->orderIds[$i],
                          $request->invoiceNumbers[$i],
                          $unitLabel,
                          $unitPick,
                          $unitPack,
                          $unitPickPackFlat,
                          $unitMailer,
                          $unitMailerQty,
                          $unitPostageQty,
                        ]));
                      }
                    }
                    array_push($orderDetailArray, array_combine($orderDetailKeys, [
                      $detail->sku_id,
                      $skuOrderedQty,
                      $skuPurchasingCost,
                      $skuSellingCost,
                      $labelPrice,
                      $pickPrice,
                      $packPrice,
                      $pickPackFlatPrice,
                      // $mailerPrice,
                      // $postagePrice,
                      $products,
                    ]));
                  }
                  $label_Qty += $order->labelqty;
                  $pick_Qty += $order->pickqty;
                  $pack_Qty += $order->packqty;
                  $pickPackFlat_Qty += $order->pick_pack_flat_qty;
                  $mailer_Qty += $order->mailerqty;
                  $postage_Qty += $order->postageqty;
                  $order->merged = 1;
                  $order->save();
                }
              // }
              array_push($mergedInvoices, array_combine($keys, [
                $request->invoiceIds[$i],
                $request->invNumbers[$i],
                $request->orderIds[$i],
                $request->invoiceNumbers[$i],
                $request->customerIds[$i],
                $brandName,
                $totalCost,
                $labelQty,
                $pickQty,
                $packQty,
                $pickPackFlatQty,
                $mailerQty,
                $postageQty,
                $mailerPrice,
                $postagePrice,
                $orderDetailArray,
                $orderCustomerServiceCharges->labels,
                $orderCustomerServiceCharges->pick,
                $orderCustomerServiceCharges->pack,
                $orderCustomerServiceCharges->pick_pack_flat,
                $orderCustomerServiceCharges->mailer,
              ]));
              $getOrder = Orders::where('id', $request->orderIds[$i])->where('status', '!=', 4)->first();
              if (isset($getOrder)) {
                $unitCharges = json_decode($order->customer_service_charges);
                if ($i == (count($data)-1)) {
                  $labelUnitCharges .= $unitCharges->labels;
                  $pickUnitCharges .= $unitCharges->pick;
                  $packUnitCharges .= $unitCharges->pack;
                  $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat;
                  $mailerUnitCharges .= $unitCharges->mailer;
                } else {
                  $labelUnitCharges .= $unitCharges->labels.',';
                  $pickUnitCharges .= $unitCharges->pick.',';
                  $packUnitCharges .= $unitCharges->pack.',';
                  $pickPackFlatUnitCharges .= $unitCharges->pick_pack_flat.',';
                  $mailerUnitCharges .= $unitCharges->mailer.',';
                }
              }
            }
            $customer_ID = 0;
            $total_Cost = 0;
            
            $label_Charges = 0;
            $pick_Charges = 0;
            $pack_Charges = 0;
            $pick_pack_flat_Charges = 0;
            $product_Name = '';
            $product_Qty = 0;
            $getLastInvNoOfCustomer = MergedInvoice::orderBy('created_at', 'DESC')->where('customer_id', $mergedInvoices[0]['customer_id'])->first();
            $custInvNo = 1;
            if (isset($getLastInvNoOfCustomer)) {
              $custInvNo = $getLastInvNoOfCustomer->inv_nos + 1;
            }
            for ($j=0; $j < count($mergedInvoices); $j++) {
              $customer_ID = $mergedInvoices[0]['customer_id'];
              $total_Cost = $total_Cost + $mergedInvoices[$j]['total_cost'];
              $mailer_Charges = $mailer_Charges + $mergedInvoices[$j]['mailer_charges'];
              $postage_Charges = $postage_Charges + $mergedInvoices[$j]['postage_charges'];
              for ($k=0; $k < count($mergedInvoices[$j]['invoice_ordered_detai']); $k++) { 
                $label_Charges = $label_Charges + $mergedInvoices[$j]['invoice_ordered_detai'][$k]['label_charges'];
                $pick_Charges = $pick_Charges + $mergedInvoices[$j]['invoice_ordered_detai'][$k]['pick_charges'];
                $pack_Charges = $pack_Charges + $mergedInvoices[$j]['invoice_ordered_detai'][$k]['pack_charges'];
                $pick_pack_flat_Charges = $pick_pack_flat_Charges + $mergedInvoices[$j]['invoice_ordered_detai'][$k]['pick_pack_flat_charges'];
                for ($m=0; $m < count($mergedInvoices[$j]['invoice_ordered_detai'][$k]['products']); $m++) { 
                  if ($mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['product_qty'] > 0) {
                    $invoicesMerged = InvoicesMerged::create([
                      'product_id' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['product_id'],
                      'product_qty' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['product_qty'],
                      'selling_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['selling_cost'],
                      'product_price' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['product_price'],
                      'invoice_id' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['invoice_id'],
                      'inv_no' => $custInvNo,
                      'order_id' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['order_id'],
                      'invoice_number' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['invoice_number'],
                      //
                      'label_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['label_unit_cost'],
                      'pick_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['pick_unit_cost'],
                      'pack_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['pack_unit_cost'],
                      'pick_pack_flat_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['pick_pack_flat_unit_cost'],
                      'mailer_unit_cost' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['mailer_unit_cost'],
                      'mailer_unit_qty' => $mergedInvoices[$j]['invoice_ordered_detai'][$k]['products'][$m]['mailer_unit_qty'],
                      //
                    ]);
                  }
                }
              }
            }
            $mergedInvoice = MergedInvoice::create([
              'invoice_ids' => implode(',', $request->orderIds),
              'customer_id' => $customer_ID,
              'total_cost' => $total_Cost,
              'label_qty' => $label_Qty,
              'pick_qty' => $pick_Qty,
              'pack_qty' => $pack_Qty,
              'flat_pick_pack_qty' => $pickPackFlat_Qty,
              'mailer_qty' => $mailer_Qty,
              'postage_qty' => $postage_Qty,
              'label_charges' => $label_Charges,
              'pick_charges' => $pick_Charges,
              'pack_charges' => $pack_Charges,
              //
              'mailer_charges' => $mailer_Charges,
              'postage_charges' => $postage_Charges,
              //
              'pick_pack_flat_charges' => $pick_pack_flat_Charges,
              'label_unit_cost' => $labelUnitCharges,
              'pick_unit_cost' => $pickUnitCharges,
              'pack_unit_cost' => $packUnitCharges,
              'mailer_unit_cost' => $mailerUnitCharges,
              
            ]);
            $mergedInvoice->inv_nos = $custInvNo;
            $mergedInvoice->save();
            $productMerged = InvoicesMerged::where('merged_invoice_id', '=', NULL)->update([
              'merged_invoice_id' => $mergedInvoice->id,
              'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            ]);
            DB::commit();
            return response()->json(['message' => true, 'data' => 'Updated Successfully']);
          }
          catch(\Exception $e)
          {
            DB::rollback();
            return view('admin.server_error');
            return response()->json(['message' => false, 'data' => 'Something went wrong']);
          }
        } else {
          return response()->json(['message' => false, 'data' => 'Customer should be same']);
        }
      }
    }
  }

  public function setInvoiceNumbers()
  {
    $invoices = Invoices::orderBy('customer_id', 'ASC')->get();
    foreach ($invoices as $key => $invoice) {
      if ($invoices[$key]['inv_no'] == NULL) {
        if (isset($invoices[$key-1])) {
          if ($invoices[$key-1]['customer_id'] == $invoices[$key]['customer_id']) {
            $invNo = $invoices[$key-1]['inv_no'];
            $invoice->inv_no = $invNo + 1;
            $invoice->save();
          } else{
            $invoice->inv_no = 1;
            $invoice->save();
          }
        } else {
          $invoice->inv_no = 1;
          $invoice->save();
        }
      }
    }
  }

  public function sendEmail(Request $request)
  {
    try {
      $confirmSendMail = $request->send_mail;
      $userEmail = $request->email;
      $userEmails = str_replace(',,', ',', $userEmail);
      $userEmails = str_replace(' ', '', $userEmails);
      $emails = explode(',', $userEmails);
      $detail = InvoicesMerged::with('merged_invoice.customer.service_charges', 'product.product_unit')->where('merged_invoice_id', $request->id)->groupBy('product_id')->where('product_qty', '>', '0')->get();
      if ($confirmSendMail == 'true') {
        $id = $request->id;
        $pdf = \PDF::loadView('admin.invoices.emailtemplate', compact('detail', 'id'));
        Mail::send('admin.invoices.emailtemplate', compact('detail', 'id'), function($message) use ($id, $detail, $pdf, $emails) {
          $message->to($emails)->cc(['warehousesystem@gmail.com','aleem.333@hotmail.com'])->subject('Mail from warehousesystem')->attachData($pdf->output(), 'invoice.pdf');
        });
        // Mail::to($emails)->cc(['warehousesystem@gmail.com','aleem.333@hotmail.com'])->send(new \App\Mail\MyTestMail($detail, $request->id));
      }
      return response()->json(['status' => true]);
    } catch (\Exception $e) {
      dd($e);
      return view('admin.server_error');
      return response()->json(['status' => false]);
    }
  }

  public function partialPayments(Request $request, $id)
  {
    if ($request->ajax()) {
      $partialPayments = InvoicePayment::with('invoice')->orderBy('created_at', 'ASC')->where('invoice_id', $id)->get();
      return Datatables::of($partialPayments)
      ->addIndexColumn()
      ->addColumn('date', function($row) {
        return Carbon::parse($row->paid_date)->format('m/d/Y');
      })
      ->addColumn('total', function($row) {
        if ($this->balance == 0 && $this->totalAmountNow == 0) {
          $this->totalAmountNow = $row->invoice->grand_total;
        } else {
          $this->totalAmountNow = $row->invoice->grand_total - $this->balance;
        }
        return '$'.number_format($this->totalAmountNow, 2);
        // return '$'.number_format($row->invoice->grand_total, 2);
      })
      ->addColumn('paid', function($row) {
        return '$'.number_format($row->paid, 2);
      })
      ->addColumn('remaining', function($row) {
        $this->balance += $row->paid;
        $remaining = $row->invoice->grand_total - $this->balance;
        return '$'.number_format($remaining, 2);
      })
      ->addColumn('action', function($row) {
        return '<a href="#" class="edit-partial-payment" title="Edit Amount" data-bs-target="#updateIsPaidStatusModal" data-bs-toggle="modal" data-id="'. $row->id .'" data-total-amount="'. $row->invoice->grand_total .'"><i data-feather="edit"></i></a>';
      })
      ->make(true);
    }
    return view('admin.invoices.partial_payments', compact('id'));
  }

  public function setInvoicePayments()
  {
    InvoicePayment::truncate();
    Invoices::where('paid_date', '!=', NULL)->orWhere('remaining', '!=', NULL)->orWhere('paid', '!=', NULL)->update([
      'is_paid' => 0.00,
      'paid' => 0.00,
      'remaining' => 0.00,
      'paid_date' => NULL
    ]);
    Invoices::where('grand_total', '')->update([
      'grand_total' => 0.00
    ]);
    return redirect()->back();
  }

}
