<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\userMessages;
use App\Models\Signal;
use App\Models\Message;
use App\Models\UserNotification;
use App\Models\ValueSubscription;
use App\Models\User;
use App\Models\PackagePurchase;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;



class ApiController extends Controller
{

    // public function purchase(Request $request)
    // {
    //     $request->validate([
    //         'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    //     ]);

    //     // Store screenshot in storage/app/public/purchase_screenshots
    //     $path = $request->file('screenshot')->store('purchase_screenshots', 'public');

    //     $package_id = 1;

    //     $purchase = PackagePurchase::create([
    //         'user_id'    => $request->user()->id, // From API token
    //         'package_id' => $package_id,
    //         'screenshot' => $path
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Purchase submitted successfully!',
    //         'data'    => $purchase
    //     ], 201);
    // }

    // public function purchase(Request $request)
    // {
    //     $request->validate([
    //         'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    //     ]);

    //     $user = $request->user();
    //     $package_id = 1; // Static package for now

    //     // ✅ Check if user already purchased this package
    //     $alreadyPurchased = \App\Models\PackagePurchase::where('user_id', $user->id)
    //         ->where('package_id', $package_id)
    //         ->exists();

    //     if ($alreadyPurchased) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'You have already purchased this package.'
    //         ], 409); // 409 Conflict
    //     }

    //     // Store screenshot
    //     $path = $request->file('screenshot')->store('purchase_screenshots', 'public');

