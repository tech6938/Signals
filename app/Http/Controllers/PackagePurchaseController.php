<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Invoice;
use App\Models\PackagePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PackagePurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:manageBuyer']);
    }
    // public function index()
    // {
    //     $purchases = PackagePurchase::with(['user', 'package'])->latest()->paginate(10);
    //     return view('admin.packages.purchaselist', compact('purchases'));
    // }
     public function index(Request $request)
    {
        $query = PackagePurchase::with(['user', 'package'])->orderBy('id', 'desc');

        // 🔍 Search by user name or email
        if ($request->filled('query')) {
            $search = $request->query('query');
            $query->whereHas('user', function ($q) use ($search) {
                    $q->where('f_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // 📄 Per page
        $perPage = $request->query('perPage', 10);
        $purchases = $query->paginate($perPage);

        // ⚡ If AJAX request, return partial view only
        if ($request->ajax()) {
            return view('admin.packages.partials.purchases_table', compact('purchases'))->render();
        }

        // Normal full view
        return view('admin.packages.purchaselist', compact('purchases'));
    }

    // Approve or reject payment
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:pending,approved,rejected'
    //     ]);

    //     $purchase = PackagePurchase::findOrFail($id);
    //     $purchase->status = $request->status;
    //     $purchase->save();

    //     // If approved, update user type to subscriber
    //     if ($request->status === 'approved') {
    //         $user = $purchase->user;
    //         $user->type = 'subscriber';
    //         $user->save();
    //     }

    //     return back()->with('success', 'Payment status updated successfully.');
    // }

//     public function updateStatus(Request $request, $id)
// {
//     $request->validate([
//         'status' => 'required|in:pending,approved,rejected'
//     ]);

//     $purchase = PackagePurchase::findOrFail($id);

//     // 🟢 If trying to approve, check if user already has this package in `user_value`
//     if ($request->status === 'approved') {
//         $userId = $purchase->user_id;
//         $packageId = $purchase->package_id;

//         $exists = \App\Models\ValueSubscription::where('user_id', $userId)
//                     ->where('package_id', $packageId)
//                     ->exists();

//         if (!$exists) {
//             return back()->with('error', 'This package is not assigned to the user. Please assign it before approving.');
//         }

//         // ✅ Assign user type = subscriber if not already
//         $user = $purchase->user;
//         if ($user->type !== 'subscriber') {
//             $user->type = 'subscriber';
//             $user->save();
//         }
//     }

//     // 🟠 Update status (after validation)
//     $purchase->status = $request->status;
//     $purchase->save();

//     return back()->with('success', 'Payment status updated successfully.');
// }
// public function updateStatus(Request $request, $id)
// {
//     $request->validate([
//         'status' => 'required|in:pending,approved,rejected'
//     ]);

//     $purchase = PackagePurchase::findOrFail($id);
//     $user = $purchase->user;

//     if ($request->status === 'approved') {
//         // Check if package is assigned
//         $exists = \App\Models\ValueSubscription::where('user_id', $user->id)
//                     ->where('package_id', $purchase->package_id)
//                     ->exists();

//         if (!$exists) {
//             return back()->with('error', 'This package is not assigned to the user. Please assign it before approving.');
//         }

//         // Set user type to subscriber
//         if ($user->type !== 'subscriber') {
//             $user->type = 'subscriber';
//             $user->save();
//         }
//     } else {
//         // If status is pending or rejected, revert user type
//         if ($user->type === 'subscriber') {
//             $user->type = 'simple_user';
//             $user->save();
//         }
//     }

//     $purchase->status = $request->status;
//     $purchase->save();

//     return back()->with('success', 'Payment status updated successfully.');
// }
// public function updateStatus(Request $request, $id)
// {
//     $request->validate([
//         'status' => 'required|in:pending,approved,rejected'
//     ]);

//     $purchase = \App\Models\PackagePurchase::findOrFail($id);
//     $user = $purchase->user;

//     // ✅ When admin approves the purchase
//     if ($request->status === 'approved') {
//         // Check if already assigned
//         $exists = \App\Models\ValueSubscription::where('user_id', $user->id)
//             ->where('package_id', $purchase->package_id)
//             ->exists();

//         // If not assigned, create the record
//         if (!$exists) {
//             \App\Models\ValueSubscription::create([
//                 'user_id'             => $user->id,
//                 'package_id'          => $purchase->package_id,
//                 'invoice_id'          => $purchase->invoice_id,        // from PackagePurchase
//                 'package_purchases_id' => $purchase->id,                // new column
//             ]);
//         }

//         // ✅ Set user type to subscriber
//         if ($user->type !== 'subscriber') {
//             $user->type = 'subscriber';
//             $user->save();
//         }
//     } else {
//         // If pending or rejected → revert user type
//         if ($user->type === 'subscriber') {
//             $user->type = 'simple_user';
//             $user->save();
//         }
//     }

//     // ✅ Save updated purchase status
//     $purchase->status = $request->status;
//     $purchase->save();

//     return back()->with('success', 'Payment status updated successfully.');
// }
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected'
    ]);

    $purchase = PackagePurchase::findOrFail($id);
    $user = $purchase->user;
    $package = $purchase->package; // relationship: belongsTo(Package::class)

    if ($request->status === 'approved') {

        // ✅ Calculate start and end date from today
        $startDate = now();
        $endDate = $startDate->copy()->addDays($package->duration_days);

        // ✅ Update the related invoice with duration info
        $invoice = Invoice::find($purchase->invoice_id);
        dd($purchase,$startDate, $endDate, $invoice );
        if ($invoice) {
            $invoice->update([
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'duration'   => $package->duration_days . ' days',
            ]);
        }

        // ✅ Create subscription if not already exists
        $exists = \App\Models\ValueSubscription::where('user_id', $user->id)
            ->where('package_id', $purchase->package_id)
            ->exists();

        if (!$exists) {
            \App\Models\ValueSubscription::create([
                'user_id'              => $user->id,
                'package_id'           => $purchase->package_id,
                'invoice_id'           => $purchase->invoice_id,
                'package_purchases_id' => $purchase->id,
            ]);
        }

        // ✅ Update user type if not already subscriber
        if ($user->type !== 'subscriber') {
            $user->type = 'subscriber';
            $user->save();
        }

    } else {
        // For pending or rejected → check if user has other approved subscriptions
        $hasOtherApproved = \App\Models\ValueSubscription::where('user_id', $user->id)
            ->where('id', '!=', $purchase->id)
            ->whereHas('packagePurchase', function($q) {
                $q->where('status', 'approved');
            })
            ->exists();

        if (!$hasOtherApproved && $user->type === 'subscriber') {
            $user->type = 'simple_user';
            $user->save();
        }
    }

    // ✅ Save the purchase status
    $purchase->status = $request->status;
    $purchase->save();

    return back()->with('success', 'Payment status updated successfully.');
}

}
