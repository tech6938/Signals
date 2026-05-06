<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\PackagePurchase;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'permission:managChats']);
    }
    public function index()
    {
        $adminId = Auth::id();

        // Get all conversations where admin is either sender or receiver
        $allConversations = Message::with(['sender', 'receiver'])
            ->where(function($query) use ($adminId) {
                $query->where('sender_id', $adminId)
                      ->orWhere('receiver_id', $adminId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Group conversations by user and get the latest message for each
        $conversationsByUser = [];
        foreach ($allConversations as $msg) {
            // Determine the counterparty (the other user in the conversation)
            $counterparty = null;
            $chatType = null;
            
            if ($msg->sender_id == $adminId) {
                // Admin sent this message - counterparty is receiver
                $counterparty = $msg->receiver;
                $chatType = 'admin_initiated';
            } else {
                // User sent this message - counterparty is sender
                $counterparty = $msg->sender;
                $chatType = 'user_initiated';
            }
            
            if (!$counterparty) { continue; }
            
            $key = $counterparty->id;
            if (!isset($conversationsByUser[$key])) {
                // Store the latest message and add metadata
                $msg->counterparty = $counterparty;
                $msg->chat_type = $chatType;
                $msg->is_admin_initiated = ($chatType === 'admin_initiated');
                $conversationsByUser[$key] = $msg;
            }
        }

        // Separate by user type and chat initiation
        $userInitiatedSubscribers = collect($conversationsByUser)
            ->filter(function ($msg) { 
                return ($msg->counterparty->type ?? null) === 'subscriber' && !$msg->is_admin_initiated; 
            })
            ->values();

        $userInitiatedSimpleUsers = collect($conversationsByUser)
            ->filter(function ($msg) { 
                return ($msg->counterparty->type ?? null) === 'simple_user' && !$msg->is_admin_initiated; 
            })
            ->values();

        $adminInitiatedSubscribers = collect($conversationsByUser)
            ->filter(function ($msg) { 
                return ($msg->counterparty->type ?? null) === 'subscriber' && $msg->is_admin_initiated; 
            })
            ->values();

        $adminInitiatedSimpleUsers = collect($conversationsByUser)
            ->filter(function ($msg) { 
                return ($msg->counterparty->type ?? null) === 'simple_user' && $msg->is_admin_initiated; 
            })
            ->values();

        if (request()->ajax()) {
            return view('admin.chat._tables', compact(
                'userInitiatedSubscribers', 
                'userInitiatedSimpleUsers',
                'adminInitiatedSubscribers',
                'adminInitiatedSimpleUsers'
            ));
        }

        return view('admin.chat.index', compact(
            'userInitiatedSubscribers', 
            'userInitiatedSimpleUsers',
            'adminInitiatedSubscribers',
            'adminInitiatedSimpleUsers'
        ));
    }
    

    public function show($userId)
    {
        $user = User::findOrFail($userId);
    
        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    
        // Load chat messages
        $messages = Message::with('sender')->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', Auth::id());
        })
        ->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })
        ->orderBy('id', 'asc')
        ->get();
    
        // Latest purchased package
        $latestPackagePurchase = PackagePurchase::with('package')
            ->where('user_id', $userId)
            ->latest()
            ->first();
    
        // Latest invoice
        $latestInvoice = Invoice::where('user_id', $userId)
            ->orderByDesc('start_date') // or ->latest() if preferred
            ->first();
    
        if (request()->ajax()) {
            return view('admin.chat.messages', compact('messages'));
        }
    
        return view('admin.chat.show', compact('user', 'messages', 'latestPackagePurchase', 'latestInvoice'));
    }


    // public function show($userId)
    // {
    //     $user = User::findOrFail($userId);

    //     Message::where('sender_id', $userId)
    //         ->where('receiver_id', Auth::id())
    //         ->where('is_read', false)
    //         ->update(['is_read' => true]);

    //     $messages = Message::with('sender')->where(function ($q) use ($userId) {
    //         $q->where('sender_id', $userId)->where('receiver_id', Auth::id());
    //     })
    //         ->orWhere(function ($q) use ($userId) {
    //             $q->where('sender_id', Auth::id())->where('receiver_id', $userId);
    //         })
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     if (request()->ajax()) {
    //         return view('admin.chat.messages', compact('messages'));
    //     }

    //     return view('admin.chat.show', compact('user', 'messages'));
    // }

    // Send a reply
    public function reply(Request $request, $userId)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        if (!$request->filled('message') && !$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a message or attach an image.'
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $userId,
            'message'     => $request->input('message', '') ?? '',
            'image_path'  => $imagePath,
            'is_read'     => false,
        ]);

        return response()->json(['success' => true, 'message' => $msg]);
    }



    public function sendMessage(Request $request)
    {
        try {
            // Get the authenticated user from the token
            $authUser = Auth::user();

            // Check if user is authenticated
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: No valid token provided',
                ], 401);
            }

            // Validate request data
            $request->validate([
                'message' => 'required|string',
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

            // Create the message
            $message = Message::create([
                'sender_id' => $authUser->id,
                'receiver_id' => $admin->id,
                'message' => $request->message,
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
            $authUser = Auth::user();

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

    // Get all users that chatted with admin
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

    // Admin can start a new chat with any user
    public function startNewChat()
    {
        $users = User::where('status', 1)
            ->where('id', '!=', Auth::id()) // Exclude current admin
            ->select('id', 'f_name', 'last_name', 'email', 'type')
            ->orderBy('f_name')
            ->get();

        return view('admin.chat.start_new', compact('users'));
    }

    // Admin sends initial message to start conversation
    public function sendInitialMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        $user = User::findOrFail($userId);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $userId,
            'message'     => $request->input('message'),
            'image_path'  => $imagePath,
            'is_read'     => false,
        ]);

        return redirect()
            ->route('admin.chat.show', $userId)
            ->with('success', 'Message sent successfully!');
    }
}
