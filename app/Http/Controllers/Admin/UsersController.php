<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Redirect;
use DataTables;
use App\Models\User;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Customers;
use App\AdminModels\UserRoles;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
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
      $data = User::get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($row) {
          return ucwords($row->name);
        })
        ->addColumn('email', function ($row) {
          return ($row->email);
        })
        ->addColumn('role_id', function ($row) {
          if ($row->customer_status == 0) {
            if (!empty($row->roles()->pluck('name')[0])) {
              return '<span class="badge rounded-pill bg-success me-1">' . ucwords($row->roles()->pluck('name')[0]) . '</span>';
            } else {
              return '<span class="badge rounded-pill bg-danger me-1">No Role Assigned</span>';
            }
          } else {
            return '<span class="badge rounded-pill bg-success me-1">Customer</span>';
          }
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i data-feather="more-vertical"></i>
                      </button>
                      <div class="dropdown-menu">';
          if (Auth::user()->can('user_view')) {
            $btn .= '<a class="dropdown-item" href="/user/' . $row->id . '/edit">
                          <i data-feather="edit-2"></i>
                          <span>Edit User</span>
                      </a>';
          }
          if (Auth::user()->can('user_delete')) {
            $btn .= '<a class="dropdown-item" href="/delete/user/' . $row->id . '">
                      <i data-feather="trash"></i>
                      <span>Delete User</span>
                  </a>';
          }
          $btn .= '</div>
                </div>';
          return $btn;
        })
        ->rawColumns(['action', 'role_id'])
        ->make(true);
    }
    return view('admin.users.users');
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $data['roles'] = Role::where('name', '!=', 'customer')->get();
    return view('admin.users.add_user')->with($data);
  }
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'role' => ['required'],
    ]);
    $name = $request->input('name');
    $email = $request->input('email');
    $password = $request->input('password');
    $role_id = $request->input('role');
    $user = new User();
    //On left field name in DB and on right field name in Form/view
    $user->name = $name;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->assignRole($request->role);
    $user->customer_status = 0;
    $user->save();
    return redirect('/user');
  }
  public function edit($id)
  {
    $data['dataSet'] = User::find($id);
    $data['roles'] = Role::where('name', '!=', 'customer')->get();
    $roleId = '';
    if (!empty($data['dataSet']->roles()->pluck('id')[0])) {
      $roleId = $data['dataSet']->roles()->pluck('id')[0];
    }
    return view('admin.users.edit_user', compact('roleId'))->with($data);
  }
  public function editProfile($id)
  {
    $data['dataSet'] = User::find($id);
    $data['roles'] = Roles::All();
    return view('admin.users.edit_profile')->with($data);
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
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255'],
      'role' => ['required'],
    ]);
    $user = User::find($id);
    $custUser = CustomerUser::where('user_id', $id)->first();
    if ($request->password == '') {
      $user->name = $request->name;
      $user->email = $request->email;
      $user->roles()->detach();
      $user->assignRole($request->role);
      $user->save();
      if (isset($custUser)) {
        $customer = Customers::where('id', $custUser->customer_id)->first();
        if (isset($customer)) {
          $customer->customer_name = $request->name;
          $customer->email = $request->email;
          $customer->save();
        }
      }
    } else {
      $user->name = $request->name;
      $user->email = $request->email;
      $user->assignRole($request->role);
      $user->password = Hash::make($request->password);
      $user->save();
      if (isset($custUser)) {
        $customer = Customers::where('id', $custUser->customer_id)->first();
        if (isset($customer)) {
          $customer->customer_name = $request->name;
          $customer->email = $request->email;
          $customer->password = Hash::make($request->password);
          $customer->save();
        }
      }
    }
    return redirect('/user');
  }
  public function editProfileProcess(Request $request)
  {
    $validatedData = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255'],
    ]);
    $id = $request->input('id');
    $name = $request->input('name');
    $email = $request->input('email');
    $password = $request->input('password');
    if ($password == '') {
      $user = User::find($id);
      $user->name = $name;
      $user->email = $email;
      $user->save();
    } else {
      $user = User::find($id);
      $user->name = $name;
      $user->email = $email;
      $user->password = Hash::make($password);
      $user->save();
    }
    return redirect('/user');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $data = User::find($id);
    $custUser = CustomerUser::where('user_id', $id);
    if ($custUser->exists()) {
      $customer = Customers::where('id', $custUser->first()->customer_id);
      if ($customer->exists()) {
        $customer->delete();
      }
    }
    $data->delete();
    return redirect('/user');
  }
}
