<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'lyd_price',
        'duration_days',
        'signal_limit',
        'status',
    ];

    /**
     * Boot method to handle model events.
     */
     public function subscribers()
{
    return $this->belongsToMany(User::class, 'user_value', 'package_id', 'user_id')
        ->withTimestamps();
}

public function invoices()
{
    return $this->hasMany(\App\Models\Invoice::class, 'package_id');
}

   
}
