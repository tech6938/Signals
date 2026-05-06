<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use App\Models\PackagePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use PDF;

class InvoiceController extends Controller
{
    
    
    
    
    
    
   public function approved()
{ 
    // dd('here');
    // Load all invoices with related user and package
    $invoices = \App\Models\Invoice::with(['user', 'package'])->get();

    // Get all users who have at least one invoice
    $users = \App\Models\User::whereHas('invoices')->get();

    return view('admin.packages.payment', compact('invoices', 'users'));
}

public function getUserPackages($userId)
{
    // Get all invoices for this user
    $invoices = \App\Models\Invoice::with('package')
        ->where('user_id', $userId)
        ->get();

    $packages = $invoices->map(function ($invoice) {
        $pkg = $invoice->package;

        // Check if package_purchase record exists for this invoice
        $packagePurchase = \App\Models\PackagePurchase::where('invoice_id', $invoice->id)->first();

        return [
            'id' => $pkg->id,
            'name' => $pkg->name,
            'invoice_id' => $invoice->id,
            'status' => $packagePurchase ? $packagePurchase->status : null,
            'selectable' => $packagePurchase ? false : true, // Only selectable if no package_purchase record
        ];
    });

    return response()->json(['packages' => $packages]);
}


// public function statusApproved(Request $request)
// {
//     $request->validate([
//         'user_id' => 'required|exists:users,id',
//         'package_id' => 'required|exists:packages,id',
//     ]);

//     $userId = $request->user_id;
//     $packageId = $request->package_id;

//     // 1️⃣ Get the invoice of this user for this package
//     $invoice = \App\Models\Invoice::where('user_id', $userId)
//         ->where('package_id', $packageId)
//         ->latest('id')
//         ->first();

//     if (!$invoice) {
//         return redirect()->back()->with('error', 'No invoice found for this user and package.');
//     }

//     // 2️⃣ Check if a PackagePurchase already exists for this invoice
//     $purchase = \App\Models\PackagePurchase::where('invoice_id', $invoice->id)
//         ->where('user_id', $userId)
//         ->where('package_id', $packageId)
//         ->first();

//     if ($purchase) {
//         // 3️⃣ Update existing record to approved
//         $purchase->update([
//             'status' => 'approved'
//         ]);
//     } else {
//         // 4️⃣ Create new PackagePurchase record
//         $purchase = \App\Models\PackagePurchase::create([
//             'user_id' => $userId,
//             'package_id' => $packageId,
//             'invoice_id' => $invoice->id,
//             'status' => 'approved'
//         ]);
//     }

// return redirect()->route('admin.package.purchases')->with('success', 'Package marked as approved successfully.');
    
// }

public function statusApproved(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'package_id' => 'required|exists:packages,id',
        'screenshot' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $userId = $request->user_id;
    $packageId = $request->package_id;

    // 1️⃣ Get invoice of this user for this package
    $invoice = \App\Models\Invoice::where('user_id', $userId)
        ->where('package_id', $packageId)
        ->latest('id')
        ->first();

    if (!$invoice) {
        return redirect()->back()->with('error', 'No invoice found for this user and package.');
    }

    // 2️⃣ Check if package purchase exists
    $purchase = \App\Models\PackagePurchase::where('invoice_id', $invoice->id)
        ->where('user_id', $userId)
        ->where('package_id', $packageId)
        ->first();

    // 3️⃣ Handle image upload if provided
    $path = null;
    if ($request->hasFile('screenshot')) {
        $path = $request->file('screenshot')->store('purchase_screenshots', 'public');
    }

    if ($purchase) {
        // Update existing record
        $purchase->update([
            'status' => 'approved',
            'screenshot' => $path ?? $purchase->screenshot, // keep old if not uploaded
        ]);
    } else {
        // Create new record
        $purchase = \App\Models\PackagePurchase::create([
            'user_id' => $userId,
            'package_id' => $packageId,
            'invoice_id' => $invoice->id,
            'status' => 'pending',
            'screenshot' => $path,
        ]);
    }

    return redirect()->route('admin.package.purchases')->with('success', 'Package marked as approved successfully.');

}

    // public function index(Request $request)
    // {
    //     $query = Invoice::query();

    //     if ($request->filled('query')) {
    //         $search = $request->get('query');
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //                 ->orWhere('phone', 'like', "%{$search}%")
    //                 ->orWhere('duration', 'like', "%{$search}%")
    //                 ->orWhere('service_type', 'like', "%{$search}%")
    //                 ->orWhere('start_date', 'like', "%{$search}%")
    //                 ->orWhere('end_date', 'like', "%{$search}%");
    //         });
    //     }

    //     $perPage = $request->get('perPage', 10);
    //     $invoices = $query->latest()->paginate($perPage);

    //     // AJAX request → return only table + pagination
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'html' => view('admin.invoices.partials._table', compact('invoices'))->render(),
    //             'pagination' => (string) $invoices->links()
    //         ]);
    //     }

    //     return view('admin.invoices.index', compact('invoices'));
    // }
    
