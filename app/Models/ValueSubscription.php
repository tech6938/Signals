<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValueSubscription extends Model
{
  use HasFactory;

  protected $table = 'user_value';
protected $fillable = [
        'package_purchases_id',
        'invoice_id',
        'user_id',
        'package_id',
    ];
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id',  'id');
  }
  public function package()
  {
    return $this->belongsTo(Package::class, 'package_id',  'id');
  }
  public function invoice()
  {
    return $this->belongsTo(Invoice::class, 'user_id', 'user_id');
  }
  
  public function packagePurchase()
{
    return $this->belongsTo(\App\Models\PackagePurchase::class, 'package_purchases_id');
}

}