    //     // Create purchase record
    //     $purchase = \App\Models\PackagePurchase::create([
    //         'user_id'    => $user->id,
    //         'package_id' => $package_id,
    //         'screenshot' => $path
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Purchase submitted successfully!',
    //         'data'    => $purchase
    //     ], 201);
    // }
//     public function purchase(Request $request)
// {
//     $request->validate([
//         'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     $user = $request->user();

//     // ✅ Step 1: Check if user already purchased any package
//     $alreadyPurchased = \App\Models\PackagePurchase::where('user_id', $user->id)->exists();

//     if ($alreadyPurchased) {
//         return response()->json([
//             'success' => false,
//             'message' => 'You have already purchased a package.'
//         ], 409);
//     }

//     // ✅ Step 2: Get the user's latest or active invoice (if any)
//     $invoice = \App\Models\Invoice::where('user_id', $user->id)->latest('id')->first();

//     // Optional: if you want to block if user has no invoice, you can do:
//     if (!$invoice) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No invoice found for this user.'
//         ], 404);
//     }

//     // ✅ Step 3: Choose the package (or get from your logic)
//     $package = \App\Models\Package::first(); // Example: static or default package

//     if (!$package) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No package available for purchase.'
//         ], 404);
//     }

//     // ✅ Step 4: Upload screenshot
//     $path = $request->file('screenshot')->store('purchase_screenshots', 'public');

//     // ✅ Step 5: Create the package purchase record with invoice_id
//     $purchase = \App\Models\PackagePurchase::create([
//         'user_id'    => $user->id,
//         'package_id' => $package->id,
//         'invoice_id' => $invoice->id, // 👈 link invoice here
//         'screenshot' => $path,
//     ]);

//     // ✅ Step 6: Return success response
//     return response()->json([
//         'success' => true,
//         'message' => 'Purchase submitted successfully!',
//         'data'    => $purchase
//     ], 201);
// }

// public function purchase(Request $request)
// {
//     $request->validate([
//         'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     $user = $request->user();

//     // ✅ Step 1: Check if user already purchased any package
//     $alreadyPurchased = \App\Models\PackagePurchase::where('user_id', $user->id)->exists();

//     if ($alreadyPurchased) {
//         return response()->json([
//             'success' => false,
//             'message' => 'You have already purchased a package.'
//         ], 409);
//     }

//     // ✅ Step 2: Get the latest invoice of the user
//     $invoice = \App\Models\Invoice::where('user_id', $user->id)->latest('id')->first();

//     if (!$invoice) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No invoice found for this user.'
//         ], 404);
//     }

//     // ✅ Step 3: Get package from the invoice (assuming invoice table has a package_id column)
//     $package = \App\Models\Package::find($invoice->package_id);

//     if (!$package) {
//         return response()->json([
//             'success' => false,
//             'message' => 'The package linked to your invoice no longer exists.'
//         ], 404);
//     }

//     // ✅ Step 4: Upload screenshot
//     $path = $request->file('screenshot')->store('purchase_screenshots', 'public');

//     // ✅ Step 5: Create purchase record using package from invoice
//     $purchase = \App\Models\PackagePurchase::create([
//         'user_id'    => $user->id,
//         'package_id' => $package->id,
//         'invoice_id' => $invoice->id,
//         'screenshot' => $path,
//         'status'     => 'pending', // optional default status
//     ]);

//     // ✅ Step 6: Return success response
//     return response()->json([
//         'success' => true,
//         'message' => 'Purchase submitted successfully!',
//         'data'    => $purchase
//     ], 201);
// }

public function purchase(Request $request)
{
    $request->validate([
        'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = $request->user();

    // ✅ Step 1: Get all invoices of this user (oldest first)
    $invoices = \App\Models\Invoice::where('user_id', $user->id)
        ->orderBy('id', 'asc')
        ->get();

    if ($invoices->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No invoices found for this user.'
        ], 404);
    }

    $targetInvoice = null;

    // ✅ Step 2: Find the first invoice that has no screenshot or has a rejected one
    foreach ($invoices as $invoice) {
        $purchase = \App\Models\PackagePurchase::where('user_id', $user->id)
            ->where('invoice_id', $invoice->id)
            ->latest('id')
            ->first();

        if (!$purchase || $purchase->status === 'rejected') {
            $targetInvoice = $invoice;
            break;
        }
    }

    // ✅ Step 3: If no eligible invoice found
    if (!$targetInvoice) {
        return response()->json([
            'success' => false,
            'message' => 'You have already uploaded screenshots for all active packages.',
        ], 400);
    }

    $package = \App\Models\Package::find($targetInvoice->package_id);

    if (!$package) {
        return response()->json([
            'success' => false,
            'message' => 'The package linked to your invoice no longer exists.'
        ], 404);
    }

    // ✅ Step 4: Upload screenshot
    $path = $request->file('screenshot')->store('purchase_screenshots', 'public');

    // ✅ Step 5: Create or replace rejected record
    if (isset($purchase) && $purchase && $purchase->status === 'rejected') {
        // Optional: delete old file if needed
        $purchase->update([
            'screenshot' => $path,
            'status'     => 'pending',
        ]);
        $newPurchase = $purchase;
    } else {
        $newPurchase = \App\Models\PackagePurchase::create([
            'user_id'    => $user->id,
            'package_id' => $package->id,
            'invoice_id' => $targetInvoice->id,
            'screenshot' => $path,
            'status'     => 'pending',
        ]);
    }

    // ✅ Step 6: Return response
    return response()->json([
        'success' => true,
        'message' => 'Screenshot uploaded successfully for your package!',
        'data'    => $newPurchase,
    ], 201);
}


    //  public function profileData()
    // {
    //     try {

    //         $authUser = auth()->user();

    //         // Check if user is authenticated
    //         if (!$authUser) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized: No valid token provided',
    //             ], 401);
    //         }
    //         $user = Auth::user();

    //         $subscription = ValueSubscription::with(['user', 'package', 'invoice'])
    //             ->where('user_id', $user->id)
    //             ->first();

    //             // dd($subscription);

    //         $data = [
    //             'name' => trim(($user->f_name ?? '') . ' ' . ($user->last_name ?? '')),
    //             'p_number' => $user->phone ?? null,
    //             'sub_type' => $subscription && $subscription->package ? $subscription->package->name : null,
    //             'start_date' => $subscription && $subscription->invoice ? $subscription->invoice->start_date : null,
    //             'end_date' => $subscription && $subscription->invoice ? $subscription->invoice->end_date : null,
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User data retrieved successfully',
    //             'data' => $data,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve user data: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }


    // public function profileData()
    // {
    //     try {
    //         $authUser = auth()->user();

    //         if (!$authUser) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized: No valid token provided',
    //             ], 401);
    //         }

