<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasFactory;

    protected $table = 'values';
    protected $fillable = [
        'coin_name',
        'h_value',
        'l_value',
        'b_price',
        's_price',
        'status',
    ];

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'user_value', 'value_id', 'user_id')->withTimestamps();
    }
}
