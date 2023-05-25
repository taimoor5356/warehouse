<?php

namespace App\Http\Controllers\Admin;
ini_set('memory_limit', '5120M');
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use App\Models\Setting;
use App\Models\SkuOrder;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Invoices;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\CustomerHasSku;
use App\Models\ServiceCharges;
use App\Models\CustomerProduct;
use App\Models\SkuOrderDetails;
use App\Models\ProductLabelOrder;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use App\Models\ProductOrderDetail;
use Illuminate\Support\Facades\DB;
use App\AdminModels\InvoiceDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\InventoryHistory;
use App\AdminModels\OrderDetails;
use App\Jobs\UpdateSkuWeight;

class SkuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->hasRole('admin')) {
                if (Auth::user()->can('sku_view')) {
                    // $query = Sku::with('sku_product.product.customerHasProduct', 'brand_detail.customer');
                    $query = DB::table('skus')
                        ->join('labels', 'skus.brand_id', '=', 'labels.id')
                        ->join('customers', 'labels.customer_id', '=', 'customers.id')
                        ->join('sku_products', 'skus.id', '=', 'sku_products.sku_id');
                    if (!empty($request->customer)) {
                        $query = $query->where('customers.customer_name', '=', $request->customer);
                    }
                    if ($request->brand != '') {
                        $query = $query->where('labels.brand', '=', $request->brand);
                    }
                    $query = $query->select('skus.*', 'labels.brand as brand', 'labels.customer_id as customer_id', 'sku_products.product_id')
                        ->where('skus.deleted_at', NULL)
                        ->where('customers.deleted_at', '=', NULL)
                        ->groupBy('skus.id');
                    $data = $query;
                }
            }
            if (!Auth::user()->hasRole('admin')) {
                if (Auth::user()->can('sku_view')) {
                    $query = array();
                    $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
                    $customerId = Auth::user()->id;
                    if (isset($customerUser)) {
                      $customerId = $customerUser->customer_id;
                    }
                    $customerBrandSkus = Customers::with('brands')->where('id', $customerId)->first();
                    if (isset($customerBrandSkus)) {
                        $custBrands = $customerBrandSkus->brands;
                        foreach ($custBrands as $key => $brand) {
                            $sku = Sku::with('sku_product.product.customerHasProduct.brands', 'brand_detail.customer')->where('brand_id', $brand->id)->first();
                            array_push($query, $sku);
                        }
                    }
                    $data = $query;
                }
            }
            return Datatables::of($data->get())
                ->addIndexColumn()
                ->addColumn('btn', function($row) {
                    return '<img src="https://datatables.net/examples/resources/details_open.png" class="details-control" data-id="'.$row->id.'" data-customer-id="'.$row->customer_id.'" data-brand-id="'.$row->brand_id.'" style="cursor: pointer">';
                })
                ->addColumn('sku_id', function ($row) {
                    return ucwords($row->sku_id);
                })
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('customer', function ($row) {
                    $customerName = Customers::find($row->customer_id);
                    if (isset($customerName)) {
                        return ucwords($customerName->customer_name);
                    }
                })
                ->addColumn('brand', function ($row) {
                    return ucwords($row->brand);
                })
                ->addColumn('cost', function ($row) {
                    return '$ ' . number_format($row->selling_cost, 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i data-feather="more-vertical"></i>
                    </button>
                    <div class="dropdown-menu">';
                    if (Auth::user()->can('sku_update')) {
                        $btn .= '<a class="dropdown-item" href="/sku/' . $row->id . '/edit">
                                  <i data-feather="edit-2"></i>
                                  <span>Edit SKU</span>
                              </a>';
                    }
                    if (Auth::user()->can('sku_delete')) {
                        $btn .= '<a class="dropdown-item" onclick="confirmDelete(event)" id="delete_sku" data-id="' . $row->id . '">
                                    <i data-feather="trash"></i>
                                    <span>Delete SKU</span>
                                </a>';
                    }
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['btn', 'action'])
                ->make(true);
        }
        if (Auth::user()->hasRole('admin')) {
            if (Auth::user()->can('customer_view')) {
                $customers = Customers::get();
                $brands = Labels::get();
            }
        }
        if (!Auth::user()->hasRole('admin')) {
            $customers = Customers::get();
            $brands = Labels::get();
            if (Auth::user()->can('sku_view')) {
                $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
                $customerId = Auth::user()->id;
                if (isset($customerUser)) {
                  $customerId = $customerUser->customer_id;
                }
                $customers = Customers::where('id', $customerId)->get();
                $brands = Labels::where('customer_id', $customerId)->get();
            }
        }
        return view('admin.sku.index', compact('customers', 'brands'));
    }
    
    public function getSkuProductsAndLabels(Request $request)
    {
        $skuProducts = SkuProducts::where('sku_id', $request->sku_id)->get();
        foreach ($skuProducts as $key => $skuProduct) {
            $customerHasProd = CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('product_id', $skuProduct->product_id);
            $product = Products::find($skuProduct->product_id);
            if (isset($product)) {
                $productName = $product->name;
                $weight = $product->weight;
            } else {
                $productName = 'Not Exists';
                $weight = '0.00';
            }
            $customerProduct = $customerHasProd->first();
            $is_active = 0;
            if (isset($customerProduct)) {
                $is_active = $customerProduct->is_active;
            }
            $labelQty = $customerHasProd->sum('label_qty');

            $mergedBrand = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $skuProduct->product_id)->select(['merged_qty']);
            $mergedSelectedBrand = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $skuProduct->product_id)->select(['merged_qty']);
            if ($mergedBrand->exists()) {
                $mergedProduct = $mergedBrand->first();
            if (isset($mergedProduct)) {
                $labelQty = $mergedProduct->merged_qty;
            }
            } else if ($mergedSelectedBrand->exists()) {
                $mergedProduct = $mergedSelectedBrand->first();
            if (isset($mergedProduct)) {
                $labelQty = $mergedProduct->merged_qty;
            }
            } else {
                $labelQty = $labelQty;
            }
            $skuProducts[$key]->label_qty = $labelQty;
            $skuProducts[$key]->is_active = $is_active;
            $skuProducts[$key]->product_name = $productName;
            $skuProducts[$key]->weight = $weight;
        }
        return response()->json(['status' => true, 'data' => $skuProducts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $cust_prods = CustomerHasProduct::with('products.inventory')->where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->get();
            return response()->json($cust_prods);
        }
        $customers = Customers::where('is_active', 1)->get();
        return view('admin.sku.create', compact('customers'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validate([
                'customer' => 'required',
                'brand' => 'required',
                'sku_id' => 'required',
                'products' => 'required',
            ]);
            $name = "";
            if ($request->has('brand_id')) {
                $request->merge([
                    "brand" => $request->brand_id
                ]);
            }
            $flat_status = 0;
            if ($request->default_pick_pack_flat_status) {
                if ($request->default_pick_pack_flat_status == 1) {
                    $flat_status = 1;
                }
                $flat_status = 1;
            }
            $sku = Sku::create([
                'brand_id' => $request->brand,
                'sku_id' => $request->sku_id,
                'name' => "abc",
                'purchasing_cost' => 0.00,
                'selling_cost' => 0.00,
                'service_charges' => '0',
                'pick_pack_flat_status' => $flat_status
            ]);
            $sku_weight = 0;
            $sku_purchasing_cost = 0;
            $sku_selling_cost = 0;
            $newarr = [];
            $customerCharges = ServiceCharges::where('customer_id', $request->customer)->first();
            foreach ($request->products as $key => $value) {
                $labelInput = $request->input('label_name' . $key);
                $pickInput = $request->input('pick_name' . $key);
                $packInput = $request->input('pack_name' . $key);
                $pickPackFlatInput = $request->input('pick_pack_flat' . $key);
                $service_charges_details = [];
                $product = Products::where('id', $request->products[$key])->first();
                $name = $name . $product->name . " X " . $request->qty[$key] . " + ";
                $label = NULL;
                $pick = NULL;
                $pack = NULL;
                $pickPackFlat = 0;
                if (isset($labelInput[0])) {
                    $label = $labelInput[0];
                }
    
                if (isset($pickInput[0])) {
                    $pick = $pickInput[0];
                }
    
                if (isset($packInput[0])) {
                    $pack = $packInput[0];
                }
    
                if (isset($pickPackFlatInput[0])) {
                    $pickPackFlat = $pickPackFlatInput[0];
                }
    
                if ($pickPackFlat > 0) {
                    $pickPackFlat = 1;
                    $pick = NULL;
                    $pack = NULL;
                }
    
                if ($pickPackFlat == 0 && $pick == 0 && $pack == 0) {
                    $pickPackFlat = 1;
                    $sku->pick_pack_flat_status = $pickPackFlat;
                }
    
                $sku_products = SkuProducts::create([
                    'sku_id' => $sku->id,
                    'product_id' => $request->products[$key],
                    'quantity' => $request->qty[$key],
                    'selling_cost' => $request->selling_price[$key],
                    'purchasing_cost' => $request->qty[$key] * $product->price,
                    'label' => $label,
                    'pick' => $pick,
                    'pack' => $pack,
                    'pick_pack_flat_status' => $pickPackFlat
                ]);
                $sku_purchasing_cost += $sku_products->purchasing_cost;
                $sku_selling_cost += $sku_products->selling_cost;
                $sku_weight += ($request->qty[$key] * $product->weight);
                if (CustomerHasProduct::where('customer_id', $request->customer)->where('brand_id', $request->brand)->where('product_id', $request->products[$key])->exists()) {
                } else {
                    CustomerHasProduct::create([
                        'customer_id' => $request->customer,
                        'brand_id' => $request->brand,
                        'product_id' => $request->products[$key],
                        'selling_price' => $request->selling_price[$key],
                    ]);
                }
            }
            $name = substr($name, 0, strlen($name) - 2);
            if ($request->has('sku_name') && $request->sku_name != null) {
                $sku->name = $request->sku_name;
            } else {
                $sku->name = $name;
            }
            $sku->weight = $sku_weight;
            $sku->purchasing_cost = $sku_purchasing_cost;
            $sku->selling_cost = $sku_selling_cost;
            $sku->service_charges_detail = $newarr;
            $sku->save();
            if (CustomerHasSku::where('customer_id', $request->customer)->where('sku_id', $sku->id)->exists()) {
            } else {
                CustomerHasSku::create([
                    'customer_id' => $request->customer,
                    'sku_id' => $sku->id,
                    'brand_id' => $request->brand,
                    'selling_price' => $sku_selling_cost
                ]);
            }
            UpdateSkuWeight::dispatch();
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Data Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong']);
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
    public function edit(Sku $sku)
    {
        $sku = $sku->load('sku_product', 'brand');
        $customers = Customers::where('is_active', 1)->get();
        if (isset($sku->brand->customer)) {
            $brands = Labels::where('customer_id', $sku->brand->customer->id)->get();
            $custprods = CustomerHasProduct::with('products')->where('brand_id', $sku->brand->id)->where('customer_id', $sku->brand->customer->id)->get();
            $products = array();
            $keys = array('id', 'name');
            foreach ($custprods as $prod) {
                $product = Products::where('id', $prod->product_id)->first();
                if (isset($product)) {
                    array_push($products, array_combine($keys, [$product->id, $product->name]));
                }
            }
            $sku = Sku::where('id', $sku->id)->first();
            $_skuProducts = $sku->sku_product;
            $sku_counts = $_skuProducts->count();
            return view('admin.sku.edit', compact('sku', 'customers', 'products', 'brands', '_skuProducts', 'sku_counts'));
        } else {
            return redirect()->back()->with('error', 'Customer doesnot exists');
        }
    }
    public function editBrandSku($id, $sku_id)
    {
        $brand = Labels::with('customer')->where('id', $id)->first();
        $sku = Sku::where('id', $sku_id)->first();
        $products = Products::get();
        $_skuProducts = $sku->sku_product;
        $sku_counts = $_skuProducts->count();
        return view('admin.labels.edit-sku', compact('brand', 'products', 'sku', '_skuProducts', 'sku_counts'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sku $sku)
    {
        try {
            // dd($request->all());
            DB::beginTransaction();
            $validatedData = $request->validate([
                'customer' => 'required',
                'brand' => 'required',
                'sku_id' => 'required',
                'products' => 'required',
            ]);
            $name = "";
            if ($request->has('brand_id')) {
                $request->merge([
                    "brand" => $request->brand_id
                ]);
            }
            $products = SkuProducts::where('sku_id', $sku->id)->get();
            foreach ($products as $key => $product) {
                $product->forceDelete();
            }
            $flat_status = 0;
            if ($request->default_pick_pack_flat_status) {
                if ($request->default_pick_pack_flat_status == 1) {
                    $flat_status = 1;
                }
                $flat_status = 1;
            }
            $sku->sku_id = $request->sku_id;
            $sku->brand_id = $request->brand;
            $sku->pick_pack_flat_status = $flat_status;
            $exist = 0;
            $notexist = 0;
            $sku_weight = 0;
            $sku_purchasing_cost = 0;
            $sku_selling_cost = 0;
            $customerCharges = ServiceCharges::where('customer_id', $request->customer)->first();
            foreach ($request->products as $key => $value) {
                $pickInput = $request->input('pick_name' . $key);
                $packInput = $request->input('pack_name' . $key);
                $pickPackFlatInput = $request->input('pick_pack_flat' . $key);
                $product = Products::where('id', $request->products[$key])->first();
                $pick = NULL;
                $pack = NULL;
                $pickPackFlat = 0;
                $name = $name . $product->name . " X " . $request->qty[$key] . " + ";
                if (isset($pickInput[0])) {
                    $pick = $pickInput[0];
                }
    
                if (isset($packInput[0])) {
                    $pack = $packInput[0];
                }
    
                if (isset($pickPackFlatInput[0])) {
                    $pickPackFlat = $pickPackFlatInput[0];
                }
    
                if ($pickPackFlat > 0) {
                    $pickPackFlat = 1;
                    $pick = NULL;
                    $pack = NULL;
                }
    
                if ($pickPackFlat == 0 && $pick == 0 && $pack == 0) {
                    $pickPackFlat = 1;
                    $sku->pick_pack_flat_status = $pickPackFlat;
                }
    
                $sku_products = SkuProducts::create([
                    'sku_id' => $sku->id,
                    'product_id' => $request->products[$key],
                    'quantity' => $request->qty[$key],
                    'selling_cost' => $request->selling_price[$key],
                    'purchasing_cost' => $request->qty[$key] * $product->price,
                    'pick' => $pick,
                    'pack' => $pack,
                    'pick_pack_flat_status' => $pickPackFlat
                ]);
                $sku_weight += ($request->qty[$key] * $product->weight);
                $sku_purchasing_cost += $sku_products->purchasing_cost;
                $sku_selling_cost += $sku_products->selling_cost;
                if (CustomerHasProduct::where('customer_id', $request->customer)->where('brand_id', $request->brand)->where('product_id', $request->products[$key])->exists()) {
                    CustomerHasProduct::where('customer_id', $request->customer)->where('brand_id', $request->brand)->where('product_id', $request->products[$key])->update([
                        'selling_price' => $request->selling_price[$key],
                    ]);
                } else {
                    CustomerHasProduct::create([
                        'customer_id' => $request->customer,
                        'brand_id' => $request->brand,
                        'product_id' => $request->products[$key],
                        'selling_price' => $request->selling_price[$key],
                        'qty' => 0
                    ]);
                }
            }
            $name = substr($name, 0, strlen($name) - 2);
            if ($request->has('sku_name') && $request->sku_name != null) {
                $sku->name = $request->sku_name;
            } else {
                $sku->name = $name;
            }
            $sku->weight = $sku_weight;
            $sku->purchasing_cost = $sku_purchasing_cost;
            $sku->selling_cost = $sku_selling_cost;
            $sku->save();
            if (CustomerHasSku::where('customer_id', $request->customer)->where('sku_id', $sku->id)->exists()) {
                CustomerHasSku::where('customer_id', $request->customer)->where('sku_id', $sku->id)->where('brand_id', $request->brand)->update([
                    'selling_price' => $sku_selling_cost
                ]);
            } else {
                CustomerHasSku::create([
                    'customer_id' => $request->customer,
                    'sku_id' => $sku->id,
                    'brand_id' => $request->brand,
                    'selling_price' => $sku_selling_cost
                ]);
            }
            UpdateSkuWeight::dispatch();
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => 'Something went wrong']);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $sku = Sku::find($id);
        $sku->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'SKU Deleted Successfully'
        ], 200);
    }
    public function trash(Request $request, $id = null)
    {
        if ($request->ajax()) {
            if ($id != null) {
                $data = Sku::with('sku_product', 'brand')->where('brand_id', $id)->onlyTrashed()->get();
            } else {
                $data = Sku::with('sku_product', 'brand')->onlyTrashed()->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('sku_id', function ($row) {
                    return ucwords($row->sku_id);
                })
                ->addColumn('customer', function ($row) {
                    $customer = Customers::where('id', $row->brand->customer_id)->first();
                    $customer_name = '';
                    if (isset($customer)) {
                        $customer_name = $customer->customer_name;
                    }
                    return ucwords($customer_name);
                })
                ->addColumn('brand', function ($row) {
                    return ucwords($row->brand->brand);
                })
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('cost', function ($row) {
                    $cost = 0;
                    foreach ($row->sku_product as $key => $value) {
                        $cost = $cost + ($value->selling_cost * $value->quantity);
                    }
                    return "$ " . number_format($cost, 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/sku/restore/' . $row->id . '" class="btn btn-primary btn-sm">Restore</a>&nbsp;';
                    // $btn .= '<a href="/sku/delete/' . $row->id . '" class="btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                // ->filter(function ($instance) use ($request)
                // {
                //     if (!empty($request->get('search')['value']))
                //     {
                //         $keyword = $request->get('search')['value'];
                //         $instance->whereRaw("category.name like '%$keyword%' OR products.name like '%$keyword%' ");
                //     }
                // })
                ->make(true);
        }
        return view('admin.sku.trash');
    }
    public function restore($id)
    {
        $sku = Sku::with('sku_product')->where('id', $id)->onlyTrashed()->first();
        if (isset($sku)) {
            $sku->restore();
            return redirect()->back()->withSuccess('SKU Restored Successfully');
        } else {
            return redirect()->back()->withError('Error while restoring SKU');
        }
    }
    public function brandSku(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = Sku::with('sku_product', 'brand')->where('brand_id', $id)->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('sku_id', function ($row) {
                    return ucwords($row->sku_id);
                })
                ->addColumn('customer', function ($row) {
                    $customer = Customers::where('id', $row->brand->customer_id)->first();
                    return ucwords($customer->customer_name);
                })
                ->addColumn('brand', function ($row) {
                    return ucwords($row->brand->brand);
                })
                ->addColumn('name', function ($row) {
                    return ucwords($row->name);
                })
                ->addColumn('cost', function ($row) {
                    $cost = 0;
                    foreach ($row->sku_product as $key => $value) {
                        $cost = $cost + ($value->selling_cost * $value->quantity);
                    }
                    return "$ " . number_format($cost, 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i data-feather="more-vertical"></i>
                </button>
                <div class="dropdown-menu">';
                    if (Auth::user()->can('sku_update')) {
                        $btn .= '<a class="dropdown-item" href="/brand/' . $row->brand->id . '/sku/' . $row->id . '/edit">
                                <i data-feather="edit-2"></i>
                                <span>Edit SKU</span>
                            </a>';
                    }
                    if (Auth::user()->can('sku_delete')) {
                        $btn .= '<a class="dropdown-item" href="javascript:void(0)" id="delete_sku" data-id="' . $row->id . '">
                                <i data-feather="trash"></i>
                                <span>Delete SKU</span>
                            </a>';
                    }
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->make(true);
        }
        $brand = Labels::where('id', $id)->first();
        return view('admin.labels.sku', compact('brand'));
    }
    public function createBrandSku($id)
    {
        $brand = Labels::with('customer')->where('id', $id)->first();
        $query = Products::query()->with('inventory');
        $query->where('is_active', 1);
        $query->whereHas("Inventory", function ($q) {
            $q->where("qty", ">", "0");
        });
        $products = $query->get();
        return view('admin.labels.create-sku', compact('brand', 'products'));
    }
    public function permanentlyDeleteSku($id)
    {
        $sku = Sku::with('sku_product')->where('id', $id)->withTrashed()->first();
        foreach ($sku->sku_product as $key => $product) {
            // $product->forceDelete();
        }
        // $sku->forceDelete();
        return redirect()->back()->withSuccess("SKU Permanently Deleted");
    }
    public function getBrandSku(Request $request, $id)
    {
        $skus = Sku::with('sku_product', 'brand')->where('brand_id', $id)->get();
        $data = array();
        $keys = array('id', 'sku_id', 'name', 'weight', 'brand_id', 'selling_cost', 'products', 'unique', 'pick_pack_flat_status');
        $label = 0;
        $pick = 0;
        $pack = 0;
        foreach ($skus as $sku) {
            $products = SkuProducts::where('sku_id', $sku->id)->groupBy('product_id')->pluck('product_id');
            $custHasSku = CustomerHasSku::where('customer_id', $request->customer_id)->where('sku_id', $sku->id)->where('brand_id', $sku->brand_id)->first();
            $sku->sku_product->load('labelqty');
            $sellingCost = 0.00;
            if (isset($custHasSku)) {
                $sellingCost = $custHasSku->selling_price;
            }
            if ($sellingCost == null) {
                $sellingCost = 0.00;
            }
            $sellingCost = $sku->selling_cost;
            array_push($data, array_combine($keys, [$sku->id, $sku->sku_id, $sku->name, number_format($sku->weight, 2), $sku->brand_id, number_format($sellingCost, 2), $sku->sku_product, $products, $sku->pick_pack_flat_status]));
        }
        return response()->json($data, 200);
    }
    public function skuDetail($id)
    {
        $sku = Sku::with('sku_product')->where('id', $id)->first();
        if ($sku) {
            $selling_cost = 0;
            foreach ($sku->sku_product as $key => $product) {
                $selling_cost += $product->selling_cost;
            }
            $sku->selling_cost = number_format($selling_cost, 2);
            return response()->json([
                'status' => 'success',
                'data' => $sku
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Error while loading SKU'
            ], 200);
        }
    }
    public function skuProductDetails(Request $request, $id)
    {
        try {
            $data = array();
            $sku = Sku::where('id', $id)->first();
            $_skuProducts = $sku->sku_product->unique('product_id');
            $keys = array('status', 'labels_price', 'label_qty', 'pick_price', 'pack_price', 'prod_id', 'selling_price', 'prod_qty', 'prodCounts', 'prod_label_count', 'prod_pick_count', 'prod_pack_count', 'prod_pick_pack_flat_count', 'prod_pick_pack_flat_price', 'seller_cost_status');
            $labelCost = 0.00;
            $labelQty = 0;
            $pickCost = 0.00;
            $packCost = 0.00;
            $newpickCost = 0.00;
            $newpackCost = 0.00;
            $newlabelCost = 0.00;
            $selling_price = 0.00;
            $labelc = 0.00;
            $sellerCostStatus = 0;
            $prod_pick_pack_flat_cost = 0.00;
            $prod_pick_pack_flat_count = 0;
            foreach ($_skuProducts as $skuprod) {
                $status = 1;
                $skuProductName = '';
                if (isset($skuprod)) {
                    $custHasProduct = CustomerHasProduct::where('customer_id', $request->customer_id)
                        ->where('brand_id', $sku->brand_id)
                        ->where('product_id', $skuprod->product_id)
                        // ->latest('updated_at')
                        ->first();
                    $custProduct = CustomerProduct::where('customer_id', $request->customer_id)
                        ->where('product_id', $skuprod->product_id)
                        ->first();
                    $prodCounts = SkuProducts::where('sku_id', $skuprod->sku_id)
                        ->where('product_id', $skuprod->product_id)
                        ->count();
                    $service_charges = ServiceCharges::where('customer_id', $request->customer_id)->latest('updated_at')->first();
                    $prod_pick_cost = SkuProducts::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)->where('pick_pack_flat_status', '=', '0')->sum('pick');
                    $prod_pack_cost = SkuProducts::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)->where('pick_pack_flat_status', '=', '0')->sum('pack');
                    $prodPickPackFlatStatus = $sku->pick_pack_flat_status;
                    $pickPackFlatCost = 0;
                    if ($prodPickPackFlatStatus == 1) {
                        $pickPackFlatCost = $service_charges->pick_pack_flat;
                    }
                    $prod_label_count = SkuProducts::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)->where('label', '!=', NULL)->count();
                    $prod_pick_count = SkuProducts::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)->where('pick', '!=', NULL)->where('pick_pack_flat_status', '=', '0')->count();
                    $prod_pack_count = SkuProducts::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)->where('pack', '!=', NULL)->where('pick_pack_flat_status', '=', '0')->count();
                    $prod_pick_pack_flat_count = Sku::where('id', $id)->where('pick_pack_flat_status', 1)->count();
                    $pickCost = $skuprod->pick;
                    $packCost = $skuprod->pack;
                    $labelc = $skuprod->label;
                    if (isset($custProduct)) {
                        $sellerCostStatus = $custProduct->seller_cost_status;
                        $status = $custProduct->is_active;
                        if ($status == 0) { // If ON, 0 = ON, 1 = OFF
                            $custHasLabelCost = $custProduct->label_cost;
                            if ($custHasLabelCost <= 0 || $custHasLabelCost == NULL) {
                                if (isset($service_charges)) {
                                    $labelCost = $service_charges->labels;
                                } else {
                                    $labelCost = 0.00;
                                }
                            } else {
                                $labelCost = $custHasLabelCost;
                            }
                        } else {
                            $labelCost = 0.00;
                        }
                        if ($pickCost != 0 || $pickCost != NULL) {
                            $pickCost = $service_charges->pick;
                        } else {
                            $pickCost = 0.00;
                            $newpickCost = $newpickCost + $pickCost;
                        }
                        if ($packCost != 0 || $packCost != NULL) {
                            $packCost = $service_charges->pack;
                        } else {
                            $packCost = 0.00;
                            $newpackCost = $newpackCost + $packCost;
                        }
                        $labelQty = $custHasProduct->label_qty;
                        if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->exists()) {
                            $labelQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->first();
                            if (isset($labelQty)) {
                                $labelQty = $labelQty->merged_qty;
                            }
                        } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->exists()) {
                            $labelQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->first();
                            if (isset($labelQty)) {
                                $labelQty = $labelQty->merged_qty;
                            }
                        } else {
                            if ($labelQty == NULL) {
                                $labelQty = 0;
                            }
                        }
                    } else {
                        $labelCost = $skuprod->label;
                    }
                    $skuProductName .= $skuprod->name;
                    $selling_price = $skuprod->selling_cost;
                }
                $prod_qty = $skuprod->quantity;
                if ($labelQty > 0) {
                    if ($labelCost == 0 || $labelCost == '') {
                        $serviceCharges = ServiceCharges::where('customer_id', $request->customer_id)->first();
                        if (isset($serviceCharges)) {
                            $labelCost = $serviceCharges->labels;
                        }
                    }
                }
                if ($custHasProduct->is_active == 1) {
                    $labelCost = 0.00;
                }
                array_push($data, array_combine($keys, [$status, $labelCost, $labelQty, $prod_pick_cost, $prod_pack_cost, $skuprod->product_id, $selling_price, $prod_qty, $prodCounts, $prod_label_count, $prod_pick_count, $prod_pack_count, $prod_pick_pack_flat_count, $pickPackFlatCost, $sellerCostStatus]));
            }
            return response()->json($data);
        } catch (\Exception $e) {
            echo $e;
        }
    }
    public function getCountsProducts(Request $request)
    {
        $custProducts = CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->get();
        $data = array();
        $keys = array('prod_id', 'prod_name', 'prod_labels', 'prod_label_cost', 'prod_status', 'seller_cost_status');
        foreach ($custProducts as $custProd) {
            if (isset($custProd)) {
                $productDetails = Products::where('id', $custProd->product_id)->first();
                $labelQty = $custProd->label_qty;
                if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $custProd->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $custProd->product_id)->first();
                    if (isset($mergedQty)) {
                        $labelQty = $mergedQty->merged_qty;
                    }
                    // $res = $custhasProd->merged_qty - $qty;
                    // $custhasProd->merged_qty = $res;
                } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $custProd->product_id)->exists()) {
                    $mergedQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $custProd->product_id)->first();
                    if (isset($mergedQty)) {
                        $labelQty = $mergedQty->merged_qty;
                    }
                    // $res = $custhasProd->merged_qty - $qty;
                    // $custhasProd->merged_qty = $res;
                } else {
                    $labelQty = $custProd->label_qty;
                }
                // if ($custProd->merged_qty != NULL) {
                //     $labelQty = $custProd->merged_qty;
                // } else {
                //     $labelQty = $custProd->label_qty;
                // }
                array_push($data, array_combine($keys, [$productDetails->id, $productDetails->name, $labelQty, $custProd->label_cost, $custProd->is_active, $custProd->seller_cost_status]));
            }
        }
        return response()->json($data);
    }
    public function editOrderSkuProductDetails(Request $request, $id)
    {
        $order = Orders::where('id', $request->order_id)->where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('status', '!=', 4)->first();
        $customerService_Charges = 0;
        $getCustomerService_ChargesDetails = $order->customer_service_charges;
        $customerService_Charges = json_decode($getCustomerService_ChargesDetails);
        $getPick = $customerService_Charges->pick;
        $getPack = $customerService_Charges->pack;
        $getLabel = 0;
        $getPickPackFlat = $customerService_Charges->pick_pack_flat;
        $data = array();
        $sku = SkuOrder::with('sku_product')->where('sku_id', $request->sku_id)->where('order_id', $request->order_id)->first();
        $_skuProducts = $sku->sku_product->unique('product_id');
        $keys = array('status', 'labels_price', 'label_qty', 'pick_price', 'pack_price', 'prod_id', 'selling_price', 'prod_qty', 'prodCounts', 'prod_label_count', 'prod_pick_count', 'prod_pack_count', 'prod_pick_pack_flat_count', 'prod_pick_pack_flat_price', 'seller_cost_status');
        $labelCost = 0.00;
        $labelQty = 0;
        $pickCost = 0.00;
        $packCost = 0.00;
        $newpickCost = 0.00;
        $newpackCost = 0.00;
        $newlabelCost = 0.00;
        $selling_price = 0.00;
        $labelc = 0.00;
        $sellerCostStatus = 0;
        $prod_pick_pack_flat_cost = 0.00;
        $prod_pick_pack_flat_count = 0;
        foreach ($_skuProducts as $skuprod) {
            $status = 1;
            $skuProductName = '';
            if (isset($skuprod)) {
                $custHasProduct = CustomerHasProduct::where('customer_id', $request->customer_id)
                    ->where('brand_id', $sku->brand_id)
                    ->where('product_id', $skuprod->product_id)
                    // ->latest('updated_at')
                    ->first();
                $custProduct = CustomerProduct::where('customer_id', $request->customer_id)
                    ->where('product_id', $skuprod->product_id)
                    ->first();
                $prodCounts = SkuOrderDetails::where('product_id', $skuprod->product_id)
                    ->where('sku_order_id', $sku->id)
                    ->count();
                $service_charges = ServiceCharges::where('customer_id', $request->customer_id)->latest('updated_at')->first();
                $prod_pick_cost = SkuOrderDetails::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)
                ->where('order_id', $request->order_id)
                ->where('sku_order_id', $sku->id)
                ->where('pick_pack_flat_status', '=', '0')->sum('pick');
                $prod_pack_cost = SkuOrderDetails::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)
                ->where('pick_pack_flat_status', '=', '0')
                ->where('order_id', $request->order_id)
                ->where('sku_order_id', $sku->id)
                ->sum('pack');
                $prodPickPackFlatStatus = $skuprod->pick_pack_flat_status;
                $pickPackFlatCost = 0;
                if ($prodPickPackFlatStatus == 1) {
                    $pickPackFlatCost = $getPickPackFlat;
                }
                $prod_label_count = $order->labelqty;
                $prod_pick_count = SkuOrderDetails::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)
                ->where('order_id', $request->order_id)
                ->where('sku_order_id', $sku->id)
                ->where('pick', '!=', '0')->where('pick_pack_flat_status', '=', '0')->count();
                $prod_pack_count = SkuOrderDetails::where('product_id', $skuprod->product_id)->where('sku_id', $skuprod->sku_id)
                ->where('order_id', $request->order_id)
                ->where('sku_order_id', $sku->id)
                ->where('pack', '!=', '0')->where('pick_pack_flat_status', '=', '0')->count();
                $prod_pick_pack_flat_count = SkuOrder::where('sku_id', $request->sku_id)->where('pick_pack_flat_status', 1)->where('order_id', $request->order_id)->count();
                $pickCost = $getPick;
                $packCost = $getPack;
                $labelc = $service_charges->labels;
                if (isset($skuprod)) {
                    $sellerCostStatus = $skuprod->seller_cost_status;
                    $status = $skuprod->is_active;
                    if ($status == 0) { // If ON, 0 = ON, 1 = OFF
                        $labelCost = $labelc;
                    } else {
                        $labelCost = 0.00;
                    }
                    if ($pickCost != 0 || $pickCost != NULL) {
                        $pickCost = $pickCost;
                    } else {
                        $pickCost = 0.00;
                        $newpickCost = $newpickCost + $pickCost;
                    }
                    if ($packCost != 0 || $packCost != NULL) {
                        $packCost = $packCost;
                    } else {
                        $packCost = 0.00;
                        $newpackCost = $newpackCost + $packCost;
                    }
                    $labelQty = $custHasProduct->label_qty;
                    if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->exists()) {
                        $labelQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->first();
                        if (isset($labelQty)) {
                            $labelQty = $labelQty->merged_qty;
                        }
                    } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->exists()) {
                        $labelQty = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $sku->brand_id)->where('product_id', $skuprod->product_id)->first();
                        if (isset($labelQty)) {
                            $labelQty = $labelQty->merged_qty;
                        }
                    } else {
                        if ($labelQty == NULL) {
                            $labelQty = 0;
                        }
                    }
                } else {
                    $labelCost = $labelc;
                }
                $skuProductName .= $skuprod->name;
                $selling_price = $skuprod->selling_cost;
            }
            $prod_qty = $skuprod->quantity;
            if ($labelQty > 0) {
                if ($labelCost == 0 || $labelCost == '') {
                    $serviceCharges = ServiceCharges::where('customer_id', $request->customer_id)->first();
                    if (isset($serviceCharges)) {
                        $labelCost = $labelc;
                    }
                }
                $labelCost = $labelc;
            }
            if (isset($custHasProduct)) {
                if ($custHasProduct->is_active == 0) {
                    if ($custHasProduct->label_cost != 0) {
                        $labelCost = $custHasProduct->label_cost;
                    } else {
                        $labelCost = $labelCost;
                    }
                } else {
                    $labelCost = $labelCost;
                }
            }
            if ($skuprod->is_active == 1) {
                $labelCost = 0.00;
            }
            if ($prod_label_count == 0) {
                $labelCost = 0.00;
            }
            array_push($data, array_combine($keys, [$status, $labelCost, $labelQty, $prod_pick_cost, $prod_pack_cost, $skuprod->product_id, $selling_price, $prod_qty, $prodCounts, $prod_label_count, $prod_pick_count, $prod_pack_count, $prod_pick_pack_flat_count, $pickPackFlatCost, $sellerCostStatus]));
        }
        return response()->json($data);
    }
    public function viewInsertSkuOrderData()
    {
        return view('admin.invoices.insert_sku_order_data');
    }
    public function insertSkuOrderData(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        try {
            ini_set('max_execution_time', '0');
            $orders = Orders::with('Details.skuproduct')->where('status', '!=', 4)->whereBetween('id', [$from, $to])->get();
            // Orders::with('Details.skuproduct')->chunk(200, function($orders){ //
                foreach ($orders as $okey => $order) {
                    $invoiceData = Invoices::where('order_id', $order->id)->first();
                    $customerService_Charges = ServiceCharges::where('customer_id', $order->customer_id)->first();
                    $setting = Setting::where('id', 1)->first();
                    $brandMailer = Labels::where('customer_id', $order->customer_id)->first();
                    $mailerCost = $setting->mailer;
                    $totalCostOfGoods = 0.00;
                    $finalMailer = 0.00;
                    $finalPostage = 0.00;
                    $totalSalePrice = 0.00;
                    $totalLabelPrice = 0.00;
                    $totalPickPrice = 0.00;
                    $totalPackPrice = 0.00;
                    $totalPickPackFlatPrice = 0.00;
                    $totalMailerPrice = 0.00;
                    $totalPostagePrice = 0.00;
                    if (isset($brandMailer)) {
                        $mailerCost = $brandMailer->mailer_cost;
                    }
                    if (is_null($mailerCost) || ($mailerCost == '0.00')) {
                        if (isset($customerService_Charges)) {
                            $mailerCost = $customerService_Charges->mailer;
                        }
                    }
                    if (is_null($mailerCost) || ($mailerCost == '0.00')) {
                        if (isset($setting)){
                            $mailerCost = $setting->mailer;
                        }
                    }
                    if (isset($invoiceData)) {
                        $invoice = Invoices::where('order_id', $order->id)->update([
                            'order_id' => $order->id,
                            'invoice_number' => $order->id,
                            'subtotal' => null,
                            'tax' => null,
                            'customer_id' => $order->customer_id,
                            'is_paid' => '0',
                            'status' => '0',
                        ]);
                    } else {
                        $invoice = Invoices::create([
                            'order_id' => $order->id,
                            'invoice_number' => $order->id,
                            'subtotal' => null,
                            'tax' => null,
                            'customer_id' => $order->customer_id,
                            'is_paid' => '0',
                            'status' => '0',
                        ]);
                    }
                    if (isset($order->Details)) {
                        $labelQty = 0;
                        $pickQty = 0;
                        $packQty = 0;
                        $pickPackFlatQty = 0;
                        $mailerQty = 0;
                        $postageQty = 0;
                        foreach ($order->Details as $dkey => $orderDetail) {
                            $sku = Sku::withTrashed()->where('id', $orderDetail->sku_id)->first();
                            $checkSkuOrder = SkuOrder::where('sku_id', $orderDetail->sku_id)
                                            ->where('order_id', $orderDetail->order_id);
                            if (!($checkSkuOrder->exists())) {
                                SkuOrder::create([
                                    'order_id' => $orderDetail->order_id,
                                    'customer_id' => $order->customer_id,
                                    'sku_id' => $orderDetail->sku_id,
                                    'sku_id_name' => $sku != NULL ? $sku->sku_id : '',
                                    'name' => $sku != NULL ? $sku->name : '',
                                    'weight' => $sku != NULL ? $sku->weight : '0.00',
                                    'brand_id' => $order->brand_id ? $order->brand_id : NULL,
                                    'purchasing_cost' => $sku != NULL ? $sku->purchasing_cost : '',
                                    'selling_cost' => $sku != NULL ? $sku->selling_cost : '',
                                    'grand_total_amount' => $sku != NULL ? $sku->grand_total_amount : '',
                                    'pick_pack_flat_status' => $sku != NULL ? $sku->pick_pack_flat_status : '',
                                    'service_charges' => $sku != NULL ? $sku->service_charges : '',
                                    'service_charges_detail' => $sku != NULL ? $sku->service_charges_detail : json_encode(['']),
                                    'mailer_cost' => $mailerCost
                                ]);
                            } else {
                                SkuOrder::where('sku_id', $orderDetail->sku_id)
                                ->where('order_id', $orderDetail->order_id)->update([
                                    'order_id' => $orderDetail->order_id,
                                    'customer_id' => $order->customer_id,
                                    'sku_id' => $orderDetail->sku_id,
                                    'sku_id_name' => $sku != NULL ? $sku->sku_id : '',
                                    'name' => $sku != NULL ? $sku->name : '',
                                    'weight' => $sku != NULL ? $sku->weight : '0.00',
                                    'brand_id' => $order->brand_id ? $order->brand_id : NULL,
                                    'purchasing_cost' => $sku != NULL ? $sku->purchasing_cost : '',
                                    'selling_cost' => $sku != NULL ? $sku->selling_cost : '',
                                    'grand_total_amount' => $sku != NULL ? $sku->grand_total_amount : '',
                                    'pick_pack_flat_status' => $sku != NULL ? $sku->pick_pack_flat_status : '',
                                    'service_charges' => $sku != NULL ? $sku->service_charges : '',
                                    'service_charges_detail' => $sku != NULL ? $sku->service_charges_detail : json_encode(['']),
                                    'mailer_cost' => $mailerCost
                                ]);
                            }
                            $skuOrder = SkuOrder::where('sku_id', $orderDetail->sku_id)
                                        ->where('order_id', $orderDetail->order_id)
                                        ->first();
                            $totalLabelCharges = 0.00;
                            $totalPickCharges = 0.00;
                            $totalPackCharges = 0.00;
                            $totalPickPackFlatCharges = 0.00;
                            $totalMailerCharges = 0.00;
                            $totalPostageCharges = 0.00;
                            $costOfGoods = 0.00;
                            if (isset($orderDetail->skuproduct)) {
                                foreach ($orderDetail->skuproduct as $skukey => $sku_product) {
                                    if (isset($sku_product)) {
                                        $custhasProd = CustomerHasProduct::where('customer_id', $order->customer_id)
                                                        ->where('brand_id', $skuOrder->brand_id)
                                                        ->where('product_id', $sku_product->product_id)
                                                        ->first();
                                        $isActive = 1; // label status //if 1 then off
                                        if (isset($custhasProd)) {
                                            $isActive = $custhasProd->is_active;
                                        }
                                        $custProd = CustomerProduct::where('customer_id', $order->customer_id)
                                                    ->where('product_id', $sku_product->product_id)
                                                    ->first();
                                        $sellerCostStatus = 0;
                                        if (isset($custProd)) {
                                            $sellerCostStatus = $custProd->seller_cost_status;
                                        }
                                        if (!empty($skuOrder)) {
                                            $labelCost = $customerService_Charges->labels;
                                            $pickCost = 0.00;
                                            $packCost = 0.00;
                                            $pickPackFlatCost = 0.00;
                                            if ($isActive == 1) {
                                                $labelCost = 0.00;
                                            }
                                            if ($labelCost == NULL) {
                                                $labelCost = 0.00;
                                            }
                                            if ($labelCost > 0) {
                                                $labelQty += $orderDetail->qty;
                                            }
                                            if ($sku_product->pick_pack_flat_status == 1) {
                                                $pickPackFlatCost = $customerService_Charges->pick_pack_flat;
                                                $pickCost = 0.00;
                                                $packCost = 0.00;
                                            } else {
                                                $pickCost = $sku_product->pick;
                                                $packCost = $sku_product->pack;
                                                $pickPackFlatCost = 0.00;
                                                if ($pickCost == NULL) {
                                                    $pickCost = 0.00;
                                                }
                                                if ($packCost == NULL) {
                                                    $packCost = 0.00;
                                                }
                                                if ($pickCost > 0) {
                                                    $pickQty += $orderDetail->qty;
                                                }
                                                if ($packCost > 0) {
                                                    $packQty += $orderDetail->qty;
                                                }
                                            }
                                            $skuProductId = SkuOrderDetails::where('sku_product_id', $sku_product->id)->where('sku_id', $sku_product->sku_id)
                                            ->where('order_id', $orderDetail->order_id);
                                            if (!$skuProductId->exists()) {
                                                if (!is_null($sku_product->id)) {
                                                    SkuOrderDetails::create([
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
                                                        'label' => $labelCost,
                                                        'pick' => $pickCost,
                                                        'pack' => $packCost,
                                                        'pick_pack_flat_status' => $pickPackFlatCost,
                                                        'is_active' => $isActive,
                                                        'seller_cost_status' => $sellerCostStatus
                                                    ]);
                                                }
                                                $totalLabelCharges += $labelCost;
                                                $totalPickCharges += $pickCost;
                                                $totalPackCharges += $packCost;
                                                $totalPickPackFlatCharges = $pickPackFlatCost;
                                                $difference = $orderDetail->qty;
                                                $mergedBrandProduct = MergedBrandProduct::where('customer_id', $order->customer_id)
                                                                        ->where('merged_brand', $skuOrder->brand_id)
                                                                        ->where('product_id', $sku_product->product_id);
                                                $mergedSelectedBrandProduct = MergedBrandProduct::where('customer_id', $order->customer_id)
                                                                                ->where('selected_brand', $skuOrder->brand_id)
                                                                                ->where('product_id', $sku_product->product_id);
                                                $custhasProd = CustomerHasProduct::where('customer_id', $order->customer_id)
                                                ->where('brand_id', $skuOrder->brand_id)
                                                ->where('product_id', $sku_product->product_id)
                                                ->first();
                                                $customerLabelQty = 0;
                                                if (isset($custhasProd)) {
                                                    $customerLabelQty = $custhasProd->label_qty;
                                                }
                                                if ($mergedBrandProduct->exists()) {
                                                    $mergedQty = $mergedBrandProduct->first();
                                                    if (isset($mergedQty)) {
                                                        $res = $mergedQty->merged_qty;
                                                        $mergedQty->merged_qty = $res;
                                                        $mergedQty->save();
                                                    }
                                                } else if ($mergedSelectedBrandProduct->exists()) {
                                                    $mergedQty = $mergedSelectedBrandProduct->first();
                                                    if (isset($mergedQty)) {
                                                        $res = $mergedQty->merged_qty;
                                                        $mergedQty->merged_qty = $res;
                                                        $res2 = $customerLabelQty;
                                                        $custhasProd->label_qty = $res2;
                                                        $custhasProd->save();
                                                        $mergedQty->save();
                                                    }
                                                } else {
                                                    if (isset($custhasProd)) {
                                                        $res = $customerLabelQty;
                                                        $custhasProd->label_qty = $res;
                                                        $custhasProd->save();
                                                    } else {
                                                        $res = 0;
                                                    }
                                                }
                                                ProductLabelOrder::create([
                                                    'customer_id' => $order->customer_id,
                                                    'brand_id' => $skuOrder->brand_id,
                                                    'product_id' => $sku_product->product_id,
                                                    'label_deduction' => $res,
                                                    'label_cost' => $labelCost
                                                ]);
                                                $custProd->label_qty = $res;
                                                $custProd->save();
                                                $productOrderDetails = ProductOrderDetail::where('order_id', $order->id)->where('product_id', $sku_product->product_id);
                                                if ($productOrderDetails->exists()) {
                                                    $productOrderDetails->update([
                                                        'order_id' => $order->id,
                                                        'sku_id' => $order->sku_id,
                                                        'product_id' => $sku_product->product_id,
                                                        'seller_cost_status' => $custProd->seller_cost_status
                                                    ]);
                                                } else {
                                                    ProductOrderDetail::create([
                                                        'order_id' => $order->id,
                                                        'sku_id' => $order->sku_id,
                                                        'product_id' => $sku_product->product_id,
                                                        'seller_cost_status' => $custProd->seller_cost_status
                                                    ]);
                                                }
                                                // $inventory = Inventory::where('product_id', $sku_product->product_id)->first();
                                                // if (isset($inventory)) {
                                                //     InventoryHistory::create([
                                                //         'product_id' => $sku_product->product_id,
                                                //         'qty' => -($orderDetail->qty),
                                                //         'sales' => $orderDetail->qty,
                                                //         'total' => $inventory->qty,
                                                //         'order_id' => $order->id,
                                                //         'sku_id' => $sku_product->sku_id
                                                //     ]);
                                                // } 
                                            } 
                                        }
                                        $costOfGoods += $sku_product->purchasing_cost;
                                    }
                                }
                            }
                            $totalMailerCharges = $mailerCost * $orderDetail->qty;
                            $weight = $skuOrder->weight;
                            if ($weight < 5) {
                                $totalPostageCharges = $customerService_Charges->postage_cost_lt5;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->postage_cost_lt5;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 5 && $weight < 9) {
                                $totalPostageCharges = $customerService_Charges->postage_cost_lt9;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->postage_cost_lt9;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 9 && $weight < 13) {
                                $totalPostageCharges = $customerService_Charges->postage_cost_lt13;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->postage_cost_lt13;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 13 && $weight < 16) {
                                $totalPostageCharges = $customerService_Charges->postage_cost_gte13;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->postage_cost_gte13;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 16 && $weight < 16.16) { // LBS rates
                                $totalPostageCharges = $customerService_Charges->lbs1_1_99;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->lbs1_1_99;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 16.16 && $weight < 32) {
                                $totalPostageCharges = $customerService_Charges->lbs1_1_2;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->lbs1_1_2;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 32.16 && $weight < 48) {
                                $totalPostageCharges = $customerService_Charges->lbs2_1_3;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->lbs2_1_3;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else if($weight >= 48.16) {
                                $totalPostageCharges = $customerService_Charges->lbs3_1_4;
                                if ($totalPostageCharges == 0) {
                                    // $totalPostageCharges = $setting->lbs3_1_4;
                                }
                                if ($orderDetail->qty == 0) {
                                    $totalPostageCharges = 0;
                                }
                            } else {
                                $totalPostageCharges = 0;
                            }
                            $totalPostageCharges = $totalPostageCharges * $orderDetail->qty;
                            //
                            $getOrderDetailSellingCost = OrderDetails::where('order_id', $order->id)->where('sku_id', $orderDetail->sku_id)->first();
                            $totalSalePrice += ($getOrderDetailSellingCost->sku_selling_cost);
                            $totalLabelPrice += $totalLabelCharges * $orderDetail->qty;
                            $totalPickPrice += $totalPickCharges * $orderDetail->qty;
                            $totalPackPrice += $totalPackCharges * $orderDetail->qty;
                            $totalPickPackFlatPrice += $totalPickPackFlatCharges * $orderDetail->qty;
                            $totalMailerPrice += $totalMailerCharges;
                            $totalPostagePrice += $totalPostageCharges;
                            if ($totalPickPackFlatPrice > 0) {
                                $pickPackFlatQty += $orderDetail->qty;
                            }
                            if ($totalMailerPrice > 0) {
                                $mailerQty += $orderDetail->qty;
                            }
                            if ($totalPostagePrice > 0) {
                                $postageQty += $orderDetail->qty;
                            }
                            //
                            $orderDetail->cost_of_good = $costOfGoods;
                            $orderDetail->sku_purchasing_cost = $skuOrder->purchasing_cost * $orderDetail->qty;
                            $orderDetail->sku_selling_cost = $skuOrder->selling_cost * $orderDetail->qty;
                            $orderDetail->service_charges = '';
                            $service_charges_details = [];
                            array_push($service_charges_details, ['slug' => 'labels_price', 'name' => 'Labels Price', 'price' => $totalLabelCharges * $orderDetail->qty]);
                            array_push($service_charges_details, ['slug' => 'pick_price', 'name' => 'Pick Price', 'price' => $totalPickCharges * $orderDetail->qty]);
                            array_push($service_charges_details, ['slug' => 'pack_price', 'name' => 'Pack Price', 'price' => $totalPackCharges * $orderDetail->qty]);
                            array_push($service_charges_details, ['slug' => 'mailer_price', 'name' => 'Mailer Price', 'price' => $totalMailerPrice]);
                            array_push($service_charges_details, ['slug' => 'postage_price', 'name' => 'Postage Price', 'price' => $totalPostagePrice]);
                            $orderDetail->service_charges_detail = json_encode($service_charges_details);
                            $orderDetail->save();
                            InvoiceDetails::where('invoice_id', $invoiceData->id)->where('sku_id', $skuOrder->id)->update([
                                'invoice_id' => $invoiceData->id,
                                'sku_id' => $skuOrder->id,
                                'qty' => $orderDetail->qty,
                                'cost_of_good' => $skuOrder->selling_cost * $orderDetail->qty,
                                'service_charges' => '',
                                'service_charges_detail' => json_encode($service_charges_details),
                            ]);
                        }
                    }
                    $totalCostOfSkuOrder = ($totalSalePrice)+($totalLabelPrice)+($totalPickPrice)+($totalPackPrice)+($totalMailerPrice)+($totalPostagePrice)+($totalPickPackFlatPrice);
                    // dd(($totalSalePrice),($totalLabelPrice),($totalPickPrice),($totalPackPrice),($totalMailerPrice),($totalPostagePrice),($totalPickPackFlatPrice));
                    $order->labelqty = $labelQty;
                    $order->pickqty = $pickQty;
                    $order->packqty = $packQty;
                    $order->mailerqty = $mailerQty;
                    $order->postageqty = $postageQty;
                    $order->pick_pack_flat_qty = $pickPackFlatQty;
                    $order->total_cost = $totalCostOfSkuOrder;
                    $order->customer_service_charges = $customerService_Charges;
                    $order->pick_pack_flat_price = $totalPickPackFlatPrice;
                    $order->save();
                    $invoiceData->grand_total = $order->total_cost;
                    $invoiceData->save();
                }
            // });
            // $orders = Orders::with('Details.skuproduct')->get();
            return 'DONE';
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function truncateSkuOrderData()
    {
        // SkuOrder::where('order_id', '>=', 3301)->delete();
        // SkuOrderDetails::where('order_id', '>=', 3301)->delete();
        // DB::table('sku_orders')->truncate();
        // DB::table('sku_order_details')->truncate();
        return 'Disabled';
    }
    public function restoreDeletedSku()
    {
        $skus = Sku::onlyTrashed()->whereDate('deleted_at', '>=', '2022-08-05')->get();
        foreach ($skus as $key => $sku) {
            if (isset($sku)) {
                $sku->restore();
            }
        }
        return 'Done';
    }
    public function changeSkuPurchasingCost($from, $to)
    { 
        // 1 to 3829
        // try {
        //     if ($from < 3830) {
        //         $orderDetails = OrderDetails::where('order_id', '>=', $from)->where('order_id', '<=', $to)->where('qty', '>', 0)->orderBy('id', 'ASC')->get();
        //         foreach ($orderDetails as $key => $detail) {
        //             if ($detail->qty > 0) {
        //                 $res = $detail->sku_purchasing_cost / $detail->qty;
        //                 $detail->sku_purchasing_cost = $res;
        //                 $detail->save();
        //             }
        //         }
        //         return 'DONE';
        //     } else {
        //         return 'Not Done';
        //     }
        // } catch (\Exception $e) {
        //     dd($e);
        // }
    }
    public function newOrdersChangeSkuPurchasingCost($from, $to)
    { 
        // 1 to 7000
        // try {
        //     $orderDetails = OrderDetails::where('order_id', '>=', $from)->where('order_id', '<=', $to)->where('qty', '>', 0)->orderBy('id', 'ASC')->get();
        //     foreach ($orderDetails as $key => $detail) {
        //         if ($detail->qty > 0) {
        //             $result = number_format(($detail->sku_purchasing_cost / $detail->qty), 2);
        //             if ($result == number_format($detail->cost_of_good, 2)) {
        //                 $detail->sku_purchasing_cost = $detail->cost_of_good;
        //                 $detail->cost_of_good = $detail->sku_selling_cost;
        //                 $detail->save();
        //             }
        //         }
        //     }
        //     return 'DONE';
        // } catch (\Exception $e) {
        //     dd($e);
        // }
    }
    public function updateSkuWeight()
    {
        $skus = Sku::with('sku_product:sku_id,product_id')->select(['id, weight'])->get();
        foreach ($skus as $key => $sku) {
            if (isset($sku)) {
                $skuWeight = 0;
                $skuProducts = $sku->sku_product;
                foreach ($skuProducts as $skey => $skuProduct) {
                    if (isset($skuProduct)) {
                        $product = Products::where('id', $skuProduct)->select(['weight'])->first();
                        if (isset($product)) {
                            $skuWeight += $product->weight;
                        }
                    }
                }
                $sku->weight = $skuWeight;
                $sku->save();
            }
        }
    }
}
