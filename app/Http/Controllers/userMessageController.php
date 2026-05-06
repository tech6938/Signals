<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userMessages;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



class userMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
      public function index(Request $request)
{
    $query = $request->get('query', '');
    $perPage = (int) $request->get('perPage', 10);

    $userMessages = userMessages::when($query, function ($q) use ($query) {
        $q->where('title', 'like', "%{$query}%")
          ->orWhere('description', 'like', "%{$query}%");
    })
        ->orderBy('id', 'desc')
        ->paginate($perPage)
        ->appends(['query' => $query, 'perPage' => $perPage]);

    // Handle AJAX
    if ($request->ajax()) {
        $table = view('admin.message.partials.table-body', compact('userMessages'))->render();
        $pagination = view('admin.message.partials.pagination', compact('userMessages'))->render();

        return response()->json([
            'table' => $table,
            'pagination' => $pagination,
        ]);
    }

    // Normal page render
    return view('admin.message.list', compact('userMessages'));
}





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.message.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     
      public function store(Request $request)
    {
        // dd($request->all());
        // Validate request inputs
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
             'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|boolean',
            'send_notification' => 'sometimes|boolean',
            'notification_send_to' => 'required_if:send_notification,1|in:all,subscribers,non_subscribers,staff,individual,value_subscribers,package_subscribers',
            'target_users' => 'required_if:notification_send_to,individual',
            'value_id' => 'required_if:notification_send_to,value_subscribers|nullable|exists:values,id',
            'package_id' => 'required_if:notification_send_to,package_subscribers|nullable|exists:packages,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload if exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('analyses', 'public');
        }
        
        $targetUserIds = [];

$rawTargetUsers = $request->input('target_users');

if (is_string($rawTargetUsers) && trim($rawTargetUsers) !== '') {
    $targetUserIds = array_filter(array_map('intval', explode(',', $rawTargetUsers)));
} elseif (is_array($rawTargetUsers)) {
    $targetUserIds = array_filter(array_map('intval', $rawTargetUsers));
}

