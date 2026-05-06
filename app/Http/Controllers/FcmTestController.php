<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmTestController extends Controller
{
    public function sendDirect()
    {
        $serviceAccountPath = env('FIREBASE_CREDENTIALS');

        if (!file_exists($serviceAccountPath)) {
            return "Firebase credentials JSON not found at: $serviceAccountPath";
        }

        // Create Firebase Messaging instance (v7.x)
        $messaging = (new Factory())
            ->withServiceAccount($serviceAccountPath)
            ->createMessaging();

        // Send to a test topic (no device token needed)
        $topic = 'test-admin';

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create('Hello', 'Test message from Admin'));

        try {
            $messaging->send($message);
            return 'Notification sent successfully!';
        } catch (\Throwable $e) {
            return 'Firebase Messaging error: ' . $e->getMessage();
        }
    }
}