    //         $user = $authUser;

    //         // Base data
    //         $data = [
    //             'name' => trim(($user->f_name ?? '') . ' ' . ($user->last_name ?? '')),
    //             'p_number' => $user->phone ?? null,
    //             'status' => $user->status ?? null,
    //         ];

    //         // Subscription info only for subscribers
    //         if ($user->type === 'subscriber') {
    //             $subscription = ValueSubscription::with(['package', 'invoice'])
    //                 ->where('user_id', $user->id)
    //                 ->first();

    //             $data['sub_type'] = $subscription && $subscription->package ? $subscription->package->name : null;

    //             // Format dates with Carbon
    //             $data['start_date'] = $subscription && $subscription->invoice
    //                 ? Carbon::parse($subscription->invoice->start_date)->format('Y-m-d')
    //                 : null;

    //             $data['end_date'] = $subscription && $subscription->invoice
    //                 ? Carbon::parse($subscription->invoice->end_date)->format('Y-m-d')
    //                 : null;
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User data retrieved successfully',
    //             'data' => $data,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve user data: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }



///   original
    public function profileData()
    {
        try {
            $authUser = auth()->user();
    
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No valid token provided',
                ], 401);
            }
    
            $user = $authUser;
    
            // Base data
            
               $subscription = ValueSubscription::with(['user', 'package', 'invoice'])
                ->where('user_id', $user->id)
                ->first();
                

                // dd($subscription);

            $data = [
                'name' => trim(($user->f_name ?? '') . ' ' . ($user->last_name ?? '')),
                'p_number' => $user->phone ?? null,
                'status' => $user->status ?? null,
                'sub_type' => $subscription && $subscription->package ? $subscription->package->name : null,
                'start_date' => $subscription && $subscription->invoice ? Carbon::parse($subscription->invoice->start_date)->format('Y-m-d') : null,
                'end_date' => $subscription && $subscription->invoice ? Carbon::parse($subscription->invoice->end_date)->format('Y-m-d') : null,
            ];
            
            // $data = [
            //     'name' => trim(($user->f_name ?? '') . ' ' . ($user->last_name ?? '')),
            //     'p_number' => $user->phone ?? null,
            //     'status' => $user->status ?? null,
            // ];
    
            // Fetch all approved packages for this user
            $subscriptions = PackagePurchase::with(['package', 'invoice'])
                ->where('user_id', $user->id)
                ->where('status', 'approved') // ✅ only approved
                ->get();
    
            // Format the subscription data
            $data['subscriptions'] = $subscriptions->map(function ($sub) {
                return [
                    'package_name' => $sub->package->name ?? null,
                    'package_id'   => $sub->package->id ?? null,
                    'invoice_id'   => $sub->invoice_id ?? null,
                    'screenshot'   => $sub->screenshot ?? null,
                    'status'       => $sub->status ?? null,
                    'start_date'   => $sub->invoice && $sub->invoice->start_date 
                        ? Carbon::parse($sub->invoice->start_date)->format('Y-m-d') 
                        : null,
                    'end_date'     => $sub->invoice && $sub->invoice->end_date 
                        ? Carbon::parse($sub->invoice->end_date)->format('Y-m-d') 
                        : null,
                ];
            });
    
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'data' => $data,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user data: ' . $e->getMessage(),
            ], 500);
        }
    }


    // public function getNews()
    // {
    //     try {
    //         // Dynamically get the base URL from config or .env
    //         $baseUrl = rtrim(config('filesystems.disks.public.url', env('APP_URL', 'http://localhost') . '/storage'), '/') . '/news_images/';
    //         $news = News::where('status', '1')->get()->map(function ($item) use ($baseUrl) {
    //             $item->image = $baseUrl . basename($item->image); // Prepend base URL to image filename
    //             return $item;
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'data' => $news,
    //             'message' => 'News retrieved successfully'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve news: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
//     public function getNews()
// {
//     try {
//         // Dynamically get the base URL from config or .env
//         $baseUrl = rtrim(config('filesystems.disks.public.url', env('APP_URL', 'http://localhost') . '/storage'), '/') . '/news_images/';

