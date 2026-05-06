<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\UserNotification;
use App\Models\Invoice;

// ✅ Import Sanctum trait
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // ✅ Use it here
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'f_name',
        'last_name',
        'email',
        'password',
        'country',
        'staff_type',
        'phone',
        'fcm_token',
        'admin_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function subscribedValues()
    {
        return $this->belongsToMany(Value::class, 'user_value', 'user_id', 'value_id')->withTimestamps();
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }
    // Make sure this matches your actual table name
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_purchases', 'user_id', 'package_id')
            ->withPivot('status', 'screenshot')
            ->withTimestamps();
    }


    public function subscribedPackages()
    {
        return $this->belongsToMany(Package::class, 'user_value', 'user_id', 'package_id')
            ->withTimestamps();
    }
    public function valueSubscriptions()
{
    return $this->hasMany(\App\Models\ValueSubscription::class, 'user_id', 'id');
}
 public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function purchasedPackages()
    {
        return $this->hasManyThrough(Package::class, Invoice::class, 'user_id', 'id', 'id', 'package_id');
    }
    // App/Models/User.php

public function packagePurchases()
    {
        return $this->hasMany(PackagePurchase::class);
    }
}
