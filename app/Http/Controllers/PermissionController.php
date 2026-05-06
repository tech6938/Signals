<?php

namespace App\Http\Controllers;

use App\Models\Permission;

use Illuminate\Http\Request;

class PermissionController extends Controller
{public function __construct()
    {
        $this->middleware(['auth','permission:manageSetting']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 🔎 Get search and pagination parameters
        $query   = $request->input('query');
        $perPage = $request->input('perPage', 10);

        // 🔎 Build query
        $permissions = Permission::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('guard_name', 'like', "%{$query}%");
        })
            ->paginate($perPage);

        // ⚡ If AJAX request → return ONLY the table body rows + pagination
        if ($request->ajax()) {
            return view('admin.permission.partials.table', compact('permissions'))->render();
        }

        // Normal full page load
        return view('admin.permission.list', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validate input
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // ✅ Create new role
        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web', // ✅ Add this
        ]);

        // ✅ Redirect back with a success message
        return redirect()->route('permissions.index')->with('success', 'Role added successfully!');
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
        $permission = Permission::findOrFail($id);
        return View('admin.permission.edit', compact('permission'));
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

        $role = Permission::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->route('permissions.index')->with('success', 'permissions updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'permissions deleted successfully!');
    }
}
