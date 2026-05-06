<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TestPushNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('Laravel 🚀')
                    ->setBody('Your FCM push is working!')
                    ->setImage('https://laravel.com/img/logomark.min.svg')
            )
            ->setData(['extra' => 'payload']) // optional custom data
            ->setHttp([
                'verify' => 'C:/wamp64/bin/php/php8.2.26/extras/ssl/cacert.pem', // force cert path
            ]);
    }
}
