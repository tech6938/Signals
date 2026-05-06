<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class ValueSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|manager|staff']);
    }

    public function index(Request $request)
    {
        $query = $request->get('query', '');
        $perPage = $request->get('perPage', 10);
        $packageId = $request->get('package_id');

        $subscriptions = User::with(['subscribedPackages' => function ($q) {
            $q->select('packages.id', 'name');
        }])
            ->whereHas('subscribedPackages', function($q) use ($packageId){
                if ($packageId) {
                    $q->where('packages.id', $packageId);
                }
            })
            ->when($query, function ($q) use ($query) {
                $q->where('f_name', 'like', "%$query%")
                    ->orWhere('last_name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage, 'package_id' => $packageId]);

        $subscriberCount = $subscriptions->total();

        if ($request->ajax()) {
            $tableBody = view('admin.value_subscriptions.partials._table_body', compact('subscriptions'))->render();
            $paginationLinks = view('admin.value_subscriptions.partials._pagination', compact('subscriptions'))->render();
            return response()->json([
                'tableBody' => $tableBody,
                'paginationLinks' => $paginationLinks,
                'subscriberCount' => $subscriberCount,
            ]);
        }

        return view('admin.value_subscriptions.index', compact('subscriptions','packageId','subscriberCount'));
    }

    public function searchValues(Request $request)
{
    $q = $request->get('q', '');
    if (strlen($q) < 1) {
        return response()->json([]);
    }

    $packages = Package::where('name', 'like', "%$q%")
        ->orderBy('name')
        ->limit(10)
        ->get(['id', 'name']);

    return response()->json($packages->map(function ($p) {
        return [
            'id' => $p->id,
            'text' => $p->name,
        ];
    }));
}


    public function create()
{
    $users = User::where(function ($query) {
            $query->whereNull('staff_type')
                  ->orWhere('staff_type', '');
        })
        ->where('admin_type', 0)
        ->orderBy('f_name')
        ->get(['id', 'f_name', 'last_name', 'email']);

    $packages = Package::orderBy('name')->get(['id', 'name']);

    return view('admin.value_subscriptions.create', compact('users', 'packages'));
}


// public function getUserPurchasedPackages(Request $request)
// {
//     $userId = $request->get('user_id');
    
//     if (!$userId) {
//         return response()->json([]);
//     }
    
//     $user = User::findOrFail($userId);
    
//     // Get packages that user has purchased (has invoices for)
//     $purchasedPackages = $user->invoices()
//         ->with('package:id,name')
//         ->whereNotNull('package_id')
//         ->get()
//         ->pluck('package')
//         ->filter()
//         ->unique('id')
//         ->values();
    
//     return response()->json($purchasedPackages);
// }
// public function getUserPurchasedPackages(Request $request)
// {
//     $userId = $request->get('user_id');

//     // 🧩 If no user_id passed, return empty array
//     if (!$userId) {
//         return response()->json([]);
//     }

//     // 🧩 Fetch approved purchased packages for this user
//     $packages = \App\Models\PackagePurchase::where('user_id', $userId)
//         ->where('status', 'pending') // or 'paid' depending on your app logic
//         ->with('package:id,name') // eager load only id and name of package
//         ->get()
//         ->pluck('package') // extract the package data
//         ->filter() // remove nulls
//         ->unique('id') // avoid duplicates
//         ->values(); // reset indexes

//     // 🧩 Return packages as JSON
//     return response()->json($packages);
// }
// public function getUserPurchasedPackages(Request $request)
// {
//     $userId = $request->get('user_id');

//     if (!$userId) {
//         return response()->json([]);
//     }

//     // ✅ Step 1: Get all assigned packages for this user
//     $assignedPackageIds = \App\Models\ValueSubscription::where('user_id', $userId)
//         ->pluck('package_id')
//         ->toArray();

//     // ✅ Step 2: Get all purchased packages (approved)
//     $purchases = \App\Models\PackagePurchase::where('user_id', $userId)
//         ->where('status', 'approved')
//         ->with('package:id,name')
//         ->get()
//         ->pluck('package')
//         ->filter()
//         ->unique('id')
//         ->values();

//     // ✅ Step 3: Attach “is_assigned” flag
//     $packages = $purchases->map(function ($package) use ($assignedPackageIds) {
//         return [
//             'id' => $package->id,
//             'name' => $package->name,
//             'is_assigned' => in_array($package->id, $assignedPackageIds),
//         ];
//     });

//     return response()->json($packages);
// }

public function getUserPurchasedPackages(Request $request)
{
    $userId = $request->get('user_id');

    if (!$userId) {
        return response()->json([]);
    }

    // ✅ Step 1: Get all assigned packages for this user
    $assignedPackageIds = \App\Models\ValueSubscription::where('user_id', $userId)
        ->pluck('package_id')
        ->toArray();

    // ✅ Step 2: Get all purchased packages (approved OR pending)
    $purchases = \App\Models\PackagePurchase::where('user_id', $userId)
        ->whereIn('status', ['approved', 'pending']) // 👈 show both
        ->with(['package:id,name'])
        ->get();

    // ✅ Step 3: Attach flags for status and assignment
    $packages = $purchases->map(function ($purchase) use ($assignedPackageIds) {
        return [
            'id' => $purchase->package->id ?? null,
            'name' => $purchase->package->name ?? 'Unknown Package',
            'status' => ucfirst($purchase->status), // "Approved" / "Pending"
            'is_assigned' => in_array($purchase->package_id, $assignedPackageIds),
        ];
    })->filter(fn($pkg) => $pkg['id'] !== null)->unique('id')->values();

    return response()->json($packages);
}


    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'package_id' => 'required|exists:packages,id',
    //     ]);

    //     $user = User::findOrFail($data['user_id']);
    //     $package = Package::findOrFail($data['package_id']);
        
    //     // Check if user has purchased this package (has an invoice for it)
    //     $hasPurchased = $user->invoices()->where('package_id', $data['package_id'])->exists();
        
    //     if (!$hasPurchased) {
    //         // Get list of packages the user has actually purchased
    //         $purchasedPackages = $user->invoices()
    //             ->with('package:id,name')
    //             ->whereNotNull('package_id')
    //             ->get()
    //             ->pluck('package.name')
    //             ->filter()
    //             ->unique()
    //             ->values();
            
    //         $purchasedPackagesList = $purchasedPackages->isNotEmpty() 
    //             ? 'The user has only purchased: ' . $purchasedPackages->implode(', ')
    //             : 'The user has not purchased any packages yet.';
            
    //         return redirect()->back()
    //             ->withErrors(['package_id' => "Cannot assign package '{$package->name}' because the user has not paid for it. {$purchasedPackagesList}"])
    //             ->withInput();
    //     }

    //     // Check if user is already subscribed to this package
    //     $alreadySubscribed = $user->subscribedPackages()->where('package_id', $data['package_id'])->exists();
        
    //     if ($alreadySubscribed) {
    //         return redirect()->back()
    //             ->withErrors(['package_id' => "User is already subscribed to package '{$package->name}'."])
    //             ->withInput();
    //     }

    //     $user->subscribedPackages()->syncWithoutDetaching([$data['package_id']]);

    //     return redirect()->route('admin.value-subscriptions.index')->with('success', "Successfully assigned package '{$package->name}' to user.");
    // }
    
    
    public function store(Request $request)
{
    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'package_id' => 'required|exists:packages,id',
    ]);

    $user = \App\Models\User::findOrFail($data['user_id']);
    $package = \App\Models\Package::findOrFail($data['package_id']);

    // ✅ 1. Allow assignment if user purchased (pending OR approved)
    $purchase = \App\Models\PackagePurchase::where('user_id', $user->id)
        ->where('package_id', $package->id)
        ->whereIn('status', ['pending', 'approved'])
        ->first();

    if (!$purchase) {
        // User hasn't purchased this package at all
        $purchasedPackages = \App\Models\PackagePurchase::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->with('package:id,name')
            ->get()
            ->pluck('package.name')
            ->filter()
            ->unique()
            ->values();

        $purchasedPackagesList = $purchasedPackages->isNotEmpty()
            ? 'The user has only purchased: ' . $purchasedPackages->implode(', ')
            : 'The user has not purchased any packages yet.';

        return redirect()->back()
            ->withErrors(['package_id' => "Cannot assign package '{$package->name}' because the user has not paid for it. {$purchasedPackagesList}"])
            ->withInput();
    }

    // ✅ 2. Check if user is already subscribed to this package
    $alreadySubscribed = $user->subscribedPackages()
        ->where('package_id', $package->id)
        ->exists();

    if ($alreadySubscribed) {
        return redirect()->back()
            ->withErrors(['package_id' => "User is already subscribed to package '{$package->name}'."])
            ->withInput();
    }

    // ✅ 3. Store subscription with related invoice and purchase
    $invoiceId = $purchase->invoice_id ?? null;
    $packagePurchaseId = $purchase->id;

    $user->subscribedPackages()->attach($package->id, [
        'invoice_id' => $invoiceId,
        'package_purchases_id' => $packagePurchaseId,
    ]);

    return redirect()
        ->route('admin.value-subscriptions.index')
        ->with('success', "Successfully assigned package '{$package->name}' to user with invoice #{$invoiceId}.");
}


    
    
    
    
    
    
    
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'package_id' => 'required|exists:packages,id',
    //     ]);

    //     $user = User::findOrFail($data['user_id']);
    //     $user->subscribedPackages()->syncWithoutDetaching([$data['package_id']]);

    //     return redirect()->route('admin.value-subscriptions.index')->with('success', 'Subscription saved.');
    // }

    // public function destroy($userId, $packageId)
    // {
    //     $user = User::findOrFail($userId);
    //     $user->subscribedPackages()->detach($packageId);
    //     return redirect()->back()->with('success', 'Subscription removed.');
    // }
    
    public function destroy($userId, $packageId)
{
    // Find the user
    $user = User::findOrFail($userId);

    // Find the subscription record in user_value table
    $subscription = \App\Models\ValueSubscription::where('user_id', $userId)
        ->where('package_id', $packageId)
        ->first();

    if ($subscription) {
        // Step 1: Get the package_purchases_id
        $packagePurchaseId = $subscription->package_purchases_id;

        // Step 2: Delete from user_value table
        $subscription->delete();

        // Step 3: Update the related package_purchases record to 'pending'
        \App\Models\PackagePurchase::where('id', $packagePurchaseId)
            ->update(['status' => 'pending']);
    }

    return redirect()->back()->with('success', 'Subscription removed and status updated to pending.');
}

}
