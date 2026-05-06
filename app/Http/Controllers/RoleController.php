<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Console\ViewClearCommand;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

use App\Models\Permission;
class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','permission:manageSetting']);
    }
    /**
     * Display a listing of the resource.
     */


   // For editing role and returning JSON data
public function assaignEdit($id)
{
    $role = \Spatie\Permission\Models\Role::findOrFail($id);
    $permissions = \Spatie\Permission\Models\Permission::all();

    // Get permission IDs assigned to the role
    $role_permissions = $role->permissions()->pluck('id')->toArray();

    return response()->json([
        'role' => $role,
        'permissions' => $permissions,
        'role_permissions' => $role_permissions,
    ]);
}

// For updating role with permissions
public function assaignUpdate(Request $request, $id)
{
    try {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Convert permission IDs to names
        $permissionIds = $request->permissions ?? [];
        $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        $role->syncPermissions($permissionNames);

        // Always return JSON for AJAX
        return response()->json(['status' => 'success', 'message' => 'Role updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}




    public function index(Request $request)
    {
        $query   = $request->input('query');
        $perPage = $request->input('perPage', 10);

        $roles = Role::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('guard_name', 'like', "%{$query}%");
        })
            ->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.roles.partials.table', compact('roles'))->render();
        }

        return view('admin.roles.list', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // ✅ Validate input
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        // ✅ Create new role
        Role::create([
            'name' => $request->name,
            'guard_name' => 'web', // ✅ Add this
        ]);

        // ✅ Redirect back with a success message
        return redirect()->route('roles.index')->with('success', 'Role added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return View('admin.roles.edit', compact('role'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd('here');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }


    public function getroles()
    {
        $roles = Role::paginate(10);
        return view('admin.roles.assign', compact('roles'));
    }
}
