<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackagePurchase;
use App\Models\BankDetail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class WebviewPackageController extends Controller
{

  

// public function index(Request $request)
// {
//     $packages = Package::where('status', 1)->get();
//     $banks = BankDetail::where('is_active', 1)->get();

//     // Get token (can be from query or header)
//     $token = $request->input('token') ?? $request->query('token');

//     Log::info('🔑 Webview /packages hit - Token received:', ['token' => $token]);

//     $user = null;

//     if ($token) {
//         try {
//             // ✅ Find user via Sanctum token
//             $accessToken = PersonalAccessToken::findToken($token);

//             if ($accessToken) {
//                 $user = $accessToken->tokenable; // get the related user
//                 session([
//                     'webview_user_id'    => $user->id,
//                     'webview_user_name'  => $user->f_name . ' ' . $user->last_name,
//                     'webview_user_phone' => $user->phone,
//                 ]);

//                 Log::info('✅ Webview user session created', [
//                     'user_id' => $user->id,
//                     'name' => $user->f_name . ' ' . $user->last_name,
//                     'phone' => $user->phone,
//                 ]);
//             } else {
//                 Log::warning('⚠️ Invalid Sanctum token - not found', ['token' => $token]);
//             }
//         } catch (\Exception $e) {
//             Log::error('❌ Error in Webview package index: ' . $e->getMessage());
//         }
//     } else {
//         Log::warning('⚠️ No token received in request');
//     }

//     return view('webview.packages', compact('packages', 'banks', 'user'));
// }


public function index(Request $request)
{
    // return view('webview.coming_soon');
    
    $packages = Package::where('status', 1)->get();
    $banks = BankDetail::where('is_active', 1)->get();

    $token = $request->input('token') ?? $request->query('token');
    $user = null;
    $packageStatuses = [];

    if ($token) {
        try {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                $user = $accessToken->tokenable;

                session([
                    'webview_user_id'    => $user->id,
                    'webview_user_name'  => $user->f_name . ' ' . $user->last_name,
                    'webview_user_phone' => $user->phone,
                ]);

                // 🔍 Check each package for status
                foreach ($packages as $package) {
                    $status = 'available';

                    // Check for active or pending purchase
                    $purchase = PackagePurchase::where('user_id', $user->id)
                        ->where('package_id', $package->id)
                        ->latest()
                        ->first();

                    if ($purchase) {
                        if ($purchase->status === 'pending') {
                            $status = 'pending';
                        } elseif ($purchase->status === 'approved') {
                            // check expiration
                            if ($purchase->end_date && Carbon::parse($purchase->end_date)->isPast()) {
                                $status = 'expired';
                            } else {
                                $status = 'active';
                            }
                        }
                    } else {
                        // Check for invoice (means user started payment)
                        $invoice = Invoice::where('user_id', $user->id)
                            ->where('package_id', $package->id)
                            ->latest()
                            ->first();

                        if ($invoice) {
                            $status = 'in_progress';
                        }
                    }

                    $packageStatuses[$package->id] = $status;
                }

            } else {
                Log::warning('⚠️ Invalid Sanctum token', ['token' => $token]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Webview error: ' . $e->getMessage());
        }
    }

    return view('webview.packages', compact('packages', 'banks', 'user', 'packageStatuses'));
}


// public function generateInvoice(Request $request)
// {
//     try {
//         $request->validate([
//             'package_id' => 'required|exists:packages,id',
//             'bank_id' => 'required|exists:bank_details,id',
//             'currency' => 'required|in:usd,lyd',
//             'token' => 'nullable|string',
//             'user_id' => 'nullable|exists:users,id'
//         ]);

//         $user = null;

//         // Method 1: Session user
//         if (session('webview_user_id')) {
//             $user = User::find(session('webview_user_id'));
//         }

//         // Method 2: Token from request
//         if (!$user && $request->token) {
//             $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token)?->tokenable;
//         }

//         // Method 3: Bearer token
//         if (!$user && $request->bearerToken()) {
//             $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
//         }

//         // Method 4: Authenticated session
//         if (!$user) {
//             $user = Auth::user();
//         }

//         // Method 5: user_id from request
//         if (!$user && $request->user_id) {
//             $user = User::find($request->user_id);
//         }

//         if (!$user) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'User not authenticated. Please login first.',
//                 'error_type' => 'not_authenticated'
//             ], 401);
//         }
//         Log::info($user);
//         $package = Package::findOrFail($request->package_id);

//         // 🔹 Check if user already has ANY active subscription
//         // $hasActiveSubscription = Invoice::where('user_id', $user->id)
//         //     ->where('end_date', '>=', now())
//         //     ->exists();

//         // if ($hasActiveSubscription) {
//         //     return response()->json([
//         //         'success' => false,
//         //         'message' => 'You already have an active subscription. You cannot purchase another package until it expires.',
//         //         'error_type' => 'already_subscribed'
//         //     ], 400);
//         // }

//         // 🔹 Check if user already has an active subscription for the same package
// $hasActiveSubscription = Invoice::where('user_id', $user->id)
//     ->where('package_id', $request->package_id)
//     ->where('end_date', '>=', now())
//     ->exists();

// if ($hasActiveSubscription) {
//     return response()->json([
//         'success' => false,
//         'message' => 'You already have an active subscription for this package. Please wait until it expires before purchasing again.',
//         'error_type' => 'already_subscribed_same_package'
//     ], 400);
// }


//         // Calculate dates
//         $startDate = now();
//         $endDate = now()->addDays($package->duration_days);
//         $durationDays = $package->duration_days;

//         // Determine amount
//         $amount = $request->currency === 'lyd' ? $package->lyd_price : $package->price;

//         // Validate user info
//         if (empty($user->f_name) || empty($user->last_name)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'User profile is incomplete. Please update your first name and last name.',
//                 'error_type' => 'incomplete_profile'
//             ], 400);
//         }

//         if (empty($user->phone)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'User phone number is missing. Please update your profile.',
//                 'error_type' => 'missing_phone'
//             ], 400);
//         }

//         // Create invoice
//         $invoice = Invoice::create([
//             'user_id' => $user->id,
//             'name' => $user->f_name . ' ' . $user->last_name,
//             'phone' => $user->phone,
//             'amount' => $amount,
//             'package_id' => $package->id,
//             'start_date' => $startDate->toDateString(),
//             'end_date' => $endDate->toDateString(),
//             'duration' => $durationDays . ' days',
//             'currency' => $request->currency,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Invoice generated successfully! Your subscription will be activated after payment verification.',
//             'invoice_id' => $invoice->id
//         ]);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation error: ' . implode(', ', $e->errors()),
//             'error_type' => 'validation_error'
//         ], 422);
//     } catch (\Exception $e) {
//         Log::error('Invoice generation error: ' . $e->getMessage());
//         return response()->json([
//             'success' => false,
//             'message' => 'Error generating invoice: ' . $e->getMessage(),
//             'error_type' => 'server_error'
//         ], 500);
//     }
// }
public function generateInvoice(Request $request)
{
    try {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'bank_id' => 'required|exists:bank_details,id',
            'currency' => 'required|in:usd,lyd',
            'token' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $user = null;

        // 🔹 Method 1: Session user
        if (session('webview_user_id')) {
            $user = \App\Models\User::find(session('webview_user_id'));
        }

        // 🔹 Method 2: Token from request
        if (!$user && $request->token) {
            $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token)?->tokenable;
        }

        // 🔹 Method 3: Bearer token
        if (!$user && $request->bearerToken()) {
            $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
        }

        // 🔹 Method 4: Authenticated session
        if (!$user) {
            $user = \Illuminate\Support\Facades\Auth::user();
        }

        // 🔹 Method 5: user_id from request
        if (!$user && $request->user_id) {
            $user = \App\Models\User::find($request->user_id);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated. Please login first.',
                'error_type' => 'not_authenticated'
            ], 401);
        }

        $package = \App\Models\Package::findOrFail($request->package_id);

        // 🔹 NEW CONDITION: Check for unpaid invoice (no purchase linked yet)
        $latestInvoice = \App\Models\Invoice::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($latestInvoice) {
            $linkedPurchase = \App\Models\PackagePurchase::where('invoice_id', $latestInvoice->id)->first();

            if (!$linkedPurchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an existing invoice without payment proof. Please upload your payment before purchasing another package.',
                    'invoice_id' => $latestInvoice->id,
                    'error_type' => 'pending_payment'
                ], 400);
            }
        }

        // 🔹 Calculate subscription duration
        $startDate = now();
        $endDate = now()->addDays($package->duration_days);
        $durationDays = $package->duration_days;

        // 🔹 Determine price based on currency
        $amount = $request->currency === 'lyd' ? $package->lyd_price : $package->price;

        // 🔹 Validate user info
        if (empty($user->f_name) || empty($user->last_name)) {
            return response()->json([
                'success' => false,
                'message' => 'User profile is incomplete. Please update your first name and last name.',
                'error_type' => 'incomplete_profile'
            ], 400);
        }

        if (empty($user->phone)) {
            return response()->json([
                'success' => false,
                'message' => 'User phone number is missing. Please update your profile.',
                'error_type' => 'missing_phone'
            ], 400);
        }

        // 🔹 Create new invoice
        $invoice = \App\Models\Invoice::create([
            'user_id' => $user->id,
            'name' => $user->f_name . ' ' . $user->last_name,
            'phone' => $user->phone,
            'amount' => $amount,
            'package_id' => $package->id,
            // 'start_date' => $startDate->toDateString(),
            // 'end_date' => $endDate->toDateString(),
            'duration' => $durationDays . ' days',
            'currency' => $request->currency,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully! Please make payment and upload your proof for verification.',
            'invoice_id' => $invoice->id
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error: ' . implode(', ', $e->errors()),
            'error_type' => 'validation_error'
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Invoice generation error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error generating invoice: ' . $e->getMessage(),
            'error_type' => 'server_error'
        ], 500);
    }
}




