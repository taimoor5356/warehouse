<?php

namespace App\Http\Controllers\Admin;

use Session;
use Redirect;
use Exception;
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use App\AdminModels\Orders;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Category;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\CustomerProduct;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\Repositories\CategoryInterface;

class CategoryController extends Controller
{
  //
  public $cat;
  public function __construct(CategoryInterface $cat)
  {
    $this->middleware('auth');
    $this->cat = $cat;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->ajax())
    {
      // $columns = $request->get('columns');
      // $orders = $request->get('order');
      // $orderbyColAndDirection = [];
      // foreach ($orders as $key => $value)
      // {
      //   array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      // }
      // $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $data = Category::select(['category.*'])->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('checkbox', function ($row) {
          $checkbox = '<div class="text-center"><input type="checkbox" class="categories-checkbox" data-category-id="'. $row->id .'"/></div>';
          return $checkbox;
        })
        ->addColumn('name', function($row)
        {
          return ucwords('<a data-bs-toggle="tooltip" data-bs-placement="top" title="Show Products" data-sorting="'.$row->name.'" href="/categorywise/products/'.$row->id.'">'.$row->name.'</a>');
        })
        ->addColumn('total_products', function($row)
        {
          if (Auth::user()->hasRole('admin')) {
            if (Auth::user()->can('category_view')) {
              $total_products = Products::where('category_id', $row->id)->where('deleted_at', NULL)->count();
              return $total_products;
            } 
          }
          if (!Auth::user()->hasRole('admin')) {
            if (Auth::user()->can('category_view')) {
              $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
              $customerId = Auth::user()->id;
              if (isset($customerUser)) {
                $customerId = $customerUser->customer_id;
              }
              $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
              $count = 0;
              foreach ($customerProducts as $key => $cProduct) {
                $product = Products::where('category_id', $row->id)->where('id', $cProduct->product_id)->where('deleted_at', NULL)->first();
                if (isset($product)) {
                  $count = $count + 1;
                }
              }
              return $count;
            }
          }
        })
        ->addColumn('is_active', function($row)
        {
          if ($row->is_active == 0) return '<span class="badge rounded-pill badge-light-danger me-1">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill badge-light-success me-1">Active</span>';
        })
        ->addColumn('action', function($row)
        {
          $btn = '<div class="dropdown">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i data-feather="more-vertical"></i>
                    </button>
                    <div class="dropdown-menu">';
          if (Auth::user()->can('product_create'))
          {
            $btn .= '<a class="dropdown-item" href="/add_category_product/'.$row->id.'">
                      <i data-feather="plus"></i>
                      <span>Add Product</span>
                  </a>';
          }
          if (Auth::user()->can('product_view'))
          {
            $btn .= '<a class="dropdown-item" href="/categorywise/products/'.$row->id.'">
                        <i data-feather="eye"></i>
                        <span>View Products</span>
                    </a>';
          }
          if (Auth::user()->can('category_update'))
          {
            $btn .= '<a class="dropdown-item" href="/category/'.$row->id.'/edit">
                      <i data-feather="edit-2"></i>
                      <span>Edit Category</span>
                  </a>';
          }
          if (Auth::user()->can('category_delete'))
          {
            $btn .= '<a class="dropdown-item" href="/delete/category/'.$row->id.'" onclick="confirmDelete(event)">
                        <i data-feather="trash"></i>
                        <span>Delete Category</span>
                    </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['checkbox', 'action','is_active','name'])
        // ->filter(function ($instance) use ($request)
        // {
        //   if (!empty($request->get('search')['value']))
        //   {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("name like '%$keyword%' ");
        //   }
        // })
        ->make(true);
    }
    return view('admin.category.category');
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.category.add_category');
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
      'name' => 'required',
    ]);
    $error = '';
    $name = $request->input('name');
    $is_active = $request->input('is_active');
    if ($is_active == 1) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if (!$name) $error .= "Category Name Is Required<br />";
    if (!$error) {
      $insArr = array();
      $insArr['name'] = $name;
      $insArr['is_active'] = $is_active;
      $id = Category::addcategory($insArr);
    }
    return redirect('/category')->withSuccess('Category has been added');
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
    $data['dataSet'] = Category::find($id);
    return view('admin.category.edit_category')->with($data);
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
    //
    $validatedData = $request->validate([
      'name' => 'required',
    ]);
    $error = '';
    $id = $request->input('id');
    $name = $request->input('name');
    $is_active = $request->input('is_active');
    if ($is_active == 1) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if (!$name) $error .= "Category Name Is Required<br/>";
    if (!$error) {
      $insArr = array();
      $insArr['name'] = $name;
      $insArr['is_active'] = $is_active;
      $id = Category::updatecategory($insArr, $id);
    }
    return redirect('/category')->withSuccess('Category has been updated');
  }
  public function addCategoryProduct(Request $request, $id)
  {
    $data['cats'] = Category::All();
    $data['units'] = DB::table('product_units')->get();
    $data['cat_id'] = $id;
    $setting = Setting::first();
    $data['setting'] = $setting;
    return view('admin.category.add_category_product')->with($data);
  }
  public function storeCategoryProduct(Request $request, $id)
  {
    // In DB -> manual_threshold = qty check, forecast_status = qty status check, forecast_days = forecast days
    $validatedData = $request->validate([
      'category_name' => 'required',
      'name' => 'required',
      // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
      'product_unit_id' => 'required',
      'weight' => 'required',
      // 'cog' => 'required',
      // 'selling_cost' => 'required',
      'price' => 'required',
    ]);
    DB::beginTransaction();
    try {
      $forecast_days = NULL;
      $alert_days = NULL;
      $manual_threshold = NULL; // qty alert
      $forecastStatus = 0;
      $automatedStatus = 0;
      if ($request->forecast_status != NULL || $request->forecast_status != '') {
        $manual_threshold = $request->manual_threshold; // qty alert
        $forecastStatus = 1;
      }
      if ($forecastStatus == '0' || $forecastStatus == '' || $forecastStatus == NULL) {
        if ($request->automated_forecast_checkbox == NULL || $request->automated_forecast_checkbox == '') {
          $forecast_days = $request->input('forecast_days');
          $alert_days = $request->input('threshold_val');
          $automatedStatus = 0;
        } else if ($request->automated_forecast_checkbox == 1) {
          $setting = Setting::where('id', 1)->first();
          if (isset($setting)) {
            $forecast_days = $setting->forecast_days;
            $alert_days = $setting->threshold_val;
            $automatedStatus = 1;
          }
        }
      }

      $error = '';
      $cat_id = $request->input('category_name');
      $name = $request->input('name');
      $product_unit_id = $request->input('product_unit_id');
      $cog = $request->input('cog');
      $shipping_cost = $request->input('shipping_cost');
      $price = $request->input('price');
      if (!$error) {
        $insArr = array();
        $insArr['category_id'] = $cat_id;
        $insArr['name'] = $name;
        $insArr['product_unit_id'] = $product_unit_id;
        $insArr['value'] = 0;
        $insArr['weight'] = $request->weight;
        $insArr['cog'] = $cog;
        $insArr['shipping_cost'] = $shipping_cost;
        $insArr['price'] = $price;
        $insArr['is_active'] = 1;
        $insArr['forecast_days'] = trim($forecast_days);
        $insArr['threshold_val'] = trim($alert_days);
        $insArr['forecast_status'] = trim($forecastStatus);
        $insArr['automated_status'] = trim($automatedStatus);
        $insArr['manual_threshold'] = trim($manual_threshold); // qty alert
        if ($request->hasFile('image')) {
          $image = time() . '.' . request()->image->getClientOriginalExtension();
          request()->image->move(public_path('images/products'), $image);
          $insArr['image'] = $image;
        }
        $id = Products::addproduct($insArr);
        $last_id = Products::orderBy('id', 'DESC')->first();
        if ($last_id != null) {
          if (Inventory::where('product_id', $last_id->id)->exists()) {
          } else {
            Inventory::create([
              'product_id' => $last_id->id,
              'qty' => '0',
              'date' => Carbon::now()
            ]);
          }
        }
      }
      DB::commit();
      return redirect('/categorywise/products/' . $cat_id . '')->withSuccess('Product has been added');
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
      return redirect()->back()->withErrors('Something went wrong');
    }
  }
  public function editCategoryProduct(Request $request, $id)
  {
    $data['dataSet'] = Products::find($id);
    $data['cats'] = Category::All();
    $data['units'] = DB::table('product_units')->get();
    $setting = Setting::first();
    $data['setting'] = $setting;
    $data['cat_id'] = $id;
    return view('admin.category.edit_category_product')->with($data);
  }
  public function updateCategoryProduct(Request $request, $id)
  {
    //
    $validatedData = $request->validate([
      'category_name' => 'required',
      'name' => 'required',
      'product_unit_id' => 'required',
      'weight' => 'required',
      'price' => 'required',
    ]);
    $getProductID = $id;
    DB::beginTransaction();
    try {
      $forecast_days = NULL;
      $alert_days = NULL;
      $manual_threshold = NULL; // qty alert
      $forecastStatus = 0;
      $automatedStatus = 0;
      if ($request->forecast_status != NULL || $request->forecast_status != '') {
        $manual_threshold = $request->manual_threshold; // qty alert
        $forecastStatus = 1;
      }
      if ($forecastStatus == '0' || $forecastStatus == '' || $forecastStatus == NULL) {
        if ($request->automated_forecast_checkbox == NULL || $request->automated_forecast_checkbox == '') {
          $forecast_days = $request->input('forecast_days');
          $alert_days = $request->input('threshold_val');
          $automatedStatus = 0;
        } else if ($request->automated_forecast_checkbox == 1) {
          $setting = Setting::where('id', 1)->first();
          if (isset($setting)) {
            $forecast_days = $setting->forecast_days;
            $alert_days = $setting->threshold_val;
            $automatedStatus = 1;
          }
        }
      }
      $error = '';
      $cat_id = $request->input('category_name');
      $name = $request->input('name');
      $product_unit_id = $request->input('product_unit_id');
      $cog = $request->input('cog');
      $shipping_cost = $request->input('shipping_cost');
      $price = $request->input('price');
      if (!$error) {
        $insArr = array();
        $insArr['category_id'] = $cat_id;
        $insArr['name'] = $name;
        $insArr['product_unit_id'] = $product_unit_id;
        $insArr['value'] = 0;
        $insArr['cog'] = $cog;
        $insArr['shipping_cost'] = $shipping_cost;
        $insArr['price'] = $price;
        $insArr['weight'] = $request->weight;
        $insArr['is_active'] = 1;
        $insArr['forecast_days'] = trim($forecast_days);
        $insArr['threshold_val'] = trim($alert_days);
        $insArr['forecast_status'] = trim($forecastStatus);
        $insArr['automated_status'] = trim($automatedStatus);
        $insArr['manual_threshold'] = trim($manual_threshold); // qty alert
        if ($request->hasFile('image')) {
          $image = time() . '.' . request()->image->getClientOriginalExtension();
          request()->image->move(public_path('images/products'), $image);
          $insArr['image'] = $image;
        } else {
          $product = Products::where('id', $id)->first();
          if (isset($product)) {
            $image = $product->image;
            $insArr['image'] = $image;
          }
        }
        $prodId = $id;
        $id = Products::updateproduct($insArr, $id);
        $updateSkuProductPrice = SkuProducts::where('product_id', $getProductID)->update([
          'purchasing_cost' => $price
        ]);
        $skuProducts = SkuProducts::where('product_id', $getProductID)->get();
        foreach ($skuProducts as $skuPkey => $sku_product) {
          if (isset($sku_product)) {
            $sku = Sku::where('id', $sku_product->sku_id)->first();
            if (isset($sku)) {
              $skuproductsData = SkuProducts::where('sku_id', $sku->id)->get();
              $totalSkuPurchasingCost = 0;
              foreach ($skuproductsData as $skukey => $skuproductdata) {
                if (isset($skuproductdata)) {
                  $totalSkuPurchasingCost += $skuproductdata->purchasing_cost;
                }
              }
              $sku->purchasing_cost = $totalSkuPurchasingCost;
              $sku->weight = $request->weight;
              $sku->save();
            }
          }
        }
      }
      DB::commit();
      return redirect('/categorywise/products/' . $cat_id . '')->withSuccess('Product has been updated');
    } catch (\Exception $e) {
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
    $category = Category::where('id', $id)->first();
    if (isset($category)) {
      $products = Products::where('category_id', $category->id)->where('deleted_at', NULL)->where('trash_type', NULL)->get();
      foreach ($products as $product) {
        if (isset($product)) {
          $customerProducts = CustomerProduct::where('product_id', $product->id)->where('deleted_at', NULL)->get();
          $customerHasProducts = CustomerHasProduct::where('product_id', $product->id)->where('deleted_at', NULL)->get();
          $skuproducts = SkuProducts::where('product_id', $product->id)->where('deleted_at', NULL)->get();
          foreach ($skuproducts as $skuproduct) {
            if (isset($skuproduct)) {
              $skuproduct->delete();
            }
          }
          foreach ($customerProducts as $cproduct) {
            if (isset($cproduct)) {
              $cproduct->delete();
            }
          }
          foreach ($customerHasProducts as $chasproduct) {
            if (isset($chasproduct)) {
              $chasproduct->delete();
            }
          }
          $product->trash_type = 2;
          $product->save();
          $product->delete();
        }
      }
      Category::where('id', $category->id)->delete();
      return back()->withSuccess('Category has been deleted');
    } else {
      return redirect()->back()->with(['error' => 'Error while finding Customer']);
    }
  }
  public function trash(Request $request)
  {
    if ($request->ajax()) {
      $trashed = Category::onlyTrashed()->get();
      return Datatables::of($trashed)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('action', function ($row) {
          $btn = '<a href="/category/' . $row->id . '/restore" class="btn btn-primary btn-sm">Restore</a>&nbsp;';
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.category.trash');
  }
  public function restore(Request $request, $id)
  {
    $category = Category::withTrashed()->where('id', $id)->first();
    if (isset($category)) {
      $products = Products::withTrashed()->where('category_id', $category->id)->where('trash_type', 2)->get();
      foreach ($products as $product) {
        if (isset($product)) {
          $product->trash_type = NULL;
          $product->deleted_at = NULL;
          $product->save();
          $customerProducts = CustomerProduct::withTrashed()->where('product_id', $product->id)->get();
          $customerHasProducts = CustomerHasProduct::withTrashed()->where('product_id', $product->id)->get();
          $skuproducts = SkuProducts::withTrashed()->where('product_id', $product->id)->get();
          foreach ($skuproducts as $skuproduct) {
            if (isset($skuproduct)) {
              $sku = Sku::withTrashed()->where('id', $skuproduct->sku_id)->first();
              if (isset($sku)) {
                $sku->deleted_at = NULL;
                $sku->save();
              }
              $skuproduct->deleted_at = NULL;
              $skuproduct->save();
            }
          }
          foreach ($customerProducts as $cproduct) {
            if (isset($cproduct)) {
              $cproduct->deleted_at = NULL;
              $cproduct->save();
            }
          }
          foreach ($customerHasProducts as $chasproduct) {
            if (isset($chasproduct)) {
              $chasproduct->deleted_at = NULL;
              $chasproduct->save();
            }
          }
        }
      }
      $category->deleted_at = NULL;
      $category->save();
      return redirect()->back()->withSuccess('Category with Products has been restored');
    } else {
      return redirect()->back()->withError('Category not found');
    }
  }
}
