<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:managePackages']);
    }
    
    
    public function approved()
{ 
    dd('here');
    // Load all invoices with related user and package
    $invoices = \App\Models\Invoice::with(['user', 'package'])->get();

    // Get all users who have at least one invoice
    $users = \App\Models\User::whereHas('invoices')->get();

    return view('admin.packages.payment', compact('invoices', 'users'));
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
        $packages = \App\Models\Package::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // ⚡ If AJAX request → return ONLY the table body rows
        if ($request->ajax()) {
            return view('admin.packages.partials.table', compact('packages'))->render();
        }

        // Normal full page load
        return view('admin.packages.list', compact('packages'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // dd($request->all());
    $request->validate([
        'name'          => 'required|string|max:100',
        'price'         => 'required|numeric',
        'lyd_price'         => 'required|numeric',
        'duration_days' => 'required|integer|min:1',
        'signal_limit'  => 'nullable',
        'status'        => 'required|in:active,inactive',
        'description'   => 'nullable|string',
    ]);

    // Hardcoded LYD conversion rate
    // $conversionRate = 5.44;

    // Calculate lyd_price
    // $lydPrice = $request->price * $conversionRate;

    // Create the package
    Package::create([
        'name'          => $request->name,
        'price'         => $request->price,
        'lyd_price'         => $request->lyd_price,
        'duration_days' => $request->duration_days,
        'signal_limit'  => $request->signal_limit,
        'status'        => $request->status,
        'description'   => $request->description,
    ]);

    return redirect()->route('packages.index')
        ->with('success', 'Package created successfully!');
}


    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Find the package by ID or fail with 404
        $package = Package::findOrFail($id);

        // Return the edit form view with package data
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
 public function update(Request $request, $id)
{
    $package = Package::findOrFail($id);

    $request->validate([
        'name'          => 'required|string|max:100',
        'price'         => 'required|numeric',
        'lyd_price'         => 'required|numeric',
        'duration_days' => 'required|integer|min:1',
        'signal_limit'  => 'nullable',
        'status'        => 'required|in:active,inactive',
        'description'   => 'nullable|string',
    ]);

    // Hardcoded LYD conversion rate
    // $conversionRate = 5.44;

    // Calculate lyd_price
    // $lydPrice = $request->price * $conversionRate;

    // Update the package
    $package->update([
        'name'          => $request->name,
        'price'         => $request->price,
        'lyd_price'     => $request->lyd_price,
        'duration_days' => $request->duration_days,
        'signal_limit'  => $request->signal_limit,
        'status'        => $request->status,
        'description'   => $request->description,
    ]);

    return redirect()->route('packages.index')
        ->with('success', 'Package updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Package deleted successfully!');
    }
}
