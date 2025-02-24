<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{RolePermission, Role, Permission};

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.roles-permissions.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::find($request->role_id);
        $role->permissions()->sync($request->permissions);

        return back()->with('success', 'Permissions updated successfully');
    }

    public function destroy(RolePermission $rolePermission)
    {
        $rolePermission->delete();

        return back()->with('success', 'Permission removed successfully');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('admin.roles-permissions.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions);

        return redirect()->route('roles-permissions.index')->with('success', 'Permissions updated successfully');
    }

}