//     public function generateInvoice(Request $request)
//     {
//         try {
//             $request->validate([
//                 'package_id' => 'required|exists:packages,id',
//                 'bank_id' => 'required|exists:bank_details,id',
//                 'currency' => 'required|in:usd,lyd',
//                 'token' => 'nullable|string', // Allow token parameter for Flutter app
//                 'user_id' => 'nullable|exists:users,id' // Allow user_id parameter
//             ]);

//             // Try to get user from multiple sources
//             $user = null;
            
//             // Method 1: Check session data (for Flutter app webview)
//             if (session('webview_user_id')) {
//                 $user = User::find(session('webview_user_id'));
//             }
            
//             // Method 2: Check if token is passed as parameter (from Flutter app)
//             if (!$user && $request->token) {
//                 $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token)?->tokenable;
//             }
            
//             // Method 3: Check if user is authenticated via API token in header (Sanctum)
//             if (!$user && $request->bearerToken()) {
//                 $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
//             }
            
//             // Method 4: Try session authentication (web users)
//             if (!$user) {
//                 $user = Auth::user();
//             }
            
//             // Method 5: Check if user_id is passed directly
//             if (!$user && $request->user_id) {
//                 $user = User::find($request->user_id);
//             }
            
//             if (!$user) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'User not authenticated. Please login first.',
//                     'error_type' => 'not_authenticated'
//                 ], 401);
//             }

