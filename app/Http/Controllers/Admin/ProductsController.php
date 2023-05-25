<?php

namespace App\Http\Controllers\Admin;

use Session;
use DateTime;
use Redirect;
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use App\Models\Setting;
use App\Traits\Forecast;
use Carbon\CarbonPeriod;
use App\AdminModels\Units;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\Models\SkuProducts;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\AdminModels\Category;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\Models\CustomerHasSku;
use App\Models\CustomerProduct;
use App\AdminModels\OrderDetails;
use App\AdminModels\OtwInventory;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\AdminModels\UpcomingInventory;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class ProductsController extends Controller
{
  //
  use Forecast;
  use ImageUpload;
  public function __construct()
  {
    $this->middleware('auth');
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
          return '<center><span class="badge rounded-pill me-1" data-sorting="#001" style="background-color: red; color: white">Order Now</span></center>';
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
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $data = $this->forecastData();
      function getQtyAlerts($row)
      {
        if ($row->forecast_status == 1) {
          if ($row->invent_qty < $row->manual_threshold) {
            return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">' . $row->invent_qty . '</span>';
          } else {
            return $row->invent_qty;
          }
        } else {
          return $row->invent_qty;
        }
      }
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('btn', function ($row) {
          return '<div class="text-center"><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></div>';
        })
        ->addColumn('category_name', function ($row) {
          if (isset($row->category)) {
            return ucwords($row->category->name);
          }
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('forecast_val', function ($row) {
          return $this->forecastValues($row);
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
        ->addColumn('pqty', function ($row) {
          return getQtyAlerts($row);
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
          if (isset($row->product_unit)) {
            if ($row->product_unit->unit_type == 1) {
              return ($row->weight / 16);
            } else {
              return ($row->weight);
            }
          } else {
            return ($row->weight);
          }
        })
        ->addColumn('value', function ($row) {
          $unit = Units::where('id', $row->product_unit_id)->first();
          if (isset($unit)) {
            return ucwords($unit->name);
          } else {
            return 'Not Set';
          }
        })
        ->addColumn('cog', function ($row) {
          return $row->cog;
        })
        ->addColumn('shipping_cost', function ($row) {
          return $row->shipping_cost;
        })
        ->addColumn('price', function ($row) {
          return $row->price;
        })
        ->setRowClass(function ($row) {
          if ($row->forecast_status == 1) {
            if ($row->invent_qty < $row->manual_threshold) {
              return 'dbRowColor';
            } else {
              return '';
            }
          } else {
            return '';
          }
        })
        ->addColumn('image', function ($row) {
          $files = '';
          if (env('APP_ENV') == 'production') {
            $files = url('public/' . $row->image);
          }
          return '<img src="' . $files . '" border="0" width="40" class="img-rounded product-image" align="center" alt="product-image" />';
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill me-1" style="background-color: green; color: white">Active</span>';
        })
        ->addColumn('action', function ($row) {
          return $this->productsActionBtn($row, null);
        })
        ->rawColumns(['name', 'action', 'is_active', 'image', 'forecast_val', 'pqty', 'forecast_statuses', 'btn'])
        ->make(true);
    }
    return view('admin.products.products');
  }

  public function categoryProducts(Request $request, $catId = null)
  {
    $cid = $request->input('cid');
    if ($request->ajax()) {
      $cid = $request->input('cid');
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
            return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">' . $row->invent_qty . '</span>';
          } else {
            return $row->invent_qty;
          }
        } else {
          return $row->invent_qty;
        }
      }
      // calling function from trait ForcastTrait
      return Datatables::of($this->categoryProductData($cid))
        ->addIndexColumn()
        ->addColumn('sr', function ($row) {
          return '';
        })
        ->addColumn('btn', function ($row) {
          return '<center><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></center>';
        })
        ->addColumn('category_name', function ($row) {
          return ucwords($row->category_name);
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('forecast_val', function ($row) {
          return $this->forecastValues($row);
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
        ->addColumn('pqty', function ($row) {
          return getQtyAlerts($row);
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
          if (isset($row->product_unit)) {
            if ($row->product_unit->unit_type == 1) {
              return ($row->weight / 16);
            } else {
              return ($row->weight);
            }
          } else {
            return ($row->weight);
          }
        })
        ->addColumn('value', function ($row) {
          $unit = Units::where('id', $row->product_unit_id)->first();
          if (isset($unit)) {
            return ucwords($unit->name);
          } else {
            return 'Not Set';
          }
        })
        ->addColumn('cog', function ($row) {
          return $row->cog;
        })
        ->addColumn('shipping_cost', function ($row) {
          return $row->shipping_cost;
        })
        ->addColumn('price', function ($row) {
          return $row->price;
        })
        ->setRowClass(function ($row) {
          if ($row->forecast_status == 1) {
            if ($row->invent_qty < $row->manual_threshold) {
              return 'dbRowColor';
            } else {
              return '';
            }
          } else {
            return '';
          }
        })
        ->addColumn('image', function ($row) {
          $files = '';
          if (env('APP_ENV') == 'production') {
            $files = url('public/' . $row->image);
          }
          return '<img src="' . $files . '" border="0" width="40" class="img-rounded product-image" align="center" alt="product-image" />';
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill me-1" style="background-color: green; color: white">Active</span>';
        })
        ->addColumn('action', function ($row) {
          return $this->productsActionBtn($row, $row->cat_id);
        })
        ->rawColumns(['btn', 'action', 'is_active', 'image', 'forecast_val', 'pqty', 'forecast_statuses'])
        ->make(true);
    }
    $products = Products::get();
    return view('admin.products.categoryProducts', compact('products'))->with('cat_id', $catId);
  }

  public function selectedCategoriesProducts(Request $request)
  {
    $products = Products::get();
    $categories_ids = $request->categories_id;
    if ($request->ajax()) {
      $getcategories_ids = explode(',', $request->cid);
      $cid = $request->input('cid');
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
            return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">' . $row->invent_qty . '</span>';
          } else {
            return $row->invent_qty;
          }
        } else {
          return $row->invent_qty;
        }
      }
      // calling function from trait ForcastTrait
      return Datatables::of($this->selectedCategoriesProductsData($getcategories_ids))
        ->addIndexColumn()
        ->addColumn('sr', function ($row) {
          return '';
        })
        ->addColumn('btn', function ($row) {
          return '<center><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></center>';
        })
        ->addColumn('category_name', function ($row) {
          return ucwords($row->category_name);
        })
        ->addColumn('btn', function ($row) {
          return '<center><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></center>';
        })
        ->addColumn('category_name', function ($row) {
          return ucwords($row->category_name);
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('forecast_val', function ($row) {
          return $this->forecastValues($row);
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
        ->addColumn('pqty', function ($row) {
          return getQtyAlerts($row);
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
          if (isset($row->product_unit)) {
            if ($row->product_unit->unit_type == 1) {
              return ($row->weight / 16);
            } else {
              return ($row->weight);
            }
          } else {
            return ($row->weight);
          }
        })
        ->addColumn('value', function ($row) {
          $unit = Units::where('id', $row->product_unit_id)->first();
          if (isset($unit)) {
            return ucwords($unit->name);
          } else {
            return 'Not Set';
          }
        })
        ->addColumn('cog', function ($row) {
          return $row->cog;
        })
        ->addColumn('shipping_cost', function ($row) {
          return $row->shipping_cost;
        })
        ->addColumn('price', function ($row) {
          return $row->price;
        })
        ->setRowClass(function ($row) {
          if ($row->forecast_status == 1) {
            if ($row->invent_qty < $row->manual_threshold) {
              return 'dbRowColor';
            } else {
              return '';
            }
          } else {
            return '';
          }
        })
        ->addColumn('image', function ($row) {
          $files = '';
          if (env('APP_ENV') == 'production') {
            $files = url('public/' . $row->image);
          }
          return '<img src="' . $files . '" border="0" width="40" class="img-rounded product-image" align="center" alt="product-image" />';
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill me-1" style="background-color: green; color: white">Active</span>';
        })
        ->addColumn('action', function ($row) {
          return $this->productsActionBtn($row, $row->cat_id);
        })
        ->rawColumns(['btn', 'action', 'is_active', 'image', 'forecast_val', 'pqty', 'forecast_statuses'])
        ->make(true);
    }
    return view('admin.products.selected_categories_products', compact('products', 'categories_ids'));
  }

  function productsActionBtn($row, $catId)
  {
    $btn = '<div class="dropdown products-dropdown">
              <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i data-feather="more-vertical"></i>
              </button>
              <div class="dropdown-menu products-dropdown-menu">';
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
      if (!is_null($catId)) {
        $btn .= '<a class="dropdown-item" href="/products/' . $row->id . '/edit?cat_id='.$catId.'">
                                    <i data-feather="edit-2"></i>
                                    <span>Edit Product</span>
                                </a>';
      } else {
        $btn .= '<a class="dropdown-item" href="/products/' . $row->id . '/edit">
                                    <i data-feather="edit-2"></i>
                                    <span>Edit Product</span>
                                </a>';
      }
    }
    if (Auth::user()->can('product_delete')) {
      $btn .= '<a class="dropdown-item" href="/delete/product/' . $row->id . '" onclick="confirmDelete(event)">
                    <i data-feather="trash-2"></i>
                    <span>Delete Product</span>
                </a>';
      //adding Upcoming
          $btn .= '<a class="dropdown-item enter-pincode upcoming-inventory-each-item" data-prod-id="' . $row->id . '"  data-type="Upcoming"  data-bs-toggle="modal" data-bs-target="#upcomingInventoryModel">
          <i data-feather="log-in"></i>
          <span>Purchase Order</span>
       </a>';
      //     //  Otw
      //     $btn .= '<a class="dropdown-item enter-pincode otw-inventory-each-item" data-prod-id="' . $row->id . '"  data-type="otw"  data-bs-toggle="modal" data-bs-target="#otwInventoryModel">
      //     <i data-feather="truck"></i>
      //     <span>OTW</span>
      //  </a>';
    }
    $btn .= '<a href="/inventory-history/'.$row->id.'" _target="blank" class="dropdown-item" data-prod-id="' . $row->id . '">
              <i data-feather="eye"></i>
              <span>View Inventory History</span>
            </a>';
    $btn .= '</div></div>';
    return $btn;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($category_id = null)
  {
    $data['cats'] = Category::All();
    $data['units'] = DB::table('product_units')->get();
    $data['cat_id'] = $category_id;
    $setting = Setting::first();
    $data['setting'] = $setting;
    return view('admin.products.add_product')->with($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
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
    $getWeight = $request->weight;
    $weightType = Units::where('id', $product_unit_id)->first();
    if (isset($weightType)) {
      if ($weightType->unit_type == 1) {
        $getWeight = $request->weight * 16;
      }
    }
    if (!$error) {
      $insArr = array();
      $insArr['category_id'] = $cat_id;
      $insArr['name'] = $name;
      $insArr['product_unit_id'] = $product_unit_id;
      $insArr['value'] = 0;
      $insArr['weight'] = $getWeight;
      $insArr['cog'] = $cog;
      $insArr['shipping_cost'] = $shipping_cost;
      $insArr['price'] = $price;
      $insArr['is_active'] = 1;
      $insArr['forecast_days'] = trim($forecast_days);
      $insArr['threshold_val'] = trim($alert_days);
      $insArr['forecast_status'] = trim($forecastStatus);
      $insArr['automated_status'] = trim($automatedStatus);
      $insArr['manual_threshold'] = trim($manual_threshold); // qty alert
      $insArr['created_at'] = Carbon::now();
      $insArr['updated_at'] = Carbon::now();
      if ($request->hasFile('image')) {
        // // $image = time() . '.' . request()->image->getClientOriginalExtension();
        // // request()->image->move(public_path('images/products'), $image);
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        //get extension of file
        //define directory to store images
        $directory = 'public/';
        //change filename to a random sha1 plus current time
        $filename = "warehousesystem" . substr(sha1(rand(0, 5) . time()), 0, 15) . rand(0, 100000) . "." . $extension;
        //Move the uploaded file to temp directory
        $this->uploadImage($file, $filename, $directory);
        // $name = time() . '.' . request()->image->getClientOriginalExtension();
        // $photo_file_path = $file->storePublicly(
        //   'public/'.$name
        // );
        // $this->uploadImage($file, $photo_file_path);
        $insArr['image'] = $filename;
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
    return redirect('/products')->withSuccess('Product has been added');
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    if ($request->ajax()) {
      $product = CustomerHasProduct::with('products.product_unit')->where('brand_id', $request->brand_id)->where('product_id', $id)->where('customer_id', $request->customer_id)->first();
      if ($product) {
        return response()->json([
          'status' => 'success',
          'data' => $product
        ], 200);
      } else {
        return response()->json([
          'status' => 'failed',
          'message' => 'Unable to Find product'
        ], 200);
      }
    }
  }
  public function getProductstoCustomer(Request $request)
  {
    $product = Products::where('id', $request->product_id)->first();
    return response()->json($product);
  }
  public function getProductDetailsSku(Request $request)
  {
    $productSku = SkuProducts::where('sku_id', $request->skuId)->where('product_id', $request->id)->first();
    $product = Products::where('id', $request->id)->first();
    return response()->json([
      'status' => 'success',
      'skuproduct' => $productSku,
      'product' => $product
    ], 200);
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $data['dataSet'] = Products::with('product_unit')->find($id);
    $data['cats'] = Category::All();
    $data['units'] = DB::table('product_units')->get();
    $setting = Setting::first();
    $data['setting'] = $setting;
    return view('admin.products.edit_product')->with($data);
  }
  public function getProductDetails(Request $request)
  {
    $id = $request->input('product_id');
    $data = Products::find($id);
    return $data;
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
    try {
      $validatedData = $request->validate([
        'category_name' => 'required',
        'name' => 'required',
        'product_unit_id' => 'required',
        'weight' => 'required',
        'price' => 'required',
      ]);
      $getProductID = $id;
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
      $getWeight = $request->weight;
      $weightType = Units::where('id', $product_unit_id)->first();
      if (isset($weightType)) {
        if ($weightType->unit_type == 1) {
          $getWeight = $request->weight * 16;
        }
      }
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
        $insArr['weight'] = $getWeight;
        $insArr['is_active'] = 1;
        $insArr['forecast_days'] = trim($forecast_days);
        $insArr['threshold_val'] = trim($alert_days);
        $insArr['forecast_status'] = trim($forecastStatus);
        $insArr['automated_status'] = trim($automatedStatus);
        $insArr['manual_threshold'] = trim($manual_threshold); // qty alert
        if ($request->hasFile('image')) {
          // // $image = time() . '.' . request()->image->getClientOriginalExtension();
          // // request()->image->move(public_path('images/products'), $image);
          $file = $request->file('image');
          $extension = $file->getClientOriginalExtension();
          //get extension of file
          //define directory to store images
          $directory = 'public/';
          //change filename to a random sha1 plus current time
          $filename = "warehousesystem" . substr(sha1(rand(0, 5) . time()), 0, 15) . rand(0, 100000) . "." . $extension;
          //Move the uploaded file to temp directory
          $this->uploadImage($file, $filename, $directory);
          // $name = time() . '.' . request()->image->getClientOriginalExtension();
          // $photo_file_path = $file->storePublicly(
          //   'public/'.$name
          // );
          // $this->uploadImage($file, $photo_file_path);
          $insArr['image'] = $filename;
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
              $sku->weight = $getWeight;
              $sku->save();
            }
          }
        }
      }
      if (!empty($request->cat_id)) {
        return redirect('/categorywise/products/'.$request->cat_id)->withSuccess('Product has been updated');
      }
      return redirect('/products')->withSuccess('Product has been updated');
    } catch (\Exception $e) {
      DB::rollback();
      return view('admin.server_error');
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
    $data = Products::where('id', $id)->where('deleted_at', NULL)->where('trash_type', NULL)->first();
    if (isset($data)) {
      $customerProducts = CustomerProduct::where('product_id', $data->id)->where('deleted_at', NULL)->get();
      $customerHasProducts = CustomerHasProduct::where('product_id', $data->id)->where('deleted_at', NULL)->get();
      $skuproducts = SkuProducts::where('product_id', $data->id)->where('deleted_at', NULL)->get();
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
      $data->trash_type = 1;
      $data->save();
      $data->delete();
      return redirect()->back()->withSuccess('Product has been deleted');
    } else {
      return redirect()->back()->withError('Something went wrong');
    }
  }
  /**
   * Add inventory to Product
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function addInventory($id, Request $request)
  {
    if ($request->inventory < 0) {
      return response()->json([
        'status' => 'failed',
        'message' => 'Negative value not allowed'
      ]);
    } else {
      $product = Products::where('id', $id)->first();
      if ($product) {
        $inventory = Inventory::where('product_id', $id)->first();
        if ($inventory) {
          $inventory->qty = $inventory->qty + $request->inventory;
          $inventory->save();
          $inventory_history = InventoryHistory::create([
            'product_id' => $id,
            'qty' => $request->inventory,
            'date' => Carbon::now(),
            'status' => 2,
            'manual_add' => $request->inventory,
            'description' => $request->description,
            'total' => $inventory->qty,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
          ]);
          return response()->json([
            'status' => 'success',
            'message' => 'Inventory added Successfully'
          ], 200);
        } else {
          $inventory = Inventory::create([
            'product_id' => $product->id,
            'qty' => $request->inventory,
            'date' => now(),
          ]);
          $inventory_history = InventoryHistory::create([
            'product_id' => $id,
            'qty' => $request->inventory,
            'date' => Carbon::now(),
            'status' => 2,
            'manual_add' => $request->inventory,
            'total' => $inventory->qty,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
            'description' => $request->description
          ]);
          return response()->json([
            'status' => 'success',
            'message' => 'Inventory added Successfully'
          ], 200);
        }
      } else {
        return response()->json([
          'status' => 'failed',
          'message' => "Error while loading product"
        ], 200);
      }
    }
  }
  public function reduceInventory($id, Request $request)
  {
    $product = Products::where('id', $id)->first();
    if ($request->inventory < 0) {
      return response()->json([
        'status' => 'failed',
        'message' => 'Negative value not allowed'
      ]);
    } else {
      if ($product) {
        $inventory = Inventory::where('product_id', $id)->first();
        if ($inventory) {
          if ($inventory->qty >= $request->inventory) {
            $inventory->qty = $inventory->qty - $request->inventory;
            $inventory->save();
            $inventory_history = InventoryHistory::create([
              'product_id' => $id,
              'qty' => ('-' . $request->inventory),
              'date' => Carbon::now(),
              'status' => 2,
              'manual_reduce' => $request->inventory,
              'description' => $request->description,
              'total' => $inventory->qty,
              'user_id' => Auth::user()->id,
              'user_name' => Auth::user()->name,
            ]);
            return response()->json([
              'status' => 'success',
              'message' => 'Inventory Reduced Successfully'
            ], 200);
          } else {
            return response()->json([
              'status' => 'failed',
              'message' => "Check Inventory Quantity First"
            ], 200);
          }
        } else {
          return response()->json([
            'status' => 'failed',
            'message' => "Check Inventory First"
          ], 200);
        }
      } else {
        return response()->json([
          'status' => 'failed',
          'message' => "Error while loading product"
        ], 200);
      }
    }
  }
  public function productHistory(Request $request, $id = 0)
  {
    $pid = $request->input('pid');
    if ($request->ajax()) {
      $pid = $request->input('pid');
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('username', function ($row) {
          return ucwords($row->username);
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('qty', function ($row) {
          return $row->qty;
        })
        ->addColumn('date', function ($row) {
          return (date("m/d/Y h:i:sa", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          return $btn = "<button class='btn btn-primary btn-sm revert' data-history-id='" . $row->id . "'>Revert</button>";
        })
        // ->filter(function ($instance) use ($request) {
        //   $pid = $_GET["pid"];
        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("inventory_history.qty like '%$keyword%' OR products.name like '%$keyword%' OR users.name like '%$keyword%' AND inventory_history.product_id=$pid");
        //   }
        // })
        ->make(true);
    }
    return view('admin.products.view_history')->with('product_id', $id);
  }
  public function viewOrderDetails(Request $request, $id)
  {
    if ($request->ajax()) {
      $sku_products = SkuProducts::where('product_id', $request->id)->get();
      $get_qty = 0;
      $getarr = array();
      $keys = array('get_qty', 'date');
      foreach ($sku_products as $skuproduct) {
        $getarr = array();
        $keys = array('get_qty', 'date');
        $orders = OrderDetails::where('sku_id', $skuproduct->sku_id)
          ->whereYear('created_at', date('Y'))
          ->where('created_at', '>', Carbon::now()->subDays(10))
          ->groupBy(DB::raw('Date(created_at)'))
          ->get();
        foreach ($orders as $order) {
          $query = OrderDetails::whereDate('created_at', date_format($order->created_at, 'Y-m-d'))->get();
          $get_qty = 0;
          foreach ($query as $que) {
            $get_qty += $que->qty;
          }
          array_push($getarr, array_combine($keys, [$get_qty, $order->created_at->format('d/m/Y')]));
        }
      }
      return Datatables::of($getarr)
        ->addIndexColumn()->addColumn('get_qty', function ($row) {
          return ucwords($row['get_qty']);
        })
        ->addColumn('date', function ($row) {
          return ucwords($row['date']);
        })
        ->make(true);
    }
    return view('admin.products.view_order_details')->with('product_id', $id);
  }
  public function trash(Request $request)
  {
    if ($request->ajax()) {
      $trashed = Products::onlyTrashed()->get();
      return Datatables::of($trashed)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('weight', function ($row) {
          return number_format($row->weight, 2);
        })
        ->addColumn('price', function ($row) {
          return '$' . number_format($row->price, 2);
        })
        ->addColumn('action', function ($row) {
          $btn = '<a href="/products/' . $row->id . '/restore" class="btn btn-primary btn-sm">Restore</a>&nbsp;';
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.products.trash');
  }
  public function restore(Request $request, $id)
  {
    $product = Products::withTrashed()->where('id', $id)->first();
    if (isset($product)) {
      if (isset($product->category)) {
        if ($product->trash_type == 1) {
          $product->trash_type = NULL;
          $product->deleted_at = NULL;
          $product->save();
          $customerProducts = CustomerProduct::withTrashed()->where('product_id', $id)->get();
          $customerHasProducts = CustomerHasProduct::withTrashed()->where('product_id', $id)->get();
          $skuproducts = SkuProducts::withTrashed()->where('product_id', $id)->get();
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
          return redirect('/products')->withSuccess('Product has been restored');
        } else {
          return redirect()->back()->withError('Revert category first');
        }
      } else {
        return redirect()->back()->withError('No category found');
      }
    } else {
      return redirect()->back()->withError('No product found');
    }
  }
  public function resetTableSize()
  {
    $setting = Setting::where('id', 1)->first();
    if (isset($setting)) {
      $setting->state = '{
            "time": "1643054368534",
            "order": [
                [
                    "1",
                    "asc"
                ]
            ],
            "start": "0",
            "length": "50",
            "search": {
                "regex": "false",
                "smart": "true",
                "search": null,
                "caseInsensitive": "true"
            },
            "columns": [
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                }
            ],
            "colResize": {
                "widths": [
                    "25",
                    "286",
                    "448",
                    "79",
                    "58.6667",
                    "58.6667",
                    "58.6667",
                    "105",
                    "83",
                    "159",
                    "129",
                    "79",
                    "60"
                ]
            }
        }';
      $setting->save();
      return redirect()->back();
    }
  }
  public function getProductDetail($id)
  {
    $product = Products::with('inventory')->where('id', $id)->first();
    return response()->json($product);
  }
  public function resetInventoryQty(Request $request)
  {
    $product = Products::where('id', $request->product_id)->first();
    if ($request->inventory < 0) {
      return response()->json([
        'status' => 'failed',
        'message' => 'Negative value not allowed'
      ]);
    } else {
      if ($product) {
        $inventory = Inventory::where('product_id', $request->product_id)->first();
        if ($inventory) {
          if ($inventory->qty != 0) {
            $inventory_history = InventoryHistory::create([
              'product_id' => $request->product_id,
              'qty' => ('-' . $inventory->qty),
              'date' => Carbon::now(),
              'status' => 2,
              'user_id' => Auth::user()->id,
              'user_name' => Auth::user()->name,
              'manual_reduce' => $inventory->qty,
              'total' => 0,
              'description' => 'Reset to 0'
            ]);
            $inventory->qty = $request->inventory;
            $inventory->save();
          }
          return response()->json([
            'success' => true,
            'message' => 'Reset to 0 Successfully'
          ], 200);
        } else {
          return response()->json([
            'error' => true,
            'message' => "Check Inventory First"
          ], 200);
        }
      } else {
        return response()->json([
          'error' => true,
          'message' => "Error while loading product"
        ], 200);
      }
    }
  }
  public function getSalesHistory($p = null, $id)
  {
    $total_sale = 0;
    $total_sale = DB::table('inventory_history')->where('product_id', $id)->whereDate('created_at', $p->toDateString())
      ->select(DB::raw('SUM(inventory_history.sales) as total_sales'), DB::raw('SUM(inventory_history.cancel_order_add) as cancelled_orders'), DB::raw('SUM(inventory_history.edit_batch_qty) as batch_edited'))->first();
    $cancelled = $total_sale->cancelled_orders;
    $res = $total_sale->total_sales - $cancelled - $total_sale->batch_edited;
    if ($res < 0) {
      $res = 0;
    }
    return ($res);
  }
  public function getProductSales(Request $request, $id)
  {
    ////////////////////////////////////////////////////////////////////////////////////////
    // Forecast Sales
    $arrData = array();
    $forecastKeys = array('date', 'sale');
    $php_date1 = Carbon::now()->subDays(12);
    ////////////////////////////////////////////////////////////////////////////////////////
    $datePeriod = CarbonPeriod::create($php_date1, Carbon::now());
    $datePeriod = $datePeriod->toArray();
    foreach (($datePeriod) as $p) {
      $total_sale = $this->getSalesHistory($p, $id);
      if ($total_sale == null) {
        $total_sale = 0;
      }
      array_push($arrData, array_combine($forecastKeys, [Carbon::parse($p->toDateString())->format('m/d/Y'), $total_sale]));
    }
    return response()->json($arrData);
    // Forecast Sales
    ///////////////////////////////////////////////////////////////////////////////////////////
  }
  public function getProductHistory(Request $request)
  {
    $arr = array();
    $keys = array('date', 'manual_add', 'edit_batch_qty', 'cancel_order_add', 'supplier_inventory_received', 'return_add', 'return_edited', 'manual_reduce', 'sales', 'total', 'available_inventory');
    $availableInventory = 0;
    $productInventory = Inventory::where('product_id', $request->product_id)->first();
    if (isset($productInventory)) {
      $availableInventory = $productInventory->qty;
    }
    $lastThirtyDays = date('Y-m-d', strtotime("-30 days"));
    $dateRange = CarbonPeriod::create($lastThirtyDays, Carbon::now()->format('Y-m-d'));
    $dateRange = $dateRange->toArray();
    foreach (($dateRange) as $dkey => $date) {
      $inventoryHistoryData = InventoryHistory::where('product_id', $request->product_id)
        // ->orderBy('id', 'DESC')
        // ->orderBY('created_at', 'DESC')
        // ->where('sales', '>', 0)
        ->whereDate('created_at', '=', Carbon::parse($date->toDateString())->format('Y-m-d'))
        ->select('date', 'manual_add', 'edit_batch_qty', 'cancel_order_add', 'supplier_inventory_received', 'return_add', 'return_edited', 'manual_reduce', 'sales', 'total')
        ->get();
      $manual_Add = 0;
      $edit_Batch_Qty = 0;
      $cancel_Order_Add = 0;
      $supplier_Add = 0;
      $return_Add = 0;
      $return_Edited = 0;
      $manual_Reduce = 0;
      $sales = 0;
      $total = 0;
      foreach ($inventoryHistoryData as $ikey => $inventory) {
        $manual_Add += $inventory->manual_add;
        $edit_Batch_Qty += $inventory->edit_batch_qty;
        $cancel_Order_Add += $inventory->cancel_order_add;
        $supplier_Add += $inventory->supplier_inventory_received;
        $return_Add += $inventory->return_add;
        $return_Edited += $inventory->return_edited;
        $manual_Reduce += $inventory->manual_reduce;
        $sales += $inventory->sales;
        $total = $inventory->total;
      }
      array_push($arr, array_combine($keys, [Carbon::parse($date->toDateString())->format('m/d/Y'), $manual_Add, $edit_Batch_Qty, $cancel_Order_Add, $supplier_Add, $return_Add, $return_Edited, $manual_Reduce, $sales, $total, $availableInventory]));
    }
    return response()->json(['getarr' => $arr]);
  }
}
