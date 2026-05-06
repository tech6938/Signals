<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Value;

class ValuesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','permission:manageValues']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->get('query', '');
        $perPage = $request->get('perPage', 10);

        $values = Value::when($query, function ($q) use ($query) {
            $q->where('coin_name', 'like', "%$query%")
                ->orWhere('h_value', 'like', "%$query%")
                ->orWhere('l_value', 'like', "%$query%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage]);

        if ($request->ajax()) {
            return view('admin.values.list', compact('values'))
                ->renderSections()['tableBody'] ?? '';
        }

        return view('admin.values.list', compact('values'));
    }

    public function create()
    {
        return view('admin.values.create');
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'coin_name' => 'required|string|max:191',
    //         'h_value'   => 'required|numeric',
    //         'l_value'   => 'required|numeric',
    //         'b_price'   => 'required|numeric',
    //         's_price'   => 'required|numeric',
    //     ]);

    //     Value::create($request->only(['coin_name', 'h_value', 'l_value','b_price','s_price', 'status']));

    //     return redirect()->route('values.index')->with('success', 'Value added successfully.');
    // }
    
    public function store(Request $request)
{
    $request->validate([
        'coin_name' => 'required|string|max:191',
        'h_value'   => 'required|numeric',
        'l_value'   => 'required|numeric',
        'b_price'   => 'required|numeric',
        's_price'   => 'required|numeric',
    ]);

    $now = now(); // uses APP_TIMEZONE = Africa/Tripoli

    // Preview what will be stored — check this with dd() first
    // dd([
    //     'now()         ' => $now->toDateTimeString(),
    //     'timezone'       => $now->timezoneName,
    //     'utc_equivalent' => $now->utcOffset(),
    // ]);

    Value::create(array_merge(
        $request->only(['coin_name', 'h_value', 'l_value', 'b_price', 's_price', 'status']),
        [
            'created_at' => $now,
            'updated_at' => $now,
        ]
    ));

    return redirect()->route('values.index')->with('success', 'Value added successfully.');
}


    public function edit($id)
    {
        $value = Value::findOrFail($id);

        return view('admin.values.edit', compact('value'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'coin_name' => 'required|string|max:191',
    //         'h_value'   => 'required|numeric',
    //         'l_value'   => 'required|numeric',
    //         'b_price'   => 'required|numeric',
    //         's_price'   => 'required|numeric',
    //     ]);

    //     $value = Value::findOrFail($id);
    //     $value->update($request->only(['coin_name', 'h_value', 'l_value','b_price','s_price', 'status']));

    //     return redirect()->route('values.index')->with('success', 'Value updated successfully.');
    // }
    
    public function update(Request $request, $id)
{
    $request->validate([
        'coin_name' => 'required|string|max:191',
        'h_value'   => 'required|numeric',
        'l_value'   => 'required|numeric',
        'b_price'   => 'required|numeric',
        's_price'   => 'required|numeric',
    ]);

    $value = Value::findOrFail($id);
    
    $value->update(array_merge(
        $request->only(['coin_name', 'h_value', 'l_value', 'b_price', 's_price', 'status']),
        ['updated_at' => now()]  // manually set correct timezone time
    ));

    return redirect()->route('values.index')->with('success', 'Value updated successfully.');
}

    public function destroy($id)
    {
        $value = Value::findOrFail($id);
        $value->delete();

        return redirect()->route('values.index')->with('success', 'Value deleted successfully.');
    }
}
