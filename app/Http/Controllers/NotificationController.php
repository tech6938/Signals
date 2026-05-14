<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = $request->get('query', '');
        $perPage = $request->get('perPage', 10);

        $notifications = Notification::when($query, function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage]);

        if ($request->ajax()) {
            return view('admin.notifications.partials.table-body', compact('notifications'))->render();
        }

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        try {
            // 1. Process target_users (string → array)
            $targetUserIds = [];
            $raw = $request->input('target_users');
            if (is_string($raw) && trim($raw) !== '') {
                $targetUserIds = array_filter(array_map('intval', explode(',', $raw)));
            } elseif (is_array($raw)) {
                $targetUserIds = array_filter(array_map('intval', $raw));
            }

            // 2. Validation
            $request->validate([
                'title'        => 'required|string|max:255',
                'description'  => 'required|string',
                'send_to'      => 'required|in:all,subscribers,non_subscribers,individual,package_subscribers',
                'target_users' => 'required_if:send_to,individual',
                'package_id'   => 'required_if:send_to,package_subscribers|nullable|exists:packages,id',
            ]);

            $sendTo = $request->send_to;

            // 3. Validate individual users
            if ($sendTo === 'individual') {
                if (empty($targetUserIds)) {
                    return back()->withErrors(['target_users' => 'Select at least one user.'])->withInput();
                }
                $existing = User::whereIn('id', $targetUserIds)->pluck('id')->toArray();
                $invalid = array_diff($targetUserIds, $existing);
                if (!empty($invalid)) {
                    return back()->withErrors(['target_users' => 'Invalid users: ' . implode(', ', $invalid)])->withInput();
                }
            }

            // 4. Create notification
            $notification = Notification::create([
                'title'        => $request->title,
                'description'  => $request->description,
                'send_to'      => $sendTo,
                'package_id'   => $sendTo === 'package_subscribers' ? $request->package_id : null,
                'target_users' => $sendTo === 'individual' ? $targetUserIds : null,
            ]);

            // 5. Send
            $this->sendNotification($notification);

            Log::info('Standalone notification sent', [
                'notification_id'   => $notification->id,
                'send_to'           => $sendTo,
                'package_id'        => $request->package_id,
                'target_user_count' => count($targetUserIds),
            ]);

            return redirect()->route('notifications.index')
                ->with('success', 'Notification sent successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Failed to send notification.'])->withInput();
        }
    }

    public function show(Notification $notification)
    {
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $notification->update($request->only('title', 'description'));

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully!');
    }

    public function destroy(Notification $notification)
    {
        try {
            $notification->delete();
            return redirect()->route('notifications.index')
                ->with('success', 'Notification deleted.');
        } catch (\Exception $e) {
            return redirect()->route('notifications.index')
                ->with('error', 'Failed to delete.');
        }
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) return response()->json([]);

        $users = User::where(function ($q) use ($query) {
            $q->where('f_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
        })
            ->select('id', 'f_name', 'last_name', 'email', 'type')
            ->limit(10)
            ->get();

        return response()->json($users->map(fn($u) => [
            'id'    => $u->id,
            'text'  => "$u->f_name $u->last_name ($u->email) - " . ucfirst($u->type),
            'email' => $u->email,
            'type'  => $u->type
        ]));
    }

    // THIS IS THE ONLY sendNotification() METHOD NEEDED HERE
    private function sendNotification(Notification $notification)
    {
        $users = $notification->getTargetUsers();
        if ($users->isEmpty()) {
            Log::info('No users to notify', ['notification_id' => $notification->id]);
            return;
        }

        $fcm = app(FcmService::class);
        $sent = 0;

        foreach ($users as $user) {
            try {
                $un = UserNotification::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $user->id,
                ]);

                if (!empty($user->fcm_token)) {
                    $fcm->sendToTokens(
                        [$user->fcm_token],
                        $notification->title,
                        $notification->description,
                        [
                            'type'            => 'admin_notification',
                            'notification_id' => (string) $notification->id,
                        ]
                    );
                    $un->update(['is_sent' => true, 'sent_at' => now()]);
                    $sent++;
                }
            } catch (\Exception $e) {
                Log::error('FCM failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        $notification->update([
            'sent'       => $sent > 0,
            'sent_at'    => now(),
            'sent_count' => $sent,
        ]);
    }

    public function resend(Notification $notification)
    {
        $this->sendNotification($notification);
        return redirect()->route('notifications.index')
            ->with('success', 'Notification resent!');
    }
}
