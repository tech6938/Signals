<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userMessages extends Model
{
    protected $fillable = ['title', 'description', 'status', 'image_path','notification_type', 'target_user_ids', 'package_id', 'time',
        'date',];
    protected $table =  'usermessages';
    protected $casts = [
    'target_user_ids' => 'array',
];

    
}
