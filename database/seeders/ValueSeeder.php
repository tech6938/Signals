<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Value;

class ValueSeeder extends Seeder
{
    public function run(): void
    {
        Value::create([
            'coin_name' => 'BTC',
            'h_value' => 50000.00,
            'l_value' => 45000.00,
            'status' => 1,
        ]);

        Value::create([
            'coin_name' => 'ETH',
            'h_value' => 3500.00,
            'l_value' => 3200.00,
            'status' => 1,
        ]);
    }
}

