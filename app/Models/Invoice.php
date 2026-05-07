<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'duration',
        'start_date',
        'end_date',
        'package_id',
        'amount',
        'currency',
        'pdf_path',
        'warning_notification_sent',
        'expiration_notification_sent',
        'warning_notification_sent_at',
        'expiration_notification_sent_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'warning_notification_sent' => 'boolean',
        'expiration_notification_sent' => 'boolean',
        'warning_notification_sent_at' => 'datetime',
        'expiration_notification_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Scope for subscriptions expiring soon (within 3 days)
     */
    // public function scopeExpiringSoon($query, $days = 3)
    // {
    //     return $query->where('end_date', '<=', now()->addDays($days))
    //         ->where('end_date', '>=', now())
    //         ->where('warning_notification_sent', false);
    // }
    public function scopeExpiringSoon($query, $days = 3)
    {
        return $query->whereDate('end_date', '<=', now()->addDays($days))
            ->whereDate('end_date', '>=', now())
            ->where('warning_notification_sent', false);
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('end_date', '<=', now())
            ->where('expiration_notification_sent', false);
    }

    /**
     * Check if subscription is expiring soon
     */
    public function isExpiringSoon($days = 3)
    {
        return $this->end_date <= now()->addDays($days)
            && $this->end_date >= now()
            && !$this->warning_notification_sent;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return $this->end_date < now() && !$this->expiration_notification_sent;
    }
}