$message = userMessages::create([
    'title'             => $request->title,
    'description'       => $request->description,
    'status'            => $request->status,
    'date'            => $request->date,
    'time'            => $request->time,
    'image_path'        => $imagePath,
    'notification_type' => $request->send_notification == '1' ? $request->notification_send_to : null,
    'target_user_ids'   => $request->notification_send_to === 'individual' ? $targetUserIds : null,
    'package_id'        => $request->notification_send_to === 'package_subscribers' ? $request->package_id : null,
]);

        

        // Send notification if requested
        if ($request->send_notification == '1') {
            $this->sendNotificationFromMessage($message, $request, $imagePath);
        }

        return redirect()->route('userMessages.index')->with('success', 'Message created successfully.');
    }
    // public function store(Request $request)
    // {
    //     // $request->validate([
    //     //     'title' => 'required|string|max:255',
    //     //     'description' => 'required|string',
    //     //     'status' => 'required|boolean',
    //     //     'send_notification' => 'sometimes|boolean',
    //     //     'notification_send_to' => 'required_if:send_notification,1|in:all,subscribers,non_subscribers,staff,individual,value_subscribers',
    //     //     'target_users' => 'required_if:notification_send_to,individual',
    //     //     'value_id' => 'required_if:notification_send_to,value_subscribers|nullable|exists:values,id',
    //     //     'image' => 'nullable|image|max:2048',
    //     // ]);
        
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'status' => 'required|boolean',
    //         'send_notification' => 'sometimes|boolean',
    //         'notification_send_to' => 'required_if:send_notification,1|in:all,subscribers,non_subscribers,staff,individual,value_subscribers,package_subscribers',
    //         'target_users' => 'required_if:notification_send_to,individual',
    //         'value_id' => 'required_if:notification_send_to,value_subscribers|nullable|exists:values,id',
    //         'package_id' => 'required_if:notification_send_to,package_subscribers|nullable|exists:packages,id',
    //         'image' => 'nullable|image|max:2048',
    //     ]);


    //     // Handle image upload (optional)
    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('analyses', 'public');
    //     }

    //     // Create the message
    //     $message = userMessages::create([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'status' => $request->status,
    //         'image_path' => $imagePath,
    //     ]);

    //     // Send notification if requested
    //     if ($request->send_notification == '1') {
    //         $this->sendNotificationFromMessage($message, $request, $imagePath);
    //     }

    //     return redirect()->route('userMessages.index')->with('success', 'Message created successfully.');
    // }


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
        $userMessage = userMessages::findOrFail($id);
        return view('admin.message.edit', compact('userMessage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status'      => 'required|boolean',
        ]);

        $userMessage = userMessages::findOrFail($id);

        $userMessage->update([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status,
            'date'            => $request->date,
    'time'            => $request->time,
        ]);

        return redirect()
            ->route('userMessages.index')
            ->with('success', 'Message updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(userMessages $userMessage)
    {
        $userMessage->delete();
        return redirect()->route('userMessages.index')->with('success', 'Message deleted successfully.');
    }

    /**
     * Send notification from message
     */
    // private function sendNotificationFromMessage(userMessages $message, Request $request, $imagePath = null)
    // {
    //     try {
    //         // Process target_users if it's a string
    //         $targetUsers = $request->target_users;
    //         if (is_string($targetUsers) && !empty($targetUsers)) {
    //             $targetUsers = array_filter(array_map('trim', explode(',', $targetUsers)));
    //             $targetUsers = array_map('intval', $targetUsers);
    //         } elseif (empty($targetUsers)) {
    //             $targetUsers = [];
    //         }

    //         // Validate target users for individual notifications
    //         if ($request->notification_send_to === 'individual') {
    //             if (empty($targetUsers)) {
    //                 Log::warning('No target users selected for individual notification');
    //                 return;
    //             }

    //             // Check if all user IDs exist
    //             $existingUsers = User::whereIn('id', $targetUsers)->pluck('id')->toArray();
    //             $invalidUsers = array_diff($targetUsers, $existingUsers);
                
    //             if (!empty($invalidUsers)) {
    //                 Log::warning('Invalid user IDs for notification: ' . implode(', ', $invalidUsers));
    //                 return;
    //             }
    //         }

    //         // Create notification record
    //       // Create notification record
    //         $notification = Notification::create([
    //             'title' => $message->title,
    //             'description' => $message->description,
    //             'send_to' => $request->notification_send_to,
    //             'value_id' => $request->notification_send_to === 'value_subscribers' ? $request->value_id : null,
    //             'package_id' => $request->notification_send_to === 'package_subscribers' ? $request->package_id : null,
    //             'target_users' => $request->notification_send_to === 'individual' ? $targetUsers : null,
    //         ]);


    //         // Send notification immediately
    //         $this->sendNotification($notification);

    //         Log::info('Notification sent from message', [
    //             'message_id' => $message->id,
    //             'notification_id' => $notification->id,
    //             'send_to' => $request->notification_send_to
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Failed to send notification from message: ' . $e->getMessage(), [
    //             'message_id' => $message->id,
    //             'error' => $e->getTraceAsString()
    //         ]);
    //     }
    // }

    /**
     * Send notification to users
     */
    // private function sendNotification(Notification $notification)
    // {
    //     $users = $notification->getTargetUsers();

    //     if ($users->isEmpty()) {
    //         Log::warning('No target users found for notification', ['notification_id' => $notification->id]);
    //         return;
    //     }

    //     // Get FCM tokens for users who have them
    //     $tokens = $users->whereNotNull('fcm_token')->pluck('fcm_token')->all();

    //     if (!empty($tokens)) {
    //         try {
    //             app(FcmService::class)->sendToTokens(
    //                 $tokens,
    //                 $notification->title,
    //                 $notification->description,
    //                 [
    //                     'type' => 'admin_notification',
    //                     'notification_id' => (string) $notification->id,
    //                     // If there is a related message with image, include public URL
    //                     'image_url' => optional(userMessages::where('title', $notification->title)->where('description', $notification->description)->latest('id')->first())->image_path
    //                 ]
    //             );

    //             // Update notification as sent
    //             $notification->update([
    //                 'sent' => true,
    //                 'sent_at' => now(),
    //                 'sent_count' => count($tokens),
    //             ]);

    //             Log::info('Notification sent successfully', [
    //                 'notification_id' => $notification->id,
    //                 'sent_count' => count($tokens)
    //             ]);

    //         } catch (\Exception $e) {
    //             Log::error('Failed to send notification: ' . $e->getMessage(), [
    //                 'notification_id' => $notification->id,
    //                 'error' => $e->getTraceAsString()
    //             ]);
    //         }
    //     } else {
    //         Log::warning('No FCM tokens found for notification', ['notification_id' => $notification->id]);
    //     }
    // }


    // private function sendNotification(Notification $notification)
    // {
    //     $users = $notification->getTargetUsers();
    
    //     if ($users->isEmpty()) {
    //         Log::warning('No target users found for notification', ['notification_id' => $notification->id]);
    //         return;
    //     }
    
    //     $fcmService = app(FcmService::class);
    //     $sentCount = 0;
    
    //     foreach ($users as $user) {
    //         try {
    //             // Track notification for this user
    //             $userNotification = UserNotification::create([
    //                 'notification_id' => $notification->id,
    //                 'user_id' => $user->id,
    //             ]);
    
    //             if (!empty($user->fcm_token)) {
    //                 // Send notification via existing method sendToTokens()
    //                 $fcmService->sendToTokens(
    //                     [$user->fcm_token],
    //                     $notification->title,
    //                     $notification->description,
    //                     [
    //                         'type' => 'admin_notification',
    //                         'notification_id' => (string) $notification->id,
    //                         'image_url' => optional(
    //                             userMessages::where('title', $notification->title)
    //                                 ->where('description', $notification->description)
    //                                 ->latest('id')
    //                                 ->first()
    //                         )->image_path,
    //                     ]
    //                 );
    
    //                 // Mark as sent
    //                 $userNotification->update([
    //                     'is_sent' => true,
    //                     'sent_at' => now(),
    //                 ]);
    
    //                 $sentCount++;
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send notification to user', [
    //                 'user_id' => $user->id,
    //                 'notification_id' => $notification->id,
    //                 'error' => $e->getMessage(),
    //             ]);
    //         }
    //     }
    
    //     // Update main notification stats
    //     $notification->update([
    //         'sent' => $sentCount > 0,
    //         'sent_at' => now(),
    //         'sent_count' => $sentCount,
    //     ]);
    
    //     Log::info('Notification sending completed', [
    //         'notification_id' => $notification->id,
    //         'sent_count' => $sentCount,
    //     ]);
    // }
    
    //   private function sendNotificationFromMessage(userMessages $message, Request $request, $imagePath = null)
    // {
    //     try {
    //         // Process target_users if it's a string (comma separated)
    //         $targetUsers = $request->target_users;
    //         if (is_string($targetUsers) && !empty($targetUsers)) {
    //             $targetUsers = array_filter(array_map('trim', explode(',', $targetUsers)));
    //             $targetUsers = array_map('intval', $targetUsers);
    //         } elseif (empty($targetUsers)) {
    //             $targetUsers = [];
    //         }

    //         // Validate target users if notification is sent to individual users
    //         if ($request->notification_send_to === 'individual') {
    //             if (empty($targetUsers)) {
    //                 Log::warning('No target users selected for individual notification');
    //                 return;
    //             }

    //             // Check if all user IDs exist
    //             $existingUsers = User::whereIn('id', $targetUsers)->pluck('id')->toArray();
    //             $invalidUsers = array_diff($targetUsers, $existingUsers);

    //             if (!empty($invalidUsers)) {
    //                 Log::warning('Invalid user IDs for notification: ' . implode(', ', $invalidUsers));
    //                 return;
    //             }
    //         }

    //         // Create notification record
    //         $notification = Notification::create([
    //             'title' => $message->title,
    //             'description' => $message->description,
    //             'send_to' => $request->notification_send_to,
    //             'value_id' => $request->notification_send_to === 'value_subscribers' ? $request->value_id : null,
    //             'package_id' => $request->notification_send_to === 'package_subscribers' ? $request->package_id : null,
    //             'target_users' => $request->notification_send_to === 'individual' ? $targetUsers : null,
    //         ]);

    //         // Send the notification immediately
    //         $this->sendNotification($notification);

    //         Log::info('Notification sent from message', [
    //             'message_id' => $message->id,
    //             'notification_id' => $notification->id,
    //             'send_to' => $request->notification_send_to
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send notification from message: ' . $e->getMessage(), [
    //             'message_id' => $message->id,
    //             'error' => $e->getTraceAsString()
    //         ]);
    //     }
    // }
    
    private function sendNotificationFromMessage(userMessages $message, Request $request, $imagePath = null)
{
    try {
        // ──────────────────────────────────────────────────────
        // 1. Get data from the SAVED message (not from $request)
        // ──────────────────────────────────────────────────────
        $sendTo = $message->notification_type; // e.g. 'package_subscribers'
        $targetUserIds = $message->target_user_ids ?? [];
        $packageId = $message->package_id;

        // ──────────────────────────────────────────────────────
        // 2. Validate individual users (only if type is 'individual')
        // ──────────────────────────────────────────────────────
        if ($sendTo === 'individual') {
            if (empty($targetUserIds)) {
                Log::warning('No target users for individual message', ['message_id' => $message->id]);
                return;
            }

            $existing = User::whereIn('id', $targetUserIds)->pluck('id')->toArray();
            $invalid = array_diff($targetUserIds, $existing);

            if (!empty($invalid)) {
                Log::warning('Invalid user IDs in individual message', [
                    'message_id' => $message->id,
                    'invalid_ids' => $invalid
                ]);
                return;
            }
        }

        // ──────────────────────────────────────────────────────
        // 3. Create Notification record using SAVED data
        // ──────────────────────────────────────────────────────
        $notification = Notification::create([
            'title'        => $message->title,
            'description'  => $message->description,
            'send_to'      => $sendTo,
            'value_id'     => $sendTo === 'value_subscribers' ? $request->value_id : null,
            'package_id'   => $sendTo === 'package_subscribers' ? $packageId : null,
            'target_users' => $sendTo === 'individual' ? $targetUserIds : null,
        ]);

        // ──────────────────────────────────────────────────────
        // 4. Send the notification
        // ──────────────────────────────────────────────────────
        $this->sendNotification($notification);

        Log::info('Notification sent from message', [
            'message_id'       => $message->id,
            'notification_id'  => $notification->id,
            'notification_type'=> $sendTo,
            'package_id'       => $packageId,
            'target_user_count'=> count($targetUserIds),
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to send notification from message', [
            'message_id' => $message->id,
            'error'      => $e->getMessage(),
            'trace'      => $e->getTraceAsString(),
        ]);
    }
}

    // private function sendNotification(Notification $notification)
    // {
    //     $users = $notification->getTargetUsers();

    //     if ($users->isEmpty()) {
    //         Log::warning('No target users found for notification', ['notification_id' => $notification->id]);
    //         return;
    //     }

    //     $fcmService = app(FcmService::class);
    //     $sentCount = 0;

    //     foreach ($users as $user) {
    //         try {
    //             // Track notification for this user
    //             $userNotification = UserNotification::create([
    //                 'notification_id' => $notification->id,
    //                 'user_id' => $user->id,
    //             ]);

    //             if (!empty($user->fcm_token)) {
    //                 // Send notification via FCM service
    //                 $fcmService->sendToTokens(
    //                     [$user->fcm_token],
    //                     $notification->title,
    //                     $notification->description,
    //                     [
    //                         'type' => 'admin_notification',
    //                         'notification_id' => (string) $notification->id,
    //                         'image_url' => optional(
    //                             userMessages::where('title', $notification->title)
    //                                 ->where('description', $notification->description)
    //                                 ->latest('id')
    //                                 ->first()
    //                         )->image_path,
    //                     ]
    //                 );

    //                 // Mark as sent
    //                 $userNotification->update([
    //                     'is_sent' => true,
    //                     'sent_at' => now(),
    //                 ]);

    //                 $sentCount++;
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send notification to user', [
    //                 'user_id' => $user->id,
    //                 'notification_id' => $notification->id,
    //                 'error' => $e->getMessage(),
    //             ]);
    //         }
    //     }

    //     // Update notification status
    //     $notification->update([
    //         'sent' => $sentCount > 0,
    //         'sent_at' => now(),
    //         'sent_count' => $sentCount,
    //     ]);

    //     Log::info('Notification sending completed', [
    //         'notification_id' => $notification->id,
    //         'sent_count' => $sentCount,
    //     ]);
    // }
    
    // Inside your userMessageController.php



private function sendNotification(Notification $notification)
{
    // 1. Get ONLY the correct users (based on send_to, including package_subscribers)
    $users = $notification->getTargetUsers();

    if ($users->isEmpty()) {
        Log::warning('No target users found for notification', [
            'notification_id' => $notification->id,
            'send_to' => $notification->send_to,
            'package_id' => $notification->package_id,
        ]);
        return;
    }

    $fcmService = app(FcmService::class);
    $sentCount = 0;

    // 2. Loop through ONLY the correct users
    foreach ($users as $user) {
        try {
            // Track this notification for this user
            $userNotification = UserNotification::create([
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);

            // 3. Send FCM only if user has token
            if (!empty($user->fcm_token)) {
                // Find the original message to get image
                $message = userMessages::where('title', $notification->title)
                    ->where('description', $notification->description)
                    ->latest('id')
                    ->first();

                $imageUrl = $message?->image_path
                    ? asset('storage/' . $message->image_path)
                    : null;

                $fcmService->sendToTokens(
                    [$user->fcm_token],
                    $notification->title,
                    $notification->description,
                    [
                        'type'             => 'admin_notification',
                        'notification_id'  => (string) $notification->id,
                        'message_id'       => (string) ($message->id ?? ''),
                        'image_url'        => $imageUrl,
                        'click_action'     => route('userMessages.show', $message->id ?? ''),
                    ]
                );

                // Mark as sent
                $userNotification->update([
                    'is_sent'  => true,
                    'sent_at'  => now(),
                ]);

                $sentCount++;
            } else {
                Log::info('User has no FCM token (skipped)', [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to user', [
                'user_id'           => $user->id,
                'notification_id'   => $notification->id,
                'error'             => $e->getMessage(),
                'trace'             => $e->getTraceAsString(),
            ]);
        }
    }

    // 4. Update main notification stats
    $notification->update([
        'sent'       => $sentCount > 0,
        'sent_at'    => now(),
        'sent_count' => $sentCount,
    ]);

    Log::info('Notification sending completed', [
        'notification_id' => $notification->id,
        'send_to'         => $notification->send_to,
        'package_id'      => $notification->package_id,
        'total_users'     => $users->count(),
        'sent_count'      => $sentCount,
    ]);
}

}
