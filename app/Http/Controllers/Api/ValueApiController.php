<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Value;
use Illuminate\Http\Request;

class ValueApiController extends Controller
{
    /**
     * Get all active values
     */
//     public function active()
// {
//     $values = Value::where('status', 1)->get();

//     // Keep values exactly as in DB (no trimming or rounding)
//     foreach ($values as $item) {
//         foreach (['h_value', 'l_value', 'b_price', 's_price'] as $field) {
//             // Just make sure it's a string so JSON keeps the exact format
//             $item->$field = round((float)$item->$field, 2);
//         }
//     }
    
//     // \Log::info($values);

//     return response()->json([
//         'success' => true,
//         'count'   => $values->count(),
//         'data'    => $values
//     ]);
// }

public function active()
{
    $values = Value::where('status', 1)->get();

    $data = $values->map(function ($item) {
        return [
            'id'         => $item->id,
            'coin_name'  => $item->coin_name,
            'h_value'    => round((float)$item->h_value, 2),
            'l_value'    => round((float)$item->l_value, 2),
            'b_price'    => round((float)$item->b_price, 2),
            's_price'    => round((float)$item->s_price, 2),
            'status'     => $item->status,
            'created_at' => \Carbon\Carbon::parse($item->getRawOriginal('created_at'))
                                ->setTimezone(config('app.timezone'))
                                ->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon\Carbon::parse($item->getRawOriginal('updated_at'))
                                ->setTimezone(config('app.timezone'))
                                ->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'count'   => $data->count(),
        'data'    => $data
    ]);
}

}