    public function index(Request $request)
{
    $query   = $request->input('query');
    $perPage = $request->input('perPage', 10);

    $invoices = \App\Models\Invoice::when($query, function ($q) use ($query) {
        $q->where('name', 'like', "%{$query}%")
          ->orWhere('phone', 'like', "%{$query}%")
          ->orWhere('duration', 'like', "%{$query}%");
    })
    ->orderBy('id', 'desc')
    ->paginate($perPage);

    // AJAX → return only table rows
    if ($request->ajax()) {
        return view('admin.invoices.partials._table', compact('invoices'))->render();
    }

    return view('admin.invoices.index', compact('invoices'));
}


public function store(Request $request)
{
    $request->validate([
        'user_id'    => 'required|exists:users,id',
        'package_id' => 'required|integer|exists:packages,id',
        'amount'     => 'required|numeric',
        'currency'   => 'required|string|in:usd,lyd',
    ]);

    $user = User::findOrFail($request->user_id);

    // ✅ Check 1: Active Subscription (not expired)
    $activeInvoice = Invoice::where('user_id', $request->user_id)
        ->where('package_id', $request->package_id)
        ->whereNotNull('end_date')
        ->whereDate('end_date', '>=', now())
        ->first();

    if ($activeInvoice) {
        return back()->with('error', 'This user already has an active subscription for this package until ' .
            \Carbon\Carbon::parse($activeInvoice->end_date)->format('d M Y'));
    }

    // ✅ Check 2: Pending Package Purchase
    $pendingPurchase = PackagePurchase::where('user_id', $request->user_id)
        ->where('package_id', $request->package_id)
        ->where('status', 'pending')
        ->first();

    if ($pendingPurchase) {
        return back()->with('error', 'This user has a pending purchase for this package. Please approve or reject it first.');
    }

    // ✅ Check 3: Invoice with Empty End Date
    $incompleteInvoice = Invoice::where('user_id', $request->user_id)
        ->where('package_id', $request->package_id)
        ->whereNull('end_date')
        ->first();

    if ($incompleteInvoice) {
        return back()->with('error', 'An existing incomplete invoice for this package already exists.');
    }

    try {
        DB::beginTransaction();

        // ✅ Create Invoice
        $invoice = Invoice::create([
            'user_id'    => $user->id,
            'name'       => trim($user->f_name . ' ' . $user->last_name),
            'phone'      => $user->phone,
            'amount'     => $request->amount,
            'package_id' => $request->package_id,
            'duration'   => $request->duration ? $request->duration . ' days' : null,
            'currency'   => $request->currency,
        ]);

        // ✅ Generate PDF
        $pdf = \PDF::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

        $invoicesDir = public_path('invoices');
        if (!is_dir($invoicesDir)) {
            @mkdir($invoicesDir, 0775, true);
        }

        $filePath = 'invoices/invoice_' . $invoice->id . '.pdf';
        $pdf->save(public_path($filePath));

        $invoice->update(['pdf_path' => $filePath]);

        DB::commit();

        return redirect()->route('invoices.index')->with('success', 'Invoice generated and saved successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}



// public function store(Request $request)
//     {
//         // dd($request->all());
//         $request->validate([
//             'user_id' => 'required',
//             // 'start_date' => 'nullable|date',
//             // 'end_date' => 'nullable|date|after_or_equal:start_date',
//             'package_id' => 'required|integer|exists:packages,id',
//             'amount' => 'required|numeric',
//             'currency' => 'required|string|in:usd,lyd',
//         ]);

//         $user = User::findOrFail($request->user_id);

//         // Compute duration in days (inclusive)
//         // $startDate = \Carbon\Carbon::parse($request->start_date);
//         // $endDate = \Carbon\Carbon::parse($request->end_date);
//         // $durationDays = $startDate->diffInDays($endDate) + 1;

//         // Save to DB
//         $invoice = Invoice::create([
//             'user_id' => $user->id,
//             'name' => $user->f_name . ' ' . $user->last_name,
//             'phone' => $user->phone,
//             'amount' => $request->amount,
//             'package_id' => $request->package_id,
//             // 'start_date' => $startDate->toDateString() ?? null,
//             // 'end_date' => $endDate->toDateString() ?? null,
//             'duration' => $request->duration . ' days',
//             'currency' => $request->currency,
            
//         ]);

//         // Generate PDF
//         $pdf = PDF::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

//         // Ensure invoices directory exists
//         $invoicesDir = public_path('invoices');
//         if (!is_dir($invoicesDir)) {
//             @mkdir($invoicesDir, 0775, true);
//         }

//         $filePath = 'invoices/invoice_' . $invoice->id . '.pdf';
//         $pdf->save(public_path($filePath));

//         // Update DB with pdf path
//         $invoice->update(['pdf_path' => $filePath]);

//         return redirect()->route('invoices.index')->with('success', 'Invoice generated and saved!');
//     }
    public function showPdf(Invoice $invoice)
    {
        $pdf = PDF::loadView('admin.invoices.pdf', ['invoice' => $invoice]);
        return $pdf->stream('invoice_' . $invoice->id . '.pdf');
    }
 public function create()
    {
        $users = User::where(function ($query) {
            $query->whereNull('staff_type')
                  ->orWhere('staff_type', '');
        })
        ->where('admin_type', 0)
        ->orderBy('f_name')
        ->get();

    $packages = Package::orderBy('name')->get();

        return view('admin.invoices.create', compact('users', 'packages'));
    }
    public function destroy($id)
    {
        $package = Invoice::findOrFail($id);
        $package->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'invoice deleted successfully!');
    }



    /**
     * Manually trigger subscription expiration check
     */
    public function checkExpiration()
    {
        try {
            Artisan::call('subscription:check-expiration');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Subscription expiration check completed successfully!',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking subscription expiration: ' . $e->getMessage()
            ], 500);
        }
    }
}
