<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\PackagePurchase;
use App\Models\ValueSubscription;
use Illuminate\Support\Str;
use PDF;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manageUsers']);
    }
    /**
     * Display a listing of the resource.
     */
     
     
//      public function newUserStore(Request $request)
// {
//     $request->validate([
//         'user_id'    => 'required|exists:users,id',
//         'package_id' => 'required|exists:packages,id',
//         'currency'   => 'required|in:usd,lyd',
//         'amount'     => 'required|numeric|min:0',
//         'start_date' => 'required|date',
//         'end_date'   => 'required|date|after_or_equal:start_date',
//         'screenshot' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     $user = User::findOrFail($request->user_id);

//     try {
//         DB::beginTransaction();

//         // ✅ Upload Screenshot (if provided)
//         $screenshotPath = null;
//         if ($request->hasFile('screenshot')) {
//             $file = $request->file('screenshot');
//             $filename = 'screenshot_' . time() . '.' . $file->getClientOriginalExtension();
//             $path = 'uploads/invoices/screenshots';
//             $file->move(public_path($path), $filename);
//             $screenshotPath = $path . '/' . $filename;
//         }

//         // ✅ Create Invoice
//         $invoice = Invoice::create([
//             'user_id'    => $request->user_id,
//             'package_id' => $request->package_id,
//             'amount'     => $request->amount,
//             'currency'   => $request->currency,
//             'start_date' => $request->start_date,
//             'end_date'   => $request->end_date,
//             'name'       => trim($user->f_name . ' ' . $user->last_name),
//             'phone'      => $user->phone ?? '',
//             'duration'   => \Carbon\Carbon::parse($request->start_date)
//                                 ->diffInDays(\Carbon\Carbon::parse($request->end_date)) . ' days',
//         ]);

//         // ✅ Create Package Purchase (Manual)
//         $purchase = PackagePurchase::create([
//             'user_id'    => $request->user_id,
//             'package_id' => $request->package_id,
//             'invoice_id' => $invoice->id,
//             'status'     => 'approved',
//             'screenshot' => $screenshotPath,
//         ]);

//         // ✅ Assign Package to ValueSubscription
//         ValueSubscription::create([
//             'user_id'              => $request->user_id,
//             'package_id'           => $request->package_id,
//             'invoice_id'           => $invoice->id,
//             'package_purchases_id' => $purchase->id,
//         ]);

//         // ✅ Generate PDF
//         $pdf = \PDF::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

//         // Ensure folder exists
//         $invoicesDir = public_path('invoices');
//         if (!is_dir($invoicesDir)) {
//             @mkdir($invoicesDir, 0775, true);
//         }

//         $filePath = 'invoices/invoice_' . $invoice->id . '.pdf';
//         $pdf->save(public_path($filePath));

//         // Update DB with pdf path
//         $invoice->update(['pdf_path' => $filePath]);

//         DB::commit();

