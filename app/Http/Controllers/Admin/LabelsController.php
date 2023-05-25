<?php

namespace App\Http\Controllers\Admin;

use Session;
use DateTime;
use Redirect;
use DataTables;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\SkuOrder;
use App\Traits\Forecast;
use App\Traits\LabelQty;
use App\AdminModels\Labels;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\Models\ServiceCharges;
use App\Models\CustomerProduct;
use App\Models\SkuOrderDetails;
use App\AdminModels\OrderDetails;
use App\Models\ProductLabelOrder;
use App\AdminModels\LabelsHistory;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductLabel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LabelsController extends Controller
{
  //
  use Forecast, LabelQty;
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
      // $columns = $request->get('columns');
      // $orders = $request->get('order');
      // $orderbyColAndDirection = [];
      // foreach ($orders as $key => $value) {
      //   array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      // }
      // $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      if (Auth::user()->hasRole('admin')) {
        if (Auth::user()->can('labels_view')) {
          $query = Labels::with('customer', 'customer.brands', 'cust_has_prod', 'cust_has_prod.products', 'customer.product')->where('deleted_at', NULL);
        } 
      }
      if (!Auth::user()->hasRole('admin')) {
        if (Auth::user()->can('labels_view')) {
          $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
          $customerId = Auth::user()->id;
          if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
          }
          $query = Labels::with('customer', 'customer.brands', 'cust_has_prod', 'cust_has_prod.products', 'customer.product')->where('deleted_at', NULL)->where('customer_id', $customerId);
        }
      }
      $query->whereHas('customer', function ($q) use ($request) {
        $q->where('customers.deleted_at', NULL);
        if (!empty($request->customer)) {
          $q->where('customer_name', 'LIKE', "%$request->customer%");
        }
      });
      if ($request->brand != '') {
        if (!empty($request->brand)) {
          $query->where('brand', 'LIKE', "%$request->brand%");
        }
      }
      $data = $query->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('mailer_cost', function ($row) {
          return '$' . number_format($row->mailer_cost, 2);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical"></i>
                        </button>
                      <div class="dropdown-menu">';
          if (Auth::user()->can('labels_create')) {
            $btn .= '<a class="dropdown-item add_brand_products" data-bs-target="#show_brand_product_modal" data-bs-toggle="modal" href="#" data-brand_id="' . $row->id . '" data-customer_id="' . $row->customer->id . '">
                            <i data-feather="plus"></i>
                            <span>Add Brand Product</span>
                        </a>';
          }
          if (Auth::user()->can('sku_view')) {
            $btn .= '<a class="dropdown-item" href="/brand/' . $row->id . '/sku">
                            <i data-feather="eye"></i>
                            <span>View SKU</span>
                        </a>';
          }
          if (Auth::user()->can('labels_update'))
          {
            $btn .= '<a class="dropdown-item" href="/brands/' . $row->id . '/edit">
                            <i data-feather="edit-2"></i>
                            <span>Edit Brand</span>
                        </a>';
          }
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<a class="dropdown-item" href="/delete/brand/' . $row->id . '" onclick="confirmDelete(event)">
                            <i data-feather="trash"></i>
                            <span>Delete Brand</span>
                        </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //
    if (Auth::user()->hasRole('admin')) {
      if (Auth::user()->can('customer_view')) {
        $customers = Customers::get();
      } 
    }
    if (!Auth::user()->hasRole('admin')) {
      if (Auth::user()->can('customer_view')) {
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $customers = Customers::where('id', $customerId)->get();
      }
    }
    $brands = Labels::get();
    return view('admin.labels.labels', compact('customers', 'brands'));
  }
  public function manageLabels(Request $request)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];
      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      if (Auth::user()->hasRole('admin')) {
        if (Auth::user()->can('labels_view')) {
          $query = Labels::with('customer.brands', 'cust_has_prod.products', 'customer.product')->where('deleted_at', NULL);
          $query->whereHas('customer', function ($q) use ($request) {
            $q->where('customers.deleted_at', NULL);
            if (!empty($request->customer)) {
              $q->where('customer_name', 'LIKE', "%$request->customer%");
            }
          });
          if ($request->brand != '') {
            if (!empty($request->brand)) {
              $query->where('brand', 'LIKE', "%$request->brand%");
            }
          }
        } 
      }
      if (!Auth::user()->hasRole('admin')) {
        if (Auth::user()->can('labels_view')) {
          $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
          $customerId = Auth::user()->id;
          if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
          }
          $query = Labels::with('customer.brands', 'cust_has_prod.products', 'customer.product', 'sku.sku_product.product.customerHasProduct')->where('deleted_at', NULL)
          ->where('customer_id', $customerId);
          $query->whereHas('customer', function ($q) use ($request) {
            $q->where('customers.deleted_at', NULL);
            if (!empty($request->customer)) {
              $q->where('customer_name', 'LIKE', "%$request->customer%");
            }
          });
          if ($request->brand != '') {
            if (!empty($request->brand)) {
              $query->where('brand', 'LIKE', "%$request->brand%");
            }
          }
        }
      }
      $data = $query->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->rawColumns(['action'])
        ->make(true);
    }
  }
  public function getLabelsData(Request $request)
  {
    $customer = Customers::with('brands')->where('id', $request->customer_id)->first();
    $id = $customer->id;
    $brand_id = $request->brand_id;
    $brand = Labels::where('id', $brand_id)->first();
    $brands = array();
    $keys1 = array('prod_id', 'prod_name', 'labels_qty', 'label_cost', 'status', 'forecast_labels');
    $keys2 = array('brand_name', 'brand_id', 'products');
    $products = array();
    $customer_products = CustomerHasProduct::where('customer_id', $id)->where('brand_id', $brand_id)->get();
    foreach ($customer_products as $c_product) {
      $product = Products::where('id', $c_product->product_id)->first();
      $html = $this->forecastLabels($request->customer_id, $brand_id, $c_product->product_id);
      if (isset($product)) {
        $customerProduct = CustomerHasProduct::where('customer_id', $customer->id)
          ->where('brand_id', $brand_id)
          ->where('product_id', $product->id)
          ->first();
        $labelCost = $c_product->label_cost;
        if ($labelCost == 0 || $labelCost == '') {
          $serviceCharges = ServiceCharges::where('customer_id', $customer->id)->first();
          if (isset($serviceCharges)) {
            $labelCost = $serviceCharges->labels;
          }
        }
        $labelqty = 0;
        $label_Qty = $c_product->label_qty;
        $newlabelQty = $this->getLabelQty($id, $brand_id, $c_product->product_id);
        if ($newlabelQty != null) {
          $labelqty = $newlabelQty;
        } else {
          $labelqty = $label_Qty;
        }
        array_push($products, array_combine($keys1, [$product->id, $product->name, number_format($labelqty), number_format($labelCost, 2), $c_product->is_active, $html['html']]));
      }
    }
    array_push($brands, array_combine($keys2, [$brand->brand, $brand->id, $products]));
    $custproducts = CustomerProduct::where('customer_id', $request->customer_id)->get();
    $arr = array();
    $keys = array('prod_id', 'prod_name', 'weight');
    foreach ($custproducts as $custproduct) {
      if (CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('product_id', $custproduct->product_id)->exists()) {
      } else {
        if (isset($custproduct)) {
          $prod = Products::where('id', $custproduct->product_id)->first();
          if (isset($prod)) {
            array_push($arr, array_combine($keys, [$prod->id, $prod->name, number_format($prod->weight, 2)]));
          }
        }
      }
    }
    return response()->json(['brands' => $brands, 'products' => $arr]);
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($customer_id = null, $brand = null)
  {
    $data['customer_id'] = $customer_id;
    $data['brand'] = $brand;
    $data['customers'] = Customers::All();
    $data['products'] = Products::select('*')
      ->where('is_active', 1)
      ->get();
    return view('admin.labels.add_label')->with($data);
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
      'customer_id' => 'required',
      'brand' => 'required',
    ]);
    DB::beginTransaction();
    try {
      $customer_id = $request->input('customer_id');
      $brand = $request->input('brand');
      $mailer_cost = $request->input('mailer_cost');
      $qty = $request->input('qty');
      if (Labels::where('brand', $request->brand)->exists()) {
        return redirect()->back()->withError('Label Name Already Exists');
      }
      // create brand
      $brand = Labels::create([
        'customer_id' => $request->input('customer_id'),
        'brand' => $request->input('brand'),
        'mailer_cost' => $mailer_cost,
        'qty' => '0',
        'date' => Carbon::now(),
        'deleted_at' => null,
      ]);
      // create label history
      $brand_history = LabelsHistory::create([
        'customer_id' => $request->input('customer_id'),
        'brand_id' => $brand->id,
        'user_id' => Auth::user()->id,
        'qty' => '0',
        'date' => Carbon::now(),
        'deleted_at' => null,
        'status' => 1
      ]);
      DB::commit();
      return redirect('/brands')->withSuccess('Brand has been added');
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return redirect()->back()->withError('Something went wrong');
    }
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
    $data['dataSet'] = Labels::find($id);
    $data['customers'] = Customers::All();
    return view('admin.labels.edit_label')->with($data);
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
    $validatedData = $request->validate([
      'customer_id' => 'required',
      'brand' => 'required',
    ]);
    DB::beginTransaction();
    try {
      // update brand
      $brand = Labels::find($id);
      $brand->customer_id = $request->input('customer_id');
      $brand->brand = $request->input('brand');
      $brand->mailer_cost = $request->input('mailer_cost');
      $brand->date = Carbon::now();
      $brand->save();
      DB::commit();
      return redirect('/brands')->withSuccess('Brand has been updated');
    } catch (\Exception $th) {
      DB::rollback();
      return view('admin.server_error');
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
    $loginUserData = Auth::user();
    $data = Labels::find($id);
    $data->delete();
    return redirect()->back()->withSuccess('Brand has been deleted');
  }
  public function trashLabels(Request $request)
  {
    if ($request->ajax()) {
      $data  = DB::table("labels")
        ->join('customers', 'labels.customer_id', '=', 'customers.id')
        ->select('labels.*', 'customers.customer_name')
        ->where('labels.deleted_at', '!=', null);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<p>
                          <a class="btn btn-primary waves-effect waves-float waves-light" href="/brands/restoreTrash/' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Restore">
                            Restore
                          </a>';
            $btn .= '</p>';
          }
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.labels.trashLabels');
  }
  public function restoreTrash($id)
  {
    Labels::withTrashed()->find($id)->restore();
    return back()->withSuccess('Brand has been restored');
  }
  public function labelsHistory(Request $request, $id = 0)
  {
    $pid = $request->input('pid');
    if ($request->ajax()) {
      $pid = $request->input('pid');
      //$data = Inventory::select(['inventory.*']);
      $data  = DB::table("labels_history")
        ->join('users', 'labels_history.user_id', '=', 'users.id')
        ->join('customers', 'labels_history.customer_id', '=', 'customers.id')
        ->select('labels_history.*', 'users.name as username', 'customers.customer_name')
        ->where('labels_history.customer_id', '=', $pid)
        ->where('labels_history.deleted_at', '=', null)
        ->orderByRaw('created_at DESC');
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('username', function ($row) {
          return ucwords($row->username);
        })
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand_id);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->addColumn('status', function ($row) {
          return $btn = '<a href="javascript:void(0)" type="button" class="btn btn-sm btn-primary revert" data-history-id="' . $row->id . '">
                        Revert
                    </a>';
        })
        ->rawColumns(['status'])
        ->make(true);
    }
    return view('admin.labels.labels_history')->with('product_id', $id);
  }
  public function addLabels(Request $request, $id)
  {
    // DB::beginTransaction();
    // try {
      $brand = Labels::where('id', $id)->first();
      if ($brand) {
        $brand->qty = $brand->qty + $request->labels;
        $brand->save();
        // create label history
        $brand_history = LabelsHistory::create([
          'customer_id' => $brand->customer_id,
          'brand_id' => $brand->id,
          'user_id' => Auth::user()->id,
          'qty' => $request->labels,
          'date' => Carbon::now(),
          'deleted_at' => null,
          'status' => 1
        ]);
        return response()->json([
          'success' => true,
          'status' => 'success',
          'message' => 'Labels added to ' . $brand->brand
        ], 200);
      } else {
        return response()->json([
          'success' => true,
          'status' => 'failed',
          'message' => 'Unable to find specified brand'
        ], 200);
      }
    //   DB::commit();

    // } catch (\Throwable $th) {
    //   DB::rollback();
        // return view('admin.server_error');
    //   return response()->json(['error' => true, 'message' => 'Something went wrong']);
    // }
  }
  public function revertLabels($id)
  {
    $history = LabelsHistory::find($id);
    if ($history) {
      $brand = Labels::where('id', $history->brand_id)->withTrashed()->first();
      $brand->qty = $brand->qty - $history->qty;
      $brand->save();
      $history->delete();
      return response()->json([
        'status' => 'success',
        'message' => 'Labels are reverted'
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Error while reverting labels'
      ], 200);
    }
  }
  public function getProductLabels(Request $request, $id)
  {
    $labels = CustomerHasProduct::where('product_id', $id)->where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id);
    if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $id)->exists()) {
      $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $id)->first();
      if (isset($mergedProduct)) {
        $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $id)->sum('merged_qty');
      }
    } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $id)->exists()) {
      $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $id)->first();
      if (isset($mergedProduct)) {
        $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $id)->sum('merged_qty');
      }
    } else {
      $labels = $labels->sum('label_qty');
    }
    // if ($labels->first()->merged_qty != NULL) {
    //   $labels = $labels->sum('merged_qty');
    // } else {
    //   $labels = $labels->sum('label_qty');
    // }
    return response()->json($labels);
  }
  public function addLabelToProduct(Request $request)
  {
    DB::beginTransaction();
    try {
      if ($request->qty < 0) {
        return redirect()->back()->withError('Cant Add negative Value');
      }
      $cust_has_prod = CustomerHasProduct::where('customer_id', $request->customer_id)
        ->where('product_id', $request->product_id)
        ->where('brand_id', $request->brand_id);
      if ($cust_has_prod->exists()) {
        $cust_has_prod = $cust_has_prod->first();
        if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty = $request->qty + $labels->merged_qty;
              $labels->save();
            }
          }
        } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty = $request->qty + $labels->merged_qty;
              $labels->save();
            }
          }
        } else {
          $cust_has_prod->label_qty = $request->qty + $cust_has_prod->label_qty;
          $cust_has_prod->save();
        }
        CustomerProductLabel::create([
          'customer_id' => $request->customer_id,
          'product_id' => $request->product_id,
          'brand_id' => $request->brand_id,
          'label_qty' => $request->qty,
          'user_id' => Auth::user()->id
        ]);
      } else {
        CustomerHasProduct::create([
          'customer_id' => $request->customer_id,
          'product_id' => $request->product_id,
          'brand_id' => $request->brand_id,
          'label_qty' => $request->qty
        ]);
      }
      CustomerProductLabel::create([
        'customer_id' => $request->customer_id,
        'product_id' => $request->product_id,
        'brand_id' => $request->brand_id,
        'label_qty' => $request->qty,
        'user_id' => Auth::user()->id
      ]);
      $cust_has_prod = CustomerHasProduct::where('customer_id', $request->customer_id)
      ->where('product_id', $request->product_id)
      ->where('brand_id', $request->brand_id)->first();
      $cust_prod = CustomerProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id);
      if ($cust_prod->exists()) {
        $cust_prod = $cust_prod->first();
        $labelqty = 0;
        if ($cust_has_prod->label_qty == NULL) {
          $labelqty = 0;
        } else {
          $labelqty = $cust_has_prod->label_qty;
        }
        $cust_prod->label_qty = $labelqty;
        $cust_prod->save();
      } else {
        CustomerProduct::create([
          'customer_id' => $request->customer_id,
          'product_id' => $request->product_id,
          'label_qty' => $request->qty
        ]);
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Label added Successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function reduceLabelToProduct(Request $request)
  {
    DB::beginTransaction();
    try {
      if ($request->qty < 0) {
        return response()->json(['error' => true, 'message' => 'Cant add negative value']);
      }
      $cust_prod = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id);
      if ($cust_prod->exists()) {
        $cust_prod = $cust_prod->first();
        if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty =  $labels->merged_qty - $request->qty;
              $labels->save();
            }
          }
        } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty =  $labels->merged_qty - $request->qty;
              $labels->save();
            }
          }
        } else {
          $cust_prod->label_qty = ((int)$cust_prod->label_qty) - ((int)$request->qty);
          $cust_prod->save();
        }
      }
      $cust_prod = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id)->first();
      $custprod = CustomerProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id);
      if ($custprod->exists()) {
        $custprod = $custprod->first();
        if ($custprod->label_qty < $request->qty) {
          return response()->json(['error' => true, 'message' => 'Cant add greater quantity']);
        } else {
          $custprod->label_qty = $cust_prod->label_qty;
          $custprod->save();
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Label quantity reduced']);
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function addLabelCostToProduct(Request $request)
  {
    DB::beginTransaction();
    try {
      $cust_prod = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id)->first();
      if (isset($cust_prod)) {
        $label_cost = $cust_prod->label_cost;
        CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id)->update([
          'label_cost' => $request->label_cost
        ]);
      }
      $custProd = CustomerProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->first();
      if (isset($custProd)) {
        $custProd->label_cost = $request->label_cost;
        $custProd->save();
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Label cost added successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function productLabelsHistory(Request $request, $c_id, $b_id, $id)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];
      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $data = DB::table('customer_product_labels')
        ->join('users', 'customer_product_labels.user_id', '=', 'users.id')
        ->join('products', 'customer_product_labels.product_id', '=', 'products.id')
        ->select('users.name as username', 'products.name as p_name', 'customer_product_labels.label_qty as label_qty', 'customer_product_labels.id as plabelId', 'customer_product_labels.created_at as date')
        ->where('products.id', '=', $id)
        ->where('customer_product_labels.customer_id', $c_id)
        ->where('customer_product_labels.brand_id', $b_id)
        ->where('customer_product_labels.label_qty', '!=', '0')
        // ->where('deleted_at', '=', NULL)
        ->orderByRaw($orderbyColAndDirection);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('username', function ($row) {
          return ucwords($row->username);
        })
        ->addColumn('name', function ($row) {
          $name = '';
          if ($row->p_name == '' || $row->p_name == null) {
            $name = '';
          } else {
            $name = $row->p_name;
          }
          return ucwords($name);
        })
        ->addColumn('qty', function ($row) {
          $labelQty = $row->label_qty;
          if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = $mergedProduct->merged_qty;
            }
          } else if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = $mergedProduct->merged_qty;
            }
          } else {
            $labelQty = $row->label_qty;
          }
          return number_format($labelQty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i data-feather="more-vertical"></i>
                    </button>
                    <div class="dropdown-menu">';
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<a class="dropdown-item" href="/delete_prod_labels_history/' . $row->plabelId . '" onclick="confirmDelete(event)">
                        <i data-feather="trash"></i>
                        <span>Delete Label</span>
                      </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action'])
        ->filter(function ($instance) use ($request) {
        })
        ->make(true);
    }
    return view('admin.sku.product_labels_history');
  }
  public function deleteProdLabelsHistory($id)
  {
    $cprod_label = CustomerProductLabel::find($id);
    $cprod_label_qty = $cprod_label->label_qty;
    $prod_label = CustomerHasProduct::where('customer_id', $cprod_label->customer_id)
      ->where('product_id', $cprod_label->product_id)
      ->where('brand_id', $cprod_label->brand_id)->first();
    $prod_label_qty = $prod_label->label_qty;
    $res = $prod_label_qty - $cprod_label_qty;
    $prod_label->label_qty = $res;
    $prod_label->save();
    $cprod_label->delete();
    return redirect()->back()->withSuccess('Label has been deleted');
  }
  public function trashProductLabelsHistory(Request $request)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];
      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $data = DB::table('customer_has_products')
        ->join('products', 'customer_has_products.product_id', '=', 'products.id')
        ->select('products.name as p_name', 'customer_has_products.label_qty as label_qty', 'customer_has_products.id as plabelId', 'customer_has_products.created_at as date')
        ->where('deleted_at', '!=', NULL)->orderByRaw($orderbyColAndDirection);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          $name = '';
          if ($row->p_name == '' || $row->p_name == null) {
            $name = '';
          } else {
            $name = $row->p_name;
          }
          return ucwords($name);
        })
        ->addColumn('qty', function ($row) {
          $labelQty = $row->label_qty;
          if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = $mergedProduct->merged_qty;
            }
          } else if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = $mergedProduct->merged_qty;
            }
          } else {
            $labelQty = $row->label_qty;
          }
          return number_format($labelQty);

          // return number_format($row->label_qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("m/d/Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i data-feather="more-vertical"></i>
                      </button>
                      <div class="dropdown-menu">';
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<a class="dropdown-item" href="/revert_product_label/' . $row->plabelId . '">
                          <i data-feather="trash"></i>
                          <span>Revert</span>
                      </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action'])
        ->filter(function ($instance) use ($request) {
        })
        ->make(true);
    }
    //
    return view('admin.sku.trash_product_labels_history');
  }
  public function revertProductLabel($id)
  {
    $trashed = CustomerHasProduct::where('id', $id)->withTrashed()->update(['deleted_at' => NULL]);
    return redirect()->back()->withSuccess('Label has been Reverted');
  }
  public function getBrandProducts(Request $request)
  {
    $custproducts = CustomerProduct::where('customer_id', $request->customer_id)->get();
    $arr = array();
    $keys = array('prod_id', 'prod_name', 'weight');
    foreach ($custproducts as $custproduct) {
      if (CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('product_id', $custproduct->product_id)->exists()) {
      } else {
        if (isset($custproduct)) {
          $prod = Products::with('inventory')->where('id', $custproduct->product_id)->first();
          if (isset($prod)) {
            array_push($arr, array_combine($keys, [$prod->id, $prod->name, $prod->weight]));
          }
        }
      }
    }
    return response()->json($arr);
  }
  public function getForecastLabel(Request $request) 
  {
    if (Auth::user()->can('labels_view')) {
      if (!empty($request->min_date)) {
          if ($request->max_date == null) {
              $request->merge(['max_date' => Carbon::now()->format('Y-m-d')]);
          } else {
              $request->max_date = $request->max_date;
          }
      }
      $query = DB::table('customers')
                ->join('customer_has_products', 'customers.id', '=', 'customer_has_products.customer_id');
      if (!empty($request->customer)) {
        $query = $query->where('customers.id', '=', $request->customer);
      }
      if (!empty($request->brand)) {
        $query = $query->where('customer_has_products.brand_id', '=', $request->brand);
      }
      $query = $query->select('customer_has_products.*', 'customers.customer_name', 'customers.id as cust_id');
      $data = $query->where('customer_has_products.is_active', '=', '0')->where('customers.deleted_at', null)->where('customer_has_products.deleted_at', null)->get();
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
        $data = $query->whereBetween('customer_has_products.created_at', [$request->min_date, $max]);
      }
      $data = $query->where('customer_has_products.is_active', '=', '0')->where('customers.deleted_at', null)->where('customer_has_products.deleted_at', null)->get();
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
        $product = Products::where('id', $row->product_id)->select(['name'])->first();
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
      ->addColumn('forecast_days', function ($row) use ($request) {
          // $htmld = $this->forecastLabels($row->customer_id, $row->brand_id, $row->product_id, $request->days_filter);
          return $row->forecastdays;
      })
      ->rawColumns(['forecast_days'])
      ->make(true);
  }
  public function labelsForecast(Request $request)
  {
    if ($request->ajax()) {
      return $this->getForecastLabel($request);
    }
    $customers = Customers::get();
    $brands = Labels::get();
    return view('admin.reports.labelforecast', compact('customers', 'brands'));
  }
  public function getBrandMailer(Request $request)
  {
    $brand = Labels::where('id', $request->brandId)->first();
    $mailer_cost = $brand->mailer_cost;
    if ($mailer_cost == NULL || $mailer_cost == 0) {
      $mailer_cost = 0.00;
    }
    return response()->json($mailer_cost);
  }
  public function getEditBrandMailer(Request $request)
  {
    $mailer_cost = Setting::first()->mailer;
    $serviceCharge = ServiceCharges::where('customer_id', $request->customer_id)->first();
    $brand = SkuOrder::where('brand_id', $request->brand_id)->where('customer_id', $request->customer_id)
    ->where('order_id', $request->order_id)->first();
    $order_details = OrderDetails::where('order_id', $request->order_id)->get();
    foreach ($order_details as $key => $detail) {
      $mailerCost = 0;
      $charges = json_decode($detail->service_charges_detail);
      foreach ($charges as $ckey => $charge) {
        if ($charge->slug == 'mailer_price') {
          $mailerCost += $charge->price;
        }
      }
    }
    if ($mailerCost > 0) {
      if (isset($brand)) {
        $mailer_cost = $brand->mailer_cost;
      } else if (isset($serviceCharge)) {
        $mailer_cost = $serviceCharge->mailer;
      }
    } else {
      $mailer_cost = 0.00;
    }
    if ($mailer_cost == NULL || $mailer_cost == 0) {
      $mailer_cost = 0.00;
    }
    return response()->json($mailer_cost);
  }
  public function toggleLabelCost()
  {
    $predefined_data = Setting::first('labels');
    return response()->json(["lable_cost" => $predefined_data->labels]);
  }
  public function saveMergedItems(Request $request)
  {
    DB::beginTransaction();
    try {
      $mergedQty = str_replace(',', '', $request->merged_qty);
      for ($i = 0; $i < count($request->customer_ids); $i++) {
        if ($request->status[$i] == 'checked') {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->merged_customer_id)->where('selected_brand', $request->merged_brand_id)->where('merged_brand', $request->brand_ids[$i])->where('product_id', $request->product_id);
          if ($mergedProduct->exists()) {
            $mergedProduct->delete();
            MergedBrandProduct::create([
              'customer_id' => $request->merged_customer_id,
              'selected_brand' => $request->merged_brand_id,
              'merged_brand' => $request->brand_ids[$i],
              'product_id' => $request->product_ids[$i],
              'merged_qty' => $mergedQty,
            ]);
          } else {
            MergedBrandProduct::create([
              'customer_id' => $request->merged_customer_id,
              'selected_brand' => $request->merged_brand_id,
              'merged_brand' => $request->brand_ids[$i],
              'product_id' => $request->product_ids[$i],
              'merged_qty' => $mergedQty,
            ]);
          }
          // $mergedProductCase2 = MergedBrandProduct::where('selected_brand', $request->brand_ids[$i])->where('merged_brand', $request->merged_brand_id);
          // if ($mergedProduct->exists()) {
          //   $mergedProduct->delete();
          //   MergedBrandProduct::create([
          //     'customer_id' => $request->merged_customer_id,
          //     'selected_brand' => $request->merged_brand_id,
          //     'merged_brand' => $request->brand_ids[$i],
          //     'product_id' => $request->product_ids[$i],
          //     'merged_qty' => $mergedQty,
          //   ]);
          // } else if ($mergedProductCase2->exists()) {
          //   $mergedProductCase2->delete();
          //   MergedBrandProduct::create([
          //     'customer_id' => $request->merged_customer_id,
          //     'selected_brand' => $request->brand_ids[$i],
          //     'merged_brand' => $request->merged_brand_id,
          //     'merged_qty' => $mergedQty,
          //   ]);
          // } else {
          //   MergedBrandProduct::create([
          //     'customer_id' => $request->merged_customer_id,
          //     'selected_brand' => $request->merged_brand_id,
          //     'merged_brand' => $request->brand_ids[$i],
          //     'product_id' => $request->product_ids[$i],
          //     'merged_qty' => $mergedQty,
          //   ]);
          // }
        } else {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->merged_customer_id)->where('selected_brand', $request->merged_brand_id)->where('merged_brand', $request->brand_ids[$i])->where('product_id', $request->product_id);
          if ($mergedProduct->exists()) {
            $mergedProduct->delete();
            // MergedBrandProduct::create([
            //   'customer_id' => $request->merged_customer_id,
            //   'selected_brand' => $request->merged_brand_id,
            //   'merged_brand' => $request->brand_ids[$i],
            //   'product_id' => $request->product_ids[$i],
            //   'merged_qty' => $mergedQty,
            // ]);
          } else {
            // MergedBrandProduct::create([
            //   'customer_id' => $request->merged_customer_id,
            //   'selected_brand' => $request->merged_brand_id,
            //   'merged_brand' => $request->brand_ids[$i],
            //   'product_id' => $request->product_ids[$i],
            //   'merged_qty' => $mergedQty,
            // ]);
          }
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function resetLabelToZero(Request $request)
  {
    DB::beginTransaction();
    try {
      if ($request->qty < 0) {
        return response()->json(['error' => true, 'message' => 'Cant add negative value']);
      }
      $cust_prod = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id);
      if ($cust_prod->exists()) {
        $cust_prod = $cust_prod->first();
        if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty =  $request->qty;
              $labels->save();
            }
          }
        } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->exists()) {
          $mergedProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
          if (isset($mergedProduct)) {
            $labels = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $request->product_id)->first();
            if (isset($labels)) {
              $labels->merged_qty =  $request->qty;
              $labels->save();
            }
          }
        } else {
          $cust_prod->label_qty = ((int)$request->qty);
          $cust_prod->save();
        }
      }
      $cust_prod = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->where('brand_id', $request->brand_id)->first();
      $custprod = CustomerProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id);
      if ($custprod->exists()) {
        $custprod = $custprod->first();
        if ($custprod->label_qty < $request->qty) {
          return response()->json(['error' => true, 'message' => 'Please Add/Update Label Qty then Reset']);
        } else {
          $custprod->label_qty = $cust_prod->label_qty;
          $custprod->save();
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Label quantity set to 0']);
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
}
