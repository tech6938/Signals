<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'bank_name', 'account_title','account_number', 'iban', 'swift_code', 'is_active', 'description'
    ];
}
