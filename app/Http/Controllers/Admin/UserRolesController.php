<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\AdminModels\UserRoles;
use App\AdminModels\Permissions;
use App\AdminModels\PermissionRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use DB;
use Session;
use DataTables;
use Redirect;

class UserRolesController extends Controller
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
      $data = UserRoles::select(['user_roles.*'])->orderByRaw($orderbyColAndDirection);
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill badge-light-danger me-1">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill badge-light-success me-1">Active</span>';
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          if (Auth::user()->can('update', UserRoles::class)) {
            $btn .= '<p><a href="/userroles/' . $row->id . '/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit User Role">
                          <i data-feather="edit-2"></i>
                          </a>';
          }
          if (Auth::user()->can('update', UserRoles::class)) {
            $btn .= '<a data-bs-toggle="tooltip" data-bs-placement="top" title="Permissions" href="/role_permissions/' . $row->id . '">
                          <i data-feather="key"></i>
                        </a>';
          }
          if (Auth::user()->can('delete', UserRoles::class)) {
            $btn .= '<a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete User Role" href="/delete/userrole/' . $row->id . '" onclick="return myFunction()">
                          <i data-feather="trash"></i>
                        </a>';
          }
          $btn .= '</p>';
          return $btn;
        })
        ->rawColumns(['action', 'is_active'])
        //   ->filter(function ($instance) use ($request)
        //   {
        //     if (!empty($request->get('search')['value']))
        //     {
        //       $keyword = $request->get('search')['value'];
        //       $instance->whereRaw("user_roles.name like '%$keyword%'");
        //     }
        // })
        ->make(true);
    }
    return view('admin.user_roles.user_roles');
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('admin.user_roles.add_userrole');
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
    $name = $request->input('name');
    $is_active = $request->input('is_active');
    if ($is_active == 1) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    $storeData = new UserRoles();
    //On left field name in DB and on right field name in Form/view
    $storeData->name = $request->input('name');
    $storeData->is_active = $is_active;
    $storeData->save();
    return redirect('/userroles');
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
    $data['dataSet'] = UserRoles::find($id);
    return view('admin.user_roles.edit_userrole')->with($data);
  }
  public function permissions($id)
  {
    $data['role_id'] = $id;
    $data['permissions'] = Permissions::All();
    $assigned = PermissionRole::select('*')->where('role_id', $id)->get()->toArray();
    $newarray = array();
    foreach ($assigned as $key => $value) {
      array_push($newarray, $value['permission_id']);
    }
    $data['assignedPermisions'] = $newarray;
    return view('admin.user_roles.permissions')->with($data);
  }
  public function addPermissions(Request $request)
  {
    $permissions = $request->input('permissions');
    $role_id = $request->input('role_id');
    DB::table('permission_role')->where('role_id', $role_id)->delete();
    foreach ($permissions as $permission) {
      $storeData = new PermissionRole();
      $storeData->role_id = $role_id;
      $storeData->permission_id = $permission;
      $storeData->save();
    }
    return redirect('/userroles');
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
      'name' => 'required',
    ]);
    $name = $request->input('name');
    $is_active = $request->input('is_active');
    if ($is_active == 1) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    $storeData = UserRoles::find($id);
    //On left field name in DB and on right field name in Form/view
    $storeData->name = $request->input('name');
    $storeData->is_active = $is_active;
    $storeData->save();
    return redirect('/userroles');
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $data = UserRoles::find($id);
    $data->delete();
    return redirect('/userroles');
  }
}
