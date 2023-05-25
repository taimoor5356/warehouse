<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\AdminModels\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use DB;
use Session;
use DataTables;
use Redirect;

class UnitsController extends Controller
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
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];

      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);

      $data = Units::select(['product_units.*'])->orderByRaw($orderbyColAndDirection);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu">';
          if (Auth::user()->hasRole('admin')) {
            $btn .= '<a class="dropdown-item" href="/units/' . $row->id . '/edit">
                              <i data-feather="edit-2"></i>
                              <span>Edit Unit</span>
                          </a>';
            $btn .= '<a class="dropdown-item" href="/delete/unit/' . $row->id . '" onclick="confirmDelete(event)">
                                <i data-feather="trash"></i>
                                <span>Delete Unit</span>
                            </a>';
          }

          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'is_active'])
        // ->filter(function ($instance) use ($request) {

        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];

        //     $instance->whereRaw("product_units.name like '%$keyword%'");

        //   }
        ->make(true);
    }
    //
    return view('admin.units.units');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
    return view('admin.units.add_unit');
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
    $unit_type = 0;
    if ($request->input('unit_type') == 1) {
      $unit_type = 1;
    }
    $storeUnitData = new Units();
    $storeUnitData->name = $request->input('name');
    $storeUnitData->unit_type = $unit_type;
    $storeUnitData->save();
    return redirect('/units')->withSuccess('Unit has been added');
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
    $data['dataSet'] = Units::find($id);
    return view('admin.units.edit_unit')->with($data);
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
    $unit_type = 0;
    $storeData = Units::find($id);
    //On left field name in DB and on right field name in Form/view
    $storeData->name = $request->input('name');
    if ($request->input('unit_type') == 1) {
      $unit_type = 1;
    }
    $storeData->unit_type = $unit_type;
    $storeData->save();

    return redirect('/units')->withSuccess('Unit has been updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {

    $data = Units::find($id);
    $data->delete();
    return redirect('/units')->withSuccess('Unit has been deleted');
  }
}
