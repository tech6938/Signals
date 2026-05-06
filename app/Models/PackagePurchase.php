<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePurchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','package_id','invoice_id','screenshot','status'];

    public function user()    { return $this->belongsTo(User::class); }
     public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    
  public function invoice()
  {
    return $this->belongsTo(Invoice::class, 'user_id', 'user_id');
  }
}
