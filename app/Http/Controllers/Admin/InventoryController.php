<?php

namespace App\Http\Controllers\Admin;

use App\AdminModels\Category;
use App\AdminModels\Customers;
use Session;
use DateTime;
use Redirect;
use DataTables;
use Carbon\Carbon;
use App\Traits\Forecast;
use App\AdminModels\Products;
use App\AdminModels\Inventory;
use App\AdminModels\OrderDetails;
use App\AdminModels\OtwInventory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\InventoryHistory;
use App\AdminModels\UpcomingInventory;
use App\Interfaces\InventoryRepositoryInterface;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
  //
  private InventoryRepositoryInterface $inventoryRepository;
  use Forecast;

  public function __construct(InventoryRepositoryInterface $inventoryRepository)
  {
    $this->middleware('auth');
    $this->inventoryRepository = $inventoryRepository;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $data = $this->inventoryRepository->inventoryDetails($request);
      return $data['data'];
    }
    $categories = Category::get();
    return view('admin.inventory.inventory', compact('categories'));
  }
  public function inventoryCostTotal(Request $request)
  {
    $totalInventory = $this->inventoryRepository->inventoryDetails(($request));
    $totalCost = $totalInventory['total_cost'];
    $totalQty = $totalInventory['total_qty'];
    return response()->json(['status' => true, 'total_qty' => $totalQty, 'total_cost' => $totalCost]);
  }
  public function trashInv(Request $request)
  {
    if ($request->ajax()) {
      $data = Inventory::onlyTrashed()->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          if (isset($row)) {
            $product = Products::where('id', $row->product_id)->first();
            $name = $product->name;
          }
          return ucwords($name);
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("Y-m-d", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          if (Auth::user()->can('restore', Inventory::class)) {
            $btn .= '<p><a class="btn btn-primary waves-effect waves-float waves-light" href="/restoreTrash/' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Restore">
                        Restore
                    </a>';
          }
          $btn .= '</p>';
          return $btn;
        })
        ->rawColumns(['action'])
        // ->filter(function ($instance) use ($request)
        // {
        //   if (!empty($request->get('search')['value']))
        //   {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("inventory.qty like '%$keyword%' OR products.name like '%$keyword%'");
        //   }
        // })
        ->make(true);
    }
    return view('admin.inventory.trashInventory');
  }
  public function restoreTrash($id)
  {
    Inventory::withTrashed()->find($id)->restore();
    ////////////////////deactive the product///////////////
    $dataHistoryDeactive = Inventory::withTrashed()->where('id', $id)->first();
    $storeData = Products::find($dataHistoryDeactive->product_id);
    $storeData->is_active = 1;
    $storeData->save();
    //////////////////////////////////////////////////////
    ////////////manage history//////////////////
    $loginUserData = Auth::user();
    $dataHistory = Inventory::find($id);
    $storeHistoryData = new InventoryHistory();
    $storeHistoryData->product_id = $dataHistory->product_id;
    $storeHistoryData->user_id = $loginUserData->id;
    $storeHistoryData->qty = $dataHistory->qty;
    $storeHistoryData->date = Carbon::now();
    $storeHistoryData->deleted_at = Carbon::now();
    $storeHistoryData->status = 3;
    $storeHistoryData->save();
    ////////////////////////////////////////////
    return back();
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $data['products'] = Products::All();
    return view('admin.inventory.add_inventory')->with($data);
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $loginUserData = Auth::user();
    $validatedData = $request->validate([
      'product_id' => 'required',
      'qty' => 'required',
      'date' => 'required',
    ]);
    DB::beginTransaction();
    try {
      $product_id = $request->input('product_id');
      $qty = $request->input('qty');
      $date = $request->input('date');
      $getproductCount = DB::table('inventory')->where('product_id', $product_id)->first();
      if (isset($getproductCount->product_id)) {
        $newQty = $getproductCount->qty + $request->input('qty');
        Inventory::where('product_id', $getproductCount->product_id)->update(array('qty' => $newQty, 'date' => $request->input('date')));
        ////////////store history/////////////
        $loginUserData = Auth::user();
        $storeHistoryData = new InventoryHistory();
        $storeHistoryData->product_id = $request->input('product_id');
        $storeHistoryData->user_id = $loginUserData->id;
        $storeHistoryData->qty = $request->input('qty');
        $storeHistoryData->date = Carbon::now();
        $storeHistoryData->deleted_at = null;
        $storeHistoryData->status = 2;
        $storeHistoryData->save();
        ////////////////////////////////////////
        // return redirect('/inventory');
      } else {
        $storeData = new Inventory();
        //On left field name in DB and on right field name in Form/view
        $storeData->product_id = $request->input('product_id');
        $storeData->qty = $request->input('qty');
        $storeData->date = $request->input('date');
        $storeData->save();
        ////////////store history/////////////
        $loginUserData = Auth::user();
        $storeHistoryData = new InventoryHistory();
        $storeHistoryData->product_id = $request->input('product_id');
        $storeHistoryData->user_id = $loginUserData->id;
        $storeHistoryData->qty = $request->input('qty');
        $storeHistoryData->date = Carbon::now();
        $storeHistoryData->deleted_at = null;
        $storeHistoryData->status = 1;
        $storeHistoryData->save();
        ////////////////////////////////////////
        // return redirect('/inventory');
      }
      DB::commit();
      return redirect('/inventory')->withSuccess('Inventory added successfully');
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
    $data['dataSet'] = Inventory::find($id);
    $data['products'] = Products::All();
    return view('admin.inventory.edit_inventory')->with($data);
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
      'product_id' => 'required',
      'qty' => 'required',
      'date' => 'required',
    ]);
    $product_id = $request->input('product_id');
    $qty = $request->input('qty');
    $date = $request->input('date');

    $storeData = Inventory::find($id);
    //On left field name in DB and on right field name in Form/view
    $storeData->product_id = $request->input('product_id');
    $storeData->qty = $request->input('qty');
    $storeData->date = $request->input('date');
    $storeData->save();
    ////////////store history/////////////
    $loginUserData = Auth::user();
    $storeHistoryData = new InventoryHistory();
    $storeHistoryData->product_id = $request->input('product_id');
    $storeHistoryData->user_id = $loginUserData->id;
    $storeHistoryData->qty = $request->input('qty');
    $storeHistoryData->date = Carbon::now();
    $storeHistoryData->deleted_at = null;
    $storeHistoryData->status = 2;
    $storeHistoryData->save();
    ////////////////////////////////////////
    return redirect('/inventory');
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
    $data = Inventory::find($id);
    $data->delete();
    ////////////////////deactive the product///////////////
    $dataHistoryDeactive = Inventory::withTrashed()->where('id', $id)->first();
    $storeData = Products::find($dataHistoryDeactive->product_id);
    $storeData->is_active = 0;
    $storeData->save();
    //////////////////////////////////////////////////////
    /////////////////manage history/////////
    $dataHistory = Inventory::withTrashed()->where('id', $id)->first();
    $storeHistoryData = new InventoryHistory();
    $storeHistoryData->product_id = $dataHistory->product_id;
    $storeHistoryData->user_id = $loginUserData->id;
    $storeHistoryData->qty = $dataHistory->qty;
    $storeHistoryData->date = Carbon::now();
    $storeHistoryData->deleted_at = Carbon::now();
    $storeHistoryData->status = 0;
    $storeHistoryData->save();
    return redirect('/inventory');
  }
  public function inventoryHistory(Request $request, $id = null)
  {
    if ($request->ajax()) {
      $data  = DB::table("inventory_history")
                // ->where('product_id', 138)
                ->join('products', 'inventory_history.product_id', '=', 'products.id')
                ->select(['products.name', 'inventory_history.*'])
                ->orderBy('inventory_history.created_at', 'DESC')
                ->orderBy('inventory_history.id', 'DESC')
                ->where('inventory_history.product_id', $id);
      if (!empty($request->from)) {
        $inventoryAvailableDate = Carbon::parse('2022-07-25');
        if (Carbon::parse($request->from)->lt($inventoryAvailableDate)) {
          $request->from = Carbon::parse('2022-07-25');
        }
        $data = $data->whereDate('inventory_history.created_at', '>=', Carbon::parse($request->from)->format('Y-m-d'));
        if (!empty($request->to)) {
          $data = $data->whereDate('inventory_history.created_at', '<=', Carbon::parse($request->to)->format('Y-m-d'));
        }
      } else {
        $data = $data->whereDate('inventory_history.created_at', '>=', '2022-07-25');
      }
      $data = $data->get();
      // dd($data->toArray());
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('date', function ($row) {
          return '<span title="Created by: '.$row->user_name.'">'.(date("m/d/Y h:i:sa", strtotime($row->created_at))).'</span>';
        })
        ->addColumn('description', function ($row) {
          return ucwords($row->description);
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('manual_add', function ($row) {
          return ucwords($row->manual_add);
        })
        ->addColumn('batch_edited', function ($row) {
          return ucwords($row->edit_batch_qty);
        })
        ->addColumn('cancelled_orders', function ($row) {
          return ucwords($row->cancel_order_add);
        })
        ->addColumn('supplier_received', function ($row) {
          return ucwords($row->supplier_inventory_received);
        })
        ->addColumn('returned', function ($row) {
          return ucwords($row->return_add);
        })
        ->addColumn('return_edited', function ($row) {
          return ucwords($row->return_edited);
        })
        ->addColumn('manual_deduct', function ($row) {
          return ucwords($row->manual_reduce);
        })
        ->addColumn('sales', function ($row) {
          return ucwords($row->sales);
        })
        ->addColumn('total_inventory', function ($row) {
          return ucwords($row->total);
        })
        // ->addColumn('action', function ($row) {
        //   if (Auth::user()->can('inventory_delete')) {
        //     return $btn = "<a class='btn btn-primary btn-sm revert enter-pincode'  href='/inventory/history/" . $row->id . "/revert' data-prod-id='" . $row->id . "'  data-type='edit'  data-bs-toggle='modal' data-bs-target='#enter_pin_Modal' data-history-id='" . $row->id . "'>Revert</a>";
        //   } else {
        //     return '';
        //   }
        // })
        // ->filter(function ($instance) use ($request)
        // {
        //   $pid = $_GET["pid"];
        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("inventory_history.qty like '%$keyword%' OR products.name like '%$keyword%' OR users.name like '%$keyword%' AND inventory_history.product_id=$pid");
        //   }
        // })
        ->rawColumns(['date'])
        ->make(true);
    }
    return view('admin.inventory.inventory_history')->with('product_id', $id);
  }
  public function revertInventory($id)
  {
    $history = InventoryHistory::find($id);
    if ($history) {
      $inventory = Inventory::where('product_id', $history->product_id)->first();
      $inventory->qty = $inventory->qty - $history->qty;
      $inventory->save();
      $history->delete();
      return response()->json([
        'status' => 'success',
        'message' => 'Inventory are reverted'
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Error while reverting inventory'
      ], 200);
    }
  }
  public function forecastValues($row)
  {
    if ($row->automated_status == 1) {
      if ($row->days_left >= $row->threshold_val) {
        return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: green; color: white">' .$row->forecast_msg .'</span></center>';
      } else {
        if ($row->days_left == 'No Sales for '.$row->forecast_days) {
          return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: red; color: white">'.$row->forecast_msg.'</span></center>';
        } else if ($row->days_left < 0) {
          return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: red; color: white">'.$row->forecast_msg.'</span></center>';
        } else {
          return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: red; color: white">' .$row->forecast_msg . '' . '</span></center>';
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
        return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: green; color: white">' .$row->forecast_msg . '' . '</span></center>';
      } else {
        if ($row->days_left < 0) {
          return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: red; color: white">0d</span></center>';
        } else {
          return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->forecast_msg.'" style="background-color: red; color: white">' .$row->forecast_msg . '' . '</span></center>';
        }
      }
    }
  }
  public function nearToEmptyData(Request $request)
  {
    if ($request->ajax()) {
      $data = $this->nearToEmpty($request->category, $request->product);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('category', function ($row) {
          return ucwords($row->category->name);
        })
        ->addColumn('inventory_available', function ($row) {
          return ucwords($row->invent_qty);
        }) // adding upcoming and otw
        ->addColumn('upcoming', function ($row) {
          $UpcomingInventory = UpcomingInventory::where('product_id', $row->id)->groupBy('product_id')->sum('qty');
          return $UpcomingInventory;
        })
        ->addColumn('otw', function ($row) {
          $OtwInventory = OtwInventory::where('product_id', $row->id)->groupBy('product_id')->sum('qty');
          return $OtwInventory;
        })
        ->addColumn('forecast_statuses', function ($row) {
          if ($row->forecast_status == 1 && $row->manual_threshold > 0) {
            return '<center><span class="badge rounded-pill me-1" style="background-color: brown; color: white">Manual Forecast Count</span></center>';
          } else if ($row->automated_status == 1) {
            return '<center><span class="badge rounded-pill me-1" style="background-color: blue; color: white">Default</span></center>';
          } else if ($row->automated_status == 0) {
            return '<center><span class="badge rounded-pill me-1" style="background-color: orange; color: white">Forecast Days</span></center>';
          }
        })
        ->addColumn('forecast_val', function ($row) {
          return $this->forecastValues($row);
          // if ($row->automated_status == 1) {
          //   if ($row->days_left > $row->threshold_val) {
          //     return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: green; color: white">' .$row->days_left . 'd' . '</span></center>';
          //   } else {
          //     if ($row->days_left < 0) {
          //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">0d</span></center>';
          //     }
          //     return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">' .$row->days_left . 'd' . '</span></center>';
          //   }
          // } else if ($row->forecast_status > 0) {
          //   if ($row->manual_threshold > 0) {
          //     if ($row->invent_qty < $row->manual_threshold) {
          //       return '<center><span class="badge rounded-pill me-1" style="background-color: red; color: white">Order Now</span></center>';
          //     } else {
          //       return '<center><span class="badge rounded-pill me-1" style="background-color: green; color: white">Enough</span></center>';
          //     }
          //   }
          // } else if ($row->threshold_val > 0 || $row->forecast_days > 0) {
          //   if ($row->days_left > $row->threshold_val) {
          //     return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: green; color: white">' .$row->days_left . 'd' . '</span></center>';
          //   } else {
          //     if ($row->days_left < 0) {
          //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">0d</span></center>';
          //     } else {
          //       return '<center><span class="badge rounded-pill me-1" data-sorting="'.$row->days_left.'" style="background-color: red; color: white">' .$row->days_left . 'd' . '</span></center>';
          //     }
          //   }
          // }
        })
        ->rawColumns(['action', 'forecast_statuses', 'forecast_val'])
        ->make(true);
    }
    $categories = Category::get();
    return view('admin.inventory.near_to_empty', compact('categories'));
  }
  public function categoryProducts(Request $request, $id)
  {
    $products = Products::where('category_id', $id)->get()->toArray();
    return response()->json(['status' => 'success', 'data' => $products]);
  }
  public function setInventoryHistory()
  {
    // InventoryHistory::where('id', '1636')->update([
      // 'qty' => '-1250'
    // ]);
    // return 'Done';
  }
}
