<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RoleController extends Controller
{

    function __construct()
    {
       /* $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);*/
    }


    public function index(Request $request)
    {
        $roles = Role::all();
        return view('roles.index',compact('roles'));
    }


    public function create()
    {
        // HARDCODED module names
        $moduleNames = ['administration', 'master-data', 'memberships', 'nominees', 'bulk', 'withdrawals', 'loans'];

        // Create an array to hold modules and their permissions
        $moduleWisePermissions = [
            'administration' => array(), 
            'master-data' => array(), 
            'memberships' => array(), 
            'nominees' => array(), 
            'bulk' => array(), 
            'withdrawals' => array(), 
            'loans' => array(),
        ];

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $available = false;
            foreach ($moduleNames as $moduleName) {
                if (Str::startsWith($permission->name, $moduleName)) {
                    $moduleWisePermissions[$moduleName][$permission->id] = $permission->name;
                    $available = true;
                }
            }
            if (!$available) {
                $moduleWisePermissions['other'][$permission->id] = $permission->name;
            }
        }        

        return view('roles.create',compact('moduleWisePermissions'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success','Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        return view('roles.show',compact('role','rolePermissions'));
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        // HARDCODED module names
        $moduleNames = ['administration', 'master-data', 'memberships', 'nominees', 'bulk', 'withdrawals', 'loans'];

        // Create an array to hold modules and their permissions
        $moduleWisePermissions = [
            'administration' => array(), 
            'master-data' => array(), 
            'memberships' => array(), 
            'nominees' => array(), 
            'bulk' => array(), 
            'withdrawals' => array(), 
            'loans' => array(),
        ];

        $permissions = Permission::all();
        
        foreach ($permissions as $permission) {
            $available = false;
            foreach ($moduleNames as $moduleName) {
                if (Str::startsWith($permission->name, $moduleName)) {
                    $moduleWisePermissions[$moduleName][$permission->id] = $permission->name;
                    $available = true;
                }
            }
            if (!$available) {
                $moduleWisePermissions['other'][$permission->id] = $permission->name;
            }
        }

        return view('roles.edit',compact('role', 'rolePermissions', 'moduleWisePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success','Role updated successfully');
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
            ->with('success','Role deleted successfully');
    }
}
