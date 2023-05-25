<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
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
            
          $data = Role::select(['roles.*'])->orderByRaw($orderbyColAndDirection);
        //   dd($data->get());
          return Datatables::of($data)
                  ->addIndexColumn()
                  ->addColumn('name', function($row){

                      return ucwords($row->name); 
                  })
                  ->addColumn('is_active', function($row){
                    
                      if ($row->is_active == 0 || $row->is_active == NULL) return '<span class="badge rounded-pill badge-light-danger me-1">Not Active</span>';
                      if ($row->is_active == 1) return '<span class="badge rounded-pill badge-light-success me-1">Active</span>';
                  })
                  ->addColumn('action', function($row){
                    $btn = '';
                    // if (Auth::user()->can('userrole_update')){
                    
                    $btn .= '<p><a href="/roles/'.$row->id.'/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit User Role">
                                                            <i data-feather="edit-2"></i>
                                                        </a>';
                    
                                                        $btn.='<a data-bs-toggle="tooltip" data-bs-placement="top" title="Permissions" target="_blank" href="/role_has_permissions/'.$row->id.'">
                                                            <i data-feather="key"></i>
                                                        </a>';
                                                    //   }
                    if (Auth::user()->can('userrole_delete', Role::class) ){
                    
                                                        $btn.='<a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete User Role" href="/delete/role/'.$row->id.'" onclick="return myFunction()">
                                                            <i data-feather="trash"></i>
                                                        </a>';
                                                      }
                                                    $btn .= '</p>';
                          return $btn;
                  })
                  ->rawColumns(['action','is_active'])
                //   ->filter(function ($instance) use ($request) {

                //     if (!empty($request->get('search')['value'])) {
                //       $keyword = $request->get('search')['value'];

                //       $instance->whereRaw("user_roles.name like '%$keyword%'");

                //     }

                // })
                  ->make(true);
        }
        //
        return view('admin.user_roles.user_roles');
    }

    public function roleHasPermissions(Request $request, $role_id) {
        $data['permissions'] = Permission::All();
        $data['assignedPermisions'] = Role::find($role_id)->permissions->pluck('id')->toArray();

        $data['headings'] = array(
            'Customers' => array( 'customers' => 'Manage Customers', 'labels' => 'Manage Labels', 'skus' => 'Manage SKUs'), 
            'Files' => array('files' => 'Manage Files'), 
            'Inventory' => array('inventory' => 'Inventory', 'products' => 'Products', 'categories' => 'Categories', 'units' => 'Units', 'upcoming' => 'Upcoming', 'otw' => 'OTW'), 
            'Counts' => array('counts' => 'Orders', 'returned_order' => 'Returned Orders'), 
            'Invoices' => array('invoices' => 'Manage Invoices'), 
            'Reports' => array('reports' => 'Manage Reports'), 
            'Users' => array('roles' => 'Roles', 'users' => 'Users'), 
            'Settings' => array('settings' => 'Manage Settings')
        );
        
        return view('admin.user_roles.permissions', compact('role_id'))->with($data);
    }

    public function assignPermissions(Request $request) {
        $role = Role::find($request->input('role_id'));
        if ($request->input('role_id') != 1) {
            $permissions = $request->input('permissions');
            $role->syncPermissions($permissions);
        } else {
            $allPermissions = Permission::get();
            $role->revokePermissionTo($allPermissions);
            $role->syncPermissions($allPermissions);
        }
        return redirect()->back()->withSuccess('Updated Successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
        $validatedData = $request->validate([
            'name' => 'required'
        ]);
        Role::create([
            'name' => $request->input('name'),
            'is_active' => $request->input('is_active')
        ]);
        return redirect('/roles');
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
        //
        $data['dataSet'] = Role::find($id);
        return view('admin.user_roles.edit_userrole')->with($data);
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
            'name' => 'required'
        ]);
        if ($id != 1) {
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->is_active = $request->input('is_active');
            $role->save();
        }
        return redirect('/roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dbSeed()
    {
        $arr = ['Order Update' => 'order_update', 'Order Delete' => 'order_delete', 'Unit View' => 'unit_view', 'Unit Create' => 'unit_create', 'Unit Update' => 'unit_update', 'Unit Delete' => 'unit_delete'];
        //
        foreach ($arr as $key => $permission) {
            Permission::create([
                'name' => $permission,
                'slug' => $key,
                'guard_name' => 'web'
            ]);
        }
    }
}
