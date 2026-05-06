<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_notifications';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * Relationships
     */

    // Each record belongs to a notification
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    // Each record belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
