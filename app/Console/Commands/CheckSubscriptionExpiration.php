<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Notification;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiration extends Command
{
    protected $signature = 'subscription:check-expiration';
    protected $description = 'Check for expiring subscriptions and send notifications';

    public function handle()
    {
        $this->info('Checking subscription expiration...');
        Log::info('Subscription cron RUNNING at: ' . now());

        $today = Carbon::today();

        // Check expiring soon (3 days warning)
        $this->checkExpiringSoon();

        // Check expired subscriptions
        $this->checkExpired();

        $this->info('Subscription expiration check completed.');
    }

    private function checkExpiringSoon()
    {
        $expiringInvoices = Invoice::expiringSoon(3)
            ->whereHas('user', function($query) {
                $query->where('type', 'subscriber');
            })
            ->with('user')
            ->get();

        Log::info("Found {$expiringInvoices->count()} subscriptions expiring soon");

        foreach ($expiringInvoices as $invoice) {
            $this->sendExpiringSoonNotification($invoice);
        }
    }

    private function checkExpired()
    {
        // Simply use the scope - no extra where conditions
        $expiredInvoices = Invoice::expired()
            ->whereHas('user', function($query) {
                $query->where('type', 'subscriber');
            })
            ->with('user')
            ->get();

        Log::info("Found {$expiredInvoices->count()} expired subscriptions to process");

        foreach ($expiredInvoices as $invoice) {
            Log::info("Processing expired - User: {$invoice->user->id}, End Date: {$invoice->end_date}");

            // Send notification first
            $this->sendExpiredNotification($invoice);

            // Then update user status
            $updated = User::where('id', $invoice->user->id)
                ->where('type', 'subscriber')
                ->update(['type' => 'simple_user']);

            if ($updated) {
                Log::info("✓ User {$invoice->user->id} changed from subscriber to simple_user");
            } else {
                Log::warning("✗ User {$invoice->user->id} was NOT updated. Current type: " . User::find($invoice->user->id)->type);
            }
        }
    }

    private function sendExpiringSoonNotification($invoice)
    {
        $user = $invoice->user;
        $daysLeft = Carbon::parse($invoice->end_date)->diffInDays(Carbon::today());

        $title = 'Subscription Expiring Soon';
        $description = "Hi {$user->f_name}, your subscription will expire in {$daysLeft} days on " . Carbon::parse($invoice->end_date)->format('M d, Y') . ". Please renew to continue enjoying our services.";

        try {
            if ($user->fcm_token) {
                app(FcmService::class)->sendToTokens(
                    [$user->fcm_token],
                    $title,
                    $description,
                    [
                        'type' => 'subscription_warning',
                        'invoice_id' => (string) $invoice->id,
                        'expiration_date' => $invoice->end_date,
                    ]
                );

                Notification::create([
                    'title' => $title,
                    'description' => $description,
                    'send_to' => 'individual',
                    'target_users' => json_encode([$user->id]),
                    'sent' => true,
                    'sent_at' => now(),
                    'sent_count' => 1,
                ]);

                $invoice->update([
                    'warning_notification_sent' => true,
                    'warning_notification_sent_at' => now(),
                ]);

                Log::info("Expiring soon notification sent to user {$user->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send expiring soon notification: " . $e->getMessage());
        }
    }

    private function sendExpiredNotification($invoice)
    {
        $user = $invoice->user;

        $title = 'Subscription Expired';
        $description = "Hi {$user->f_name}, your subscription has expired. Please renew your subscription to continue accessing our premium services.";

        try {
            if ($user->fcm_token) {
                app(FcmService::class)->sendToTokens(
                    [$user->fcm_token],
                    $title,
                    $description,
                    [
                        'type' => 'subscription_expired',
                        'invoice_id' => (string) $invoice->id,
                        'expiration_date' => $invoice->end_date,
                    ]
                );

                Notification::create([
                    'title' => $title,
                    'description' => $description,
                    'send_to' => 'individual',
                    'target_users' => json_encode([$user->id]),
                    'sent' => true,
                    'sent_at' => now(),
                    'sent_count' => 1,
                ]);

                $invoice->update([
                    'expiration_notification_sent' => true,
                    'expiration_notification_sent_at' => now(),
                ]);

                Log::info("Expired notification sent to user {$user->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send expired notification: " . $e->getMessage());
        }
    }
}
