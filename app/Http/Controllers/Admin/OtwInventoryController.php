<?php

namespace App\Http\Controllers\Admin;

use App\AdminModels\Category;
use Session;
use DateTime;
use Redirect;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Inventory;
use App\AdminModels\OtwInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\AdminModels\UpcomingInventory;

class OtwInventoryController extends Controller
{
  ////
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

      // $data = OTWInventory::select(['otw_inventory.*'])
      //   ->whereHas("Products", function ($q) use ($request) {
      //     if (!empty($request->get('search')['value'])) {
      //       $keyword = $request->get('search')['value'];
      //       $q->where("name", "like", "%$keyword%");
      //     }
      //   })->where('qty', '>', 0)
        // ->get();
      $data = OtwInventory::with('Products.category')->where('qty', '>', 0);
      if (!empty($request->category_id)) {
        $data->whereHas('Products', function($q) use ($request) {
          $q->where('category_id', $request->category_id);
        });
      }
      $data = $data->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('category_name', function ($row) {
          return ucwords($row->Products->category->name);
        })
        ->addColumn('name', function ($row) {
          return ucwords($row->Products->name);
        })
        ->addColumn('description', function ($row) {
          return ucwords($row->description);
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        }) // adding upcoming and otw
        ->addColumn('upcoming', function ($row) {
          $UpcomingInventory = UpcomingInventory::where('product_id', $row->Products->id)->groupBy('product_id')->sum('qty');
          return $UpcomingInventory;
        })
        ->addColumn('purchase_date', function ($row) {
          return Carbon::parse($row->purchase_date)->format('m/d/Y');
        })
        ->addColumn('shipping_date', function ($row) {
          return Carbon::parse($row->shipping_date)->format('m/d/Y');
        })
        ->addColumn('expected_delivery_date', function ($row) {
          return Carbon::parse($row->expected_delivery_date)->format('m/d/Y');
        })
        ->addColumn('days_left', function ($row) {
          $gone = 'left';
          $to = new DateTime(date("Y-m-d"));
          $from = new DateTime(Carbon::parse($row->expected_delivery_date)->format('Y-m-d'));
          if ($from < $to) {
            $gone = '<span class="text-danger">delayed</span>';
          }
          $dd = date_diff($from, $to);
          if ($dd->y < 1) {
            $y = '';
          } else {
            $y = $dd->y . 'y ';
          }
          if ($dd->m < 1) {
            $m = '';
          } else {
            $m = $dd->m . 'm ';
          }
          return ($y. $m. $dd->d.'d '.$gone);
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          $btn = '<div class="dropdown">
            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i data-feather="more-vertical"></i>
            </button>
            <div class="dropdown-menu">';
          if (Auth::user()->can('otw_inventory_move_stock')) {
            $btn .= '<a data-bs-toggle="modal" class="dropdown-item movestock" data-product-id="' . $row->product_id . '" data-row-id="' . $row->id . '"  data-otw-quantity="' . $row->qty . '" data-bs-target="#move_to_stock_modal" data-bs-placement="top" title="Add This to Stock">
                                    <i data-feather="briefcase"></i> Add this to Stock
                                </a>';
          }
          if (Auth::user()->can('otw_inventory_update')) {
            $btn .= '<a class="dropdown-item" href="/otw_inventory/' . $row->id . '/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit OTW Inventory Date">
                                                            <i data-feather="edit-2"></i> Edit
                                                        </a>';
          }
          if (Auth::user()->can('otw_inventory_delete')) {
            $btn .= '<a class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete OTW Inventory" href="/delete/otw/inventory/' . $row->id . '" onclick="return myFunction()">
                                    <i data-feather="trash"></i> Delete
                                </a>';
          }

          $btn .= '</div>
                </div>';
          return $btn;
        })
        ->rawColumns(['action', 'days_left'])
        ->make(true);
    }
    //
    $categories = Category::get();
    return view('admin.otw_inventory.inventory', compact('categories'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
    $data['products'] = Products::All();
    return view('admin.otw_inventory.add_inventory')->with($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if ($request->row_id) {
      $upcomingInventory = UpcomingInventory::where('id', $request->row_id)->first();
      $purchasing_date = Carbon::parse($upcomingInventory->created_at)->format('Y-m-d');
      if (isset($upcomingInventory)) {
        $purchasing_date = Carbon::parse($upcomingInventory->date)->format('Y-m-d');
      }
    } else {
      $purchasing_date = Carbon::now()->format('Y-m-d');
    }
    if ($request->ajax()) {
      OtwInventory::create([
        'product_id' => $request->product_id,
        'qty' => $request->qty,
        'shipping_date' => Carbon::parse($request->shipping_date)->format('Y-m-d'),
        'purchase_date' => Carbon::parse($purchasing_date)->format('Y-m-d'),
        'expected_delivery_date' => Carbon::parse($request->expected_delivery_date)->format('Y-m-d'),
        'description' => $request->description,
      ]);
      return response()->json(['data' => 'success']);
    } else {
      $loginUserData = Auth::user();
      $validatedData = $request->validate([
        'product_id' => 'required',
        'qty' => 'required',
        'shipping_date' => 'required',
        'expected_delivery_date' => 'required',
      ]);
      // check if request is to move stock from upcoming to otw  
      if ($request->has('row_id')) {
        $row_upcoming_inventory = UpcomingInventory::find($request->row_id);
        $updated_inventory = $row_upcoming_inventory->qty - $request->qty;
        $row_upcoming_inventory->qty = $updated_inventory;
        $row_upcoming_inventory->save();
      }
      OtwInventory::create([
        'product_id' => $request->product_id,
        'qty' => $request->qty,
        'shipping_date' => Carbon::parse($request->shipping_date)->format('Y-m-d'),
        'purchase_date' => Carbon::parse($purchasing_date)->format('Y-m-d'),
        'expected_delivery_date' => Carbon::parse($request->expected_delivery_date)->format('Y-m-d'),
        'description' => $request->description,
      ]);
      return redirect('/otw_inventory');
    }
  }

  public function storeStock(Request $request)
  {
    $loginUserData = Auth::user();

    $validatedData = $request->validate([
      'product_id' => 'required',
      'qty' => 'required',
      'date' => 'required',
    ]);

    if ($request->has('row_id')) {
      $OtwInventory = OtwInventory::find($request->row_id);
      $updated_inventory = $OtwInventory->qty - $request->qty;
      $OtwInventory->qty = $updated_inventory;
      $OtwInventory->save();
    }
    // finding inventory in stock if finded then update otherwiise insert
    $inventory = Inventory::where('product_id', $request->product_id)->first();
    if ($inventory) {
      $updated_inventory = $inventory->qty + $request->qty;
      $inventory->qty = $updated_inventory;
      $inventory->save();
    } else {
      $inventory = Inventory::create([
        'product_id' => $request->product_id,
        'qty' => $request->qty
      ]);
    }
    $inventoryData = Inventory::where('product_id', $request->product_id)->first();
    InventoryHistory::create([
      'product_id' => $request->product_id,
      'qty' => $request->qty,
      'user_id' => Auth()->user()->id,
      'supplier_inventory_received' => $request->qty,
      'description' => $request->description,
      'total' => $inventoryData->qty,
      'user_id' => Auth::user()->id,
      'user_name' => Auth::user()->name,
    ]);
    return redirect('/otw_inventory');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $upcomingInventoryProduct = OtwInventory::with('Products:id,name')->where('product_id', $id)->where('qty', '>', 0)->get();
    $success = false;
    if (count($upcomingInventoryProduct) > 0) {
      $upcomingInventoryProduct->toArray();
      $success = true;
    }
    if ($request->ajax()) {
      $data = $upcomingInventoryProduct;
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('product_name', function ($row) {
          return ucwords($row->Products->name);
        })
        ->addColumn('description', function ($row) {
          return ucwords($row->description);
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('shipping_date', function ($row) {
          return Carbon::parse($row->shipping_date)->format('m/d/Y');
        })
        ->addColumn('expected_delivery_date', function ($row) {
          return Carbon::parse($row->expected_delivery_date)->format('m/d/Y');
        })
        ->addColumn('days_left', function ($row) {
          // $now = time(); // or your date as well
          // $your_date = strtotime($row->date);
          // $datediff = $now - $your_date;
          // $days=round($datediff / (60 * 60 * 24));
          // return ($days==(-0) || $days == (+0)) ? $days . 'days' :0;
          // dd(round($datediff / (60 * 60 * 24)));
          // $date1 = new DateTime(); // today date
          // $date2 = new DateTime($row->date);
          // // To calculate the time difference of two dates
          // $Difference_In_Time = $date1->diff($date2);
          // // dd($Difference_In_Time);
          // // To calculate the no. of days between two dates
          // // dd($Difference_In_Time->format('%R%a days'));
          // $Difference_In_Days = $Difference_In_Time / (1000 * 3600 * 24);
          // dd($Difference_In_Days);
          // return (round($datediff / (60 * 60 * 24)) . 'days');
          $date1 = new DateTime(); // today date
          $date2 = new DateTime($row->expected_delivery_date);
          // To calculate the time difference of two dates
          $Difference_In_Time = $date1->diff($date2);
          $date = $Difference_In_Time->format('%R%a');
          if ($date == '+0' || $date == '-0') {
            $date = 0 . ' day';
          } else {
            $date = $date . ' days';
          }
          // To calculate the no. of days between two dates
          // dd($Difference_In_Time->format('%R%a days'));
          // $Difference_In_Days = $Difference_In_Time / (1000 * 3600 * 24);
          return ($date);
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          if (Auth::user()->can('otw_inventory_move_stock')) {
            $btn .= '<p><a data-bs-toggle="modal" class="movestock" data-product-id="' . $row->product_id . '" data-row-id="' . $row->id . '"  data-otw-quantity="' . $row->qty . '" data-bs-target="#move_to_stock_modal" data-bs-placement="top" title="Add This to Stock">
                                  <i data-feather="briefcase"></i>
                              </a>';
          }
          if (Auth::user()->can('otw_inventory_update')) {
            $btn .= '<a href="/otw_inventory/' . $row->id . '/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit OTW Inventory Date">
                                                          <i data-feather="edit-2"></i>
                                                      </a>';
          }
          if (Auth::user()->can('otw_inventory_delete')) {
            $btn .= '<a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete OTW Inventory" href="/delete/otw/inventory/' . $row->id . '" onclick="return myFunction()">
                                  <i data-feather="trash"></i>
                              </a>';
          }

          $btn .= '</p>';
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return response()->json(['upcomingInventory' => $upcomingInventoryProduct, 'success' => $success]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $data['dataSet'] = OtwInventory::find($id);
    $data['products'] = Products::All();
    return view('admin.otw_inventory.edit_inventory')->with($data);
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
      'shipping_date' => 'required',
      'expected_delivery_date' => 'required',
    ]);


    $date = $request->input('shipping_date');

    $storeData = OtwInventory::find($id);

    $storeData->shipping_date = Carbon::parse($request->input('shipping_date'))->format('Y-m-d');
    $storeData->expected_delivery_date = Carbon::parse($request->input('expected_delivery_date'))->format('Y-m-d');
    $storeData->qty = $request->input('qty');
    $storeData->description = $request->description;
    $storeData->save();
    return redirect('/otw_inventory');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // $getOtwProductCount = DB::table('otw_inventory')->where('id', $id)->first();
    // $getUpcomingProductCount = DB::table('upcoming_inventory')->where('product_id', $getOtwProductCount->product_id)->first();
    // $qty = 0;
    // $qty2 = 0;
    // if (isset($getUpcomingProductCount)) {
    //   $qty2 = $getUpcomingProductCount->qty;
    // }
    // if (isset($getOtwProductCount)) {
    //   $qty = $getOtwProductCount->qty;
    //   $newQty = $qty + $qty2;
    //   UpcomingInventory::where('product_id', $getOtwProductCount->product_id)->update(array('qty' => $newQty));
    // }

    // $loginUserData = Auth::user();
    $data = OtwInventory::find($id);
    $data->delete();

    return redirect()->back()->withSuccess('Deleted Successfully');
  }
}
