<?php

namespace App\Http\Controllers\Admin;

use App\AdminModels\Category;
use DB;
use Date;
use Session;
use DateTime;
use Redirect;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\AdminModels\OtwInventory;
use App\AdminModels\UpcomingInventory;

class UpcomingInventoryController extends Controller
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
      // $data = UpcomingInventory::select(['upcoming_inventory.*'])
      //   ->whereHas("Products.category", function ($q) use ($request) {
      //     if (!empty($request->get('search')['value'])) {
      //       $keyword = $request->get('search')['value'];
      //       $q->where("Products.name", "like", "%$keyword%");
      //     }
      //   })->where('qty', '>', 0)
      //   ->get();
      $data = UpcomingInventory::with('Products.category')->where('qty', '>', 0);
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
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('otw', function ($row) {
          $otwQty = OtwInventory::where('product_id', $row->Products->id)->groupBy('product_id')->sum('qty');
          return $otwQty;
        })
        ->addColumn('description', function ($row) {
          // $html = "<div style='width:100px;'>" . ucwords($row->description) . "</div";
          return ucwords($row->description);
        })
        ->addColumn('shipping_date', function ($row) {
          return Carbon::parse($row->shipping_date)->format('m/d/Y');
        })
        ->addColumn('purchase_date', function ($row) {
          return Carbon::parse($row->date)->format('m/d/Y');
        })
        ->addColumn('days_left', function ($row) {
          $gone = '';
          $from = new DateTime(Carbon::parse($row->date)->format('Y-m-d'));
          $to = new DateTime(date("Y-m-d"));
          if ($from < $to) {
            $gone = '<span class="text-danger">delayed</span>';
          } else if ($from > $to) {
            $gone = '<span class="text-success">ahead</span>';
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
          if (Auth::user()->can('upcoming_inventory_move_otw')) {
            $btn .= '<a data-bs-toggle="modal" class="dropdown-item moveotw" data-product-id="' . $row->product_id . '" data-row-id="' . $row->id . '"  data-upcoming-quantity="' . $row->qty . '" data-bs-target="#move_to_otw_modal" data-bs-placement="top" title="Move this to OTW">
                                    <i data-feather="truck"></i> Move to OTW
                                </a>';
          }
          if (Auth::user()->can('upcoming_inventory_update')) {
            $btn .= '<a class="dropdown-item" href="/upcoming_inventory/' . $row->id . '/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Upcoming Inventory">
                                                            <i data-feather="edit-2"></i> Edit
                                                        </a>';
          }
          if (Auth::user()->can('upcoming_inventory_delete')) {
            $btn .= '<a class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Upcoming Inventory" href="/delete/upcoming/inventory/' . $row->id . '" onclick="return myFunction()">
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
    return view('admin.upcoming_inventory.inventory', compact('categories'));
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
    return view('admin.upcoming_inventory.add_inventory')->with($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if ($request->ajax()) {
      UpcomingInventory::create([
        'product_id' => $request->product_id,
        'qty' => $request->qty,
        'date' => Carbon::parse($request->purchase_date)->format('Y-m-d'),
        'description' => $request->description
      ]);
      return response()->json(['data' => 'success']);
    } else {
      $loginUserData = Auth::user();
      $validatedData = $request->validate([
        'product_id' => 'required',
        'qty' => 'required',
        'purchase_date' => 'required',
      ]);
      UpcomingInventory::create([
        'product_id' => $request->product_id,
        'qty' => $request->qty,
        'date' => Carbon::parse($request->purchase_date)->format('Y-m-d'),
        'description' => $request->description
      ]);
      return redirect('/upcoming_inventory');
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $upcomingInventoryProduct = UpcomingInventory::with('Products:id,name')->where('product_id', $id)->where('qty', '>', 0)->get();
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
          return Carbon::parse($row->shipping_date)->format('Y-m-d');
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
          $date2 = new DateTime($row->date);
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
          if (Auth::user()->can('upcoming_inventory_move_otw')) {
            $btn .= '<p><a data-bs-toggle="modal" class="moveotw" data-product-id="' . $row->product_id . '" data-row-id="' . $row->id . '"  data-upcoming-quantity="' . $row->qty . '" data-bs-target="#move_to_otw_modal" data-bs-placement="top" title="Move this to OTW">
                                  <i data-feather="truck"></i>
                              </a>';
          }
          if (Auth::user()->can('upcoming_inventory_update')) {
            $btn .= '<a href="/upcoming_inventory/' . $row->id . '/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Upcoming Inventory">
                                                          <i data-feather="edit-2"></i>
                                                      </a>';
          }
          if (Auth::user()->can('upcoming_inventory_delete')) {
            $btn .= '<a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Upcoming Inventory" href="/delete/upcoming/inventory/' . $row->id . '" onclick="return myFunction()">
                                  <i data-feather="trash"></i>
                              </a>';
          }

          $btn .= '</p>';
          return $btn;
        })
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
    $data['dataSet'] = UpcomingInventory::find($id);
    $date = Carbon::parse($data['dataSet']->date)->format('m/d/Y');
    $data['products'] = Products::All();
    return view('admin.upcoming_inventory.edit_inventory', compact('date'))->with($data);
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
      'product_id' => 'required',
      'qty' => 'required',
      'purchase_date' => 'required',
    ]);

    $product_id = $request->input('product_id');
    $qty = $request->input('qty');
    $date = $request->input('purchase_date');

    $storeData = UpcomingInventory::find($id);
    //On left field name in DB and on right field name in Form/view
    $storeData->product_id = $request->input('product_id');
    $storeData->qty = $request->input('qty');
    $storeData->date = Carbon::parse($request->input('purchase_date'))->format('Y-m-d');
    $storeData->description = $request->description;
    $storeData->save();
    return redirect('/upcoming_inventory');
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
    $data = UpcomingInventory::find($id);
    $data->delete();

    return redirect('/upcoming_inventory');
  }
}
