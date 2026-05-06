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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring subscriptions and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking subscription expiration...');
        Log::info('Subscription cron RUNNING at: ' . now());
        
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);
        
        // Check for subscriptions expiring in 3 days (before expiration notification)
        $this->checkExpiringSoon($threeDaysFromNow);
        
        // Check for expired subscriptions (after expiration notification)
        $this->checkExpired($today);
        
        $this->info('Subscription expiration check completed.');
    }
    
    /**
     * Check for subscriptions expiring soon and send warning notifications
     */
    private function checkExpiringSoon($date)
    {
        $expiringInvoices = Invoice::expiringSoon(3)
            ->where('end_date', $date->toDateString())
            ->whereHas('user', function($query) {
                $query->where('type', 'subscriber');
            })
            ->with('user')
            ->get();
            
        foreach ($expiringInvoices as $invoice) {
            $this->sendExpiringSoonNotification($invoice);
        }
        
        $this->info("Checked {$expiringInvoices->count()} subscriptions expiring soon.");
    }
    
    /**
     * Check for expired subscriptions and send expiration notifications
     */
    private function checkExpired($date)
    {
        $expiredInvoices = Invoice::expired()
            ->where('end_date', '<', $date->toDateString())
            ->whereHas('user', function($query) {
                $query->where('type', 'subscriber');
            })
            ->with('user')
            ->get();
            
        foreach ($expiredInvoices as $invoice) {
            $this->sendExpiredNotification($invoice);
            $this->updateUserStatus($invoice->user);
        }
        
        $this->info("Checked {$expiredInvoices->count()} expired subscriptions.");
    }
    
    /**
     * Send notification for subscription expiring soon
     */
    private function sendExpiringSoonNotification($invoice)
    {
        $user = $invoice->user;
        $daysLeft = Carbon::parse($invoice->end_date)->diffInDays(Carbon::today());
        
        $title = 'Subscription Expiring Soon';
        $description = "Hi {$user->f_name}, your subscription will expire in {$daysLeft} days on " . Carbon::parse($invoice->end_date)->format('M d, Y') . ". Please renew to continue enjoying our services.";
        
        try {
            // Create notification record
            $notification = Notification::create([
                'title' => $title,
                'description' => $description,
                'send_to' => 'individual',
                'target_users' => [$user->id],
                'sent' => false,
            ]);
            
            // Send FCM notification
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
                
                $notification->update([
                    'sent' => true,
                    'sent_at' => now(),
                    'sent_count' => 1,
                ]);
                
                // Mark warning notification as sent
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
    
    /**
     * Send notification for expired subscription
     */
    private function sendExpiredNotification($invoice)
    {
        $user = $invoice->user;
        
        $title = 'Subscription Expired';
        $description = "Hi {$user->f_name}, your subscription has expired. Please renew your subscription to continue accessing our premium services.";
        
        try {
            // Create notification record
            $notification = Notification::create([
                'title' => $title,
                'description' => $description,
                'send_to' => 'individual',
                'target_users' => [$user->id],
                'sent' => false,
            ]);
            
            // Send FCM notification
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
                
                $notification->update([
                    'sent' => true,
                    'sent_at' => now(),
                    'sent_count' => 1,
                ]);
                
                // Mark expiration notification as sent
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
    
    /**
     * Update user status when subscription expires
     */
    private function updateUserStatus($user)
    {
        try {
            // Change user type from subscriber to simple_user
            $user->update(['type' => 'simple_user']);
            
            Log::info("User {$user->id} status updated to simple_user due to expired subscription");
            
        } catch (\Exception $e) {
            Log::error("Failed to update user status: " . $e->getMessage());
        }
    }
}