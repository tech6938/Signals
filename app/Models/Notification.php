<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'send_to',
        'value_id',
        'package_id',        // Add this line
        'target_users',
        'sent',
        'sent_at',
        'sent_count',
        'image_path',
    ];

    protected $casts = [
        'target_users' => 'array',
        'sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get users based on the send_to criteria
     */
     
     public function getTargetUsers()
    {
        switch ($this->send_to) {
            case 'all':
                return User::all();
            case 'subscribers':
                return User::where('type', 'subscriber')->get();
            case 'value_subscribers':
                if ($this->value_id) {
                    return User::whereHas('subscribedValues', function($q){
                        $q->where('values.id', $this->value_id);
                    })->get();
                }
                return collect();
            case 'non_subscribers':
                return User::where('type', 'simple_user')->get();
            case 'staff':
                return User::where('staff_type', 'staff')->get();
            case 'individual':
                return User::whereIn('id', $this->target_users ?? [])->get();
          case 'package_subscribers':
    if ($this->package_id) {
        $users = User::whereHas('packages', function ($q) {
            $q->where('packages.id', $this->package_id)
            ->where('package_purchases.status', 'approved');
        })
        ->whereNotNull('fcm_token') // ✅ Add this
        ->get();

        \Log::info('Found package subscribers', [
            'package_id' => $this->package_id,
            'user_ids' => $users->pluck('id')->toArray(),
        ]);

        return $users;
    }

    \Log::warning('No package_id set in notification', [
        'notification_id' => $this->id
    ]);

    return collect();


            default:
                return collect();
        }
    }

    // public function getTargetUsers()
    // {
    //     switch ($this->send_to) {
    //         case 'all':
    //             return User::all();
    //         case 'subscribers':
    //             return User::where('type', 'subscriber')->get();
    //         case 'value_subscribers':
    //             if ($this->value_id) {
    //                 return User::whereHas('subscribedValues', function($q){
    //                     $q->where('values.id', $this->value_id);
    //                 })->get();
    //             }
    //             return collect();
    //         case 'non_subscribers':
    //             return User::where('type', 'simple_user')->get();
    //         case 'staff':
    //             return User::where('staff_type', 'staff')->get();
    //         case 'individual':
    //             return User::whereIn('id', $this->target_users ?? [])->get();
    //         default:
    //             return collect();
    //     }
    // }

    /**
     * Get formatted send_to text
     */
     public function getSendToTextAttribute()
    {
        return match($this->send_to) {
            'all' => 'All Users',
            'subscribers' => 'Subscribers Only',
            'non_subscribers' => 'Non-Subscribers Only',
            'staff' => 'Staff Only',
            'individual' => 'Selected Users',
            'value_subscribers' => 'Value Subscribers',
            'package_subscribers' => 'Package Subscribers',  // Add this
            default => 'Unknown'
        };
    }

    // public function getSendToTextAttribute()
    // {
    //     return match($this->send_to) {
    //         'all' => 'All Users',
    //         'subscribers' => 'Subscribers Only',
    //         'non_subscribers' => 'Non-Subscribers Only',
    //         'staff' => 'Staff Only',
    //         'individual' => 'Selected Users',
    //         default => 'Unknown'
    //     };
    // }
}