//         $news = News::where('status', '1')
//             ->orderBy('id', 'desc')
//             ->get()
//             ->map(function ($item) use ($baseUrl) {
//                 $item->image = $baseUrl . basename($item->image); // Prepend base URL to image filename
//                 return $item;
//             });

//         return response()->json([
//             'success' => true,
//             'data' => $news,
//             'message' => 'News retrieved successfully'
//         ], 200);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to retrieve news: ' . $e->getMessage()
//         ], 500);
//     }
// }
public function getNews()
{
    try {
        $user = Auth::user();

        // Base URL for news images
        $baseUrl = rtrim(config('filesystems.disks.public.url', env('APP_URL', 'http://localhost') . '/storage'), '/') . '/news_images/';

        // Fetch all active news
        $news = News::where('status', '1')
            ->orderBy('id', 'desc')
            ->get();

        // Filter based on audience type
        $filtered = $news->filter(function ($item) use ($user) {
            $type = $item->audience_type;

            return match ($type) {
                // Show to everyone
                'all', null => true,

                // Show only to subscribers
                'subscribers' => $user->type === 'subscriber',

                // Show only to users who bought the package
                'package' => $item->package_id &&
                    $user->packagePurchases()
                        ->where('package_id', $item->package_id)
                        ->where('status', 'approved')
                        ->exists(),

                // Hide for unknown audience type
                default => false,
            };
        })->values(); // Re-index

        // Prepend base URL to image field
        $filtered->transform(function ($item) use ($baseUrl) {
            if ($item->image) {
                $item->image = $baseUrl . basename($item->image);
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $filtered,
            'message' => 'News retrieved successfully',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve news: ' . $e->getMessage(),
        ], 500);
    }
}

    public function getSignals()
    {
        try {
            // Dynamically get the base URL for uploads
            $baseUrl = rtrim(config('filesystems.disks.public.url', env('APP_URL', 'http://localhost') . '/storage'), '/');

            // Fetch only active signals
            $signals = Signal::where('status', 1)->get()->map(function ($item) use ($baseUrl) {

                // Prepend base URL to icon paths
                $item->icon1 = $item->icon1 ? $baseUrl . '/' . ltrim($item->icon1, '/') : null;
                $item->icon2 = $item->icon2 ? $baseUrl . '/' . ltrim($item->icon2, '/') : null;

                // Format tp1-tp4 values
                foreach (['tp1', 'tp2', 'tp3', 'tp4'] as $tp) {
                    if (isset($item->$tp)) {
                        $value = floatval($item->$tp);
                        $item->$tp = ($value == floor($value)) ? (int)$value : round($value, 4);
                    }
                }

                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $signals,
                'message' => 'Signals retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve signals: ' . $e->getMessage()
            ], 500);
        }
    }


    // public function messages()
    // {
    //     try {
    //         $user = Auth::user();   // the user making the request

    //         // 🔒 Check if user is subscriber
    //         if ($user->type !== 'subscriber') {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'No messages for this user type',
    //                 'data'    => []        // return empty data
    //             ], 200);
    //         }

    //         // ✅ Only subscribers reach here
    //         $messages = userMessages::where('status', 1)->orderBy('id','desc')->
    //         get();
    //         $messages->transform(function ($message) {
    //             if ($message->image_path) {
    //                 $message->image_path = url('storage/' . $message->image_path);
    //             }
    //             return $message;
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Messages retrieved successfully',
    //             'data'    => $messages,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve messages: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function messages()
{
    try {
        $user = Auth::user();

        // Get all active messages
        $messages = userMessages::where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        // Filter messages that the current user should see
        $filtered = $messages->filter(function ($message) use ($user) {
            $type = $message->notification_type;

            // 1. No type → show to everyone (backward compatible)
            if (!$type) {
                return true;
            }

            return match ($type) {
                // Show to ALL users
                'all' => true,

                // Show only to subscribers
                'subscribers' => $user->type === 'subscriber',

                // Show only to non-subscribers
                'non_subscribers' => $user->type !== 'subscriber',

                // Show only to specific users (individual)
                'individual' => in_array($user->id, $message->target_user_ids ?? []),

                // Show only to users who bought the package
                'package_subscribers' => $message->package_id && $user->packagePurchases()
                    ->where('package_id', $message->package_id)
                    ->where('status', 'approved')
                    ->exists(),

                // Any other type → hide
                default => false,
            };
        })->values(); // re-index

        // Convert image path to full URL
        $filtered->transform(function ($message) {
            if ($message->image_path) {
                $message->image_path = asset('storage/' . $message->image_path);
            }
            return $message;
        });

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully',
            'data'    => $filtered,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve messages: ' . $e->getMessage(),
        ], 500);
    }
}

    public function sendMessage(Request $request)
    {
        try {
            // Get the authenticated user from the token
            $authUser = auth()->user();

            // Check if user is authenticated
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No valid token provided',
                ], 401);
            }

            // Validate request data
            $request->validate([
                'message' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',

            ]);

            // Find an admin user with status = 1
            $admin = User::where('status', 1)->first();

            // Check if an admin exists
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No admin user found with status = 1',
                ], 404);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('chat_images', 'public');
            }

            // Create the message
            $message = Message::create([
                'sender_id' => $authUser->id,
                'receiver_id' => $admin->id,
                'message' => $request->message,
                'image_path' => $imagePath,

            ]);

            // Return simplified response
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'image_url' => $message->image_path ? url('storage/' . $message->image_path) : null,

                ],
                'message' => 'Message sent successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getChat(Request $request)
    {
        try {
            // Get the authenticated user from the token
            $authUser = auth()->user();

            // Check if user is authenticated
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No valid token provided',
                ], 401);
            }

            // Find an admin user with status = 1
            $admin = User::where('status', 1)->first();

            // Check if an admin exists
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No admin user found with status = 1',
                ], 404);
            }

            // Retrieve messages where user is sender and admin is receiver, or vice versa
            $chat = Message::where(function ($q) use ($authUser, $admin) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $admin->id);
            })->orWhere(function ($q) use ($authUser, $admin) {
                $q->where('sender_id', $admin->id)->where('receiver_id', $authUser->id);
            })->orderBy('created_at', 'asc')->get()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'image_url' => $message->image_path ? url('storage/' . $message->image_path) : null,

                ];
            });

            return response()->json([
                'success' => true,
                'data' => $chat,
                'message' => 'Chat retrieved successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve chat: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getUsersWithChats()
    {
        $adminId = 1;

        $users = Message::where('sender_id', $adminId)
            ->orWhere('receiver_id', $adminId)
            ->with(['sender', 'receiver'])
            ->get()
            ->map(function ($msg) use ($adminId) {
                return $msg->sender_id == $adminId ? $msg->receiver : $msg->sender;
            })
            ->unique('id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }


    // public function getUserNotifications(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not authenticated.',
    //         ], 401);
    //     }

    //     $notifications = UserNotification::with('notification')
    //         ->where('user_id', $user->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function ($userNotification) {
    //             return [
    //                 'id' => $userNotification->id,
    //                 'title' => $userNotification->notification->title ?? '',
    //                 'description' => $userNotification->notification->description ?? '',
    //                 'sent_at' => $userNotification->created_at,
    //             ];
    //         });


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User notifications fetched successfully.',
    //         'data' => $notifications,
    //         'total_count' => $notifications->count(),
    //     ]);
    // }
    
    public function getUserNotifications(Request $request)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not authenticated.',
        ], 401);
    }

    $notifications = UserNotification::with('notification')
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($userNotification) {
            return [
                'id'          => $userNotification->id,
                'title'       => $userNotification->notification->title ?? '',
                'description' => $userNotification->notification->description ?? '',
                'sent_at'     => \Carbon\Carbon::parse($userNotification->getRawOriginal('created_at'))
                                    ->setTimezone(config('app.timezone'))
                                    ->format('Y-m-d H:i:s'),
            ];
        });

    return response()->json([
        'status'      => true,
        'message'     => 'User notifications fetched successfully.',
        'data'        => $notifications,
        'total_count' => $notifications->count(),
    ]);
}
}
