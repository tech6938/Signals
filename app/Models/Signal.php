<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'coin_name',
        'b_price',
        'tp1', 'tp2', 'tp3', 'tp4',
        'icon1', 'icon2',
        'last_price',
        'status'
    ];
}