//             $package = Package::findOrFail($request->package_id);

//             // Check if user already has an active subscription for this package
//             $hasActiveSubscription = Invoice::where('user_id', $user->id)
//                 ->where('package_id', $package->id)
//                 ->where('end_date', '>=', now())
//                 ->exists();

//             if ($hasActiveSubscription) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'You already have an active subscription for this package!',
//                     'error_type' => 'already_subscribed'
//                 ], 400);
//             }

//             // Calculate dates
//             $startDate = now();
//             $endDate = now()->addDays($package->duration_days);
//             $durationDays = $package->duration_days;

//             // Determine amount based on currency
//             $amount = $request->currency === 'lyd' ? $package->lyd_price : $package->price;

//             // Validate required fields before creating invoice
//             if (empty($user->f_name) || empty($user->last_name)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'User profile is incomplete. Please update your first name and last name.',
//                     'error_type' => 'incomplete_profile'
//                 ], 400);
//             }

//             if (empty($user->phone)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'User phone number is missing. Please update your profile.',
//                     'error_type' => 'missing_phone'
//                 ], 400);
//             }

//             // Create invoice
//             $invoice = Invoice::create([
//                 'user_id' => $user->id,
//                 'name' => $user->f_name . ' ' . $user->last_name,
//                 'phone' => $user->phone,
//                 'amount' => $amount,
//                 'package_id' => $package->id,
//                 'start_date' => $startDate->toDateString(),
//                 'end_date' => $endDate->toDateString(),
//                 'duration' => $durationDays . ' days',
//                 'currency' => $request->currency,
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Invoice generated successfully! Your subscription will be activated after payment verification.',
//                 'invoice_id' => $invoice->id
//             ]);

//         } catch (\Illuminate\Validation\ValidationException $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation error: ' . implode(', ', $e->errors()),
//                 'error_type' => 'validation_error'
//             ], 422);
//         } catch (\Exception $e) {
//             Log::error('Invoice generation error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Error generating invoice: ' . $e->getMessage(),
//                 'error_type' => 'server_error'
//             ], 500);
//         }
// }


    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $path = $request->file('screenshot')->store('purchase_screenshots','public');

        PackagePurchase::create([
            'user_id'    => Auth::id(),   // pass or handle user id
            'package_id' => $request->package_id,
            'screenshot' => $path
        ]);

        return back()->with('success','Purchase submitted successfully!');
    }
}