//         return redirect()->route('invoices.index')
//             ->with('success', 'Invoice generated and saved successfully.');

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return back()->with('error', 'Something went wrong: ' . $e->getMessage());
//     }
// }

    
public function newUserStore(Request $request)
{
    $request->validate([
        'user_id'    => 'required|exists:users,id',
        'package_id' => 'required|exists:packages,id',
        'currency'   => 'required|in:usd,lyd',
        'amount'     => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'screenshot' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = User::findOrFail($request->user_id);

    // ✅ Check if user already has an active subscription for this package
    $activeSubscription = ValueSubscription::where('user_id', $request->user_id)
        ->where('package_id', $request->package_id)
        ->whereHas('invoice', function ($q) {
            $q->whereDate('end_date', '>=', now());
        })
        ->first();

    if ($activeSubscription) {
        return back()->with('error', 'This user already has an active subscription for this package until ' .
            \Carbon\Carbon::parse($activeSubscription->invoice->end_date)->format('d M Y'));
    }

    try {
        DB::beginTransaction();

        // ✅ Upload Screenshot (if provided)
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $filename = 'screenshot_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/invoices/screenshots';
            $file->move(public_path($path), $filename);
            $screenshotPath = $path . '/' . $filename;
        }

        // ✅ Create Invoice
        $invoice = Invoice::create([
            'user_id'    => $request->user_id,
            'package_id' => $request->package_id,
            'amount'     => $request->amount,
            'currency'   => $request->currency,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'name'       => trim($user->f_name . ' ' . $user->last_name),
            'phone'      => $user->phone ?? '',
            'duration'   => \Carbon\Carbon::parse($request->start_date)
                                ->diffInDays(\Carbon\Carbon::parse($request->end_date)) . ' days',
        ]);

        // ✅ Create Package Purchase (Manual)
        $purchase = PackagePurchase::create([
            'user_id'    => $request->user_id,
            'package_id' => $request->package_id,
            'invoice_id' => $invoice->id,
            'status'     => 'approved',
            'screenshot' => $screenshotPath,
        ]);

        // ✅ Assign Package to ValueSubscription
        ValueSubscription::create([
            'user_id'              => $request->user_id,
            'package_id'           => $request->package_id,
            'invoice_id'           => $invoice->id,
            'package_purchases_id' => $purchase->id,
        ]);

        // ✅ Generate PDF
        $pdf = \PDF::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

        // Ensure folder exists
        $invoicesDir = public_path('invoices');
        if (!is_dir($invoicesDir)) {
            @mkdir($invoicesDir, 0775, true);
        }

        $filePath = 'invoices/invoice_' . $invoice->id . '.pdf';
        $pdf->save(public_path($filePath));

        // Update DB with pdf path
        $invoice->update(['pdf_path' => $filePath]);

        DB::commit();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice generated and saved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

     
     
     public function newUser()
     {
         
                 $users = User::where(function ($query) {
            $query->whereNull('staff_type')
                  ->orWhere('staff_type', '');
        })
        ->where('admin_type', 0)
        ->orderBy('f_name')
        ->get();

    $packages = Package::orderBy('name')->get();

        return view('admin.invoices.newUser', compact('users', 'packages'));
     }
     
     
     
   

     
     public function index(Request $request)
{
    $query      = $request->get('query', '');
    $status     = $request->get('status', '');
    $packageId  = $request->get('package', '');
    $perPage    = $request->get('perPage', 10);

    $users = \App\Models\User::where(function ($q) {
            $q->where('staff_type', '!=', 'staff')
              ->orWhereNull('staff_type');
        })
        ->when($query, function ($q) use ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('f_name', 'like', "%$query%")
                    ->orWhere('last_name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%")
                    ->orWhere('status', 'like', "%$query%")
                    ->orWhere('country', 'like', "%$query%");
            });
        })
        ->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->when($packageId, function ($q) use ($packageId) {
            $q->whereHas('packages', function ($sub) use ($packageId) {
                $sub->where('package_id', $packageId)
                    ->where('package_purchases.status', 'approved');
            });
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends([
            'query'   => $query,
            'status'  => $status,
            'package' => $packageId,
            'perPage' => $perPage,
        ]);

    $packages = \App\Models\Package::where('status', 1)->get();

    if ($request->ajax()) {
        $view = view('admin.userlist', compact('users', 'packages'));
        $sections = $view->renderSections();
        return $sections['tableBody'] ?? '';
    }

    return view('admin.userlist', compact('users', 'packages', 'packageId', 'status'));
}
    // public function index(Request $request)
    // {
    //     $query   = $request->get('query', '');
    //     $status  = $request->get('status', ''); // 👈 new filter
    //     $perPage = $request->get('perPage', 10);

    //     $users = User::where(function ($q) {
    //         $q->where('staff_type', '!=', 'staff')
    //             ->orWhereNull('staff_type');
    //     })
    //         ->when($query, function ($q) use ($query) {
    //             $q->where(function ($sub) use ($query) {
    //                 $sub->where('f_name', 'like', "%$query%")
    //                     ->orWhere('last_name', 'like', "%$query%")
    //                     ->orWhere('email', 'like', "%$query%")
    //                     ->orWhere('phone', 'like', "%$query%")
    //                     ->orWhere('status', 'like', "%$query%")
    //                     ->orWhere('country', 'like', "%$query%");
    //             });
    //         })
    //         ->when($status, function ($q) use ($status) {
    //             $q->where('status', $status); // 👈 filter by status (e.g., active/inactive)
    //         })
    //         ->orderBy('id', 'desc')
    //         ->paginate($perPage)
    //         ->appends([
    //             'query'   => $query,
    //             'status'  => $status, // 👈 keep status in pagination links
    //             'perPage' => $perPage
    //         ]);

    //     if ($request->ajax()) {
    //         return view('admin.userlist', compact('users'))
    //             ->renderSections()['tableBody'] ?? '';
    //     }

    //     return view('admin.userlist', compact('users', 'status'));
    // }

    public function staff(Request $request)
    {
        $query   = $request->get('query', '');
        $perPage = $request->get('perPage', 10);

        $users = User::where('staff_type', 'staff') // ✅ Only staff
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('f_name', 'like', "%$query%")
                        ->orWhere('last_name', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%")
                        ->orWhere('phone', 'like', "%$query%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage]);

        if ($request->ajax()) {
            return view('admin.users.staff', compact('users'))
                ->renderSections()['tableBody'] ?? '';
        }

        return view('admin.users.staff', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    public function profile()
    {
        return view('admin.users.profile');
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'f_name'    => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'phone'     => 'nullable|string|max:20',
        ]);

    

        User::create([
            'f_name'      => $validated['f_name'],
            'last_name'   => $validated['last_name'],
            'email'       => $validated['email'],
            'password'    => bcrypt($validated['password']),
            'phone'       => $validated['phone'] ?? null,
            'staff_type'  => 'staff',
            'admin_type'  => 1,
        ]);


        return redirect()->route('users.staff')->with('success', 'Staff created successfully!');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $user->status = $request->status;   // store directly as string
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }


    // public function editRoles(User $user)
    // {
    //     return response()->json([
    //         'user' => $user,
    //         'roles' => Role::all(),
    //         'user_roles' => $user->roles->pluck('id')->toArray(),
    //     ]);
    // }

    public function editRoles(User $user)
    {
        $currentUser = auth()->user();
        $availableRoles = collect();

        // Role-based filtering
        if ($currentUser->hasRole('admin')) {
            // Admin can see all roles
            $availableRoles = Role::all();
        } elseif ($currentUser->hasRole('manager')) {
            // Manager can only see staff role
            $availableRoles = Role::where('name', 'staff')->get();
        }
        // Staff users cannot assign roles (no roles shown)

        return response()->json([
            'user' => $user,
            'roles' => $availableRoles,
            'user_roles' => $user->roles->pluck('id')->toArray(),
        ]);
    }



    public function updateRoles(Request $request, User $user)
    {
        // $request->roles is an array of role IDs from checkboxes
        $roleIds = $request->input('roles', []);

        // 🔹 Convert IDs to role names
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        // 🔹 Assign roles by NAME
        $user->syncRoles($roleNames);

        return response()->json(['success' => true, 'message' => 'Roles Assaign to user successfully.']);
    }
}
