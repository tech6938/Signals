<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signal;
use Illuminate\Support\Facades\Storage;


class SignalController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','permission:manageSignal']);
    }
    public function index(Request $request)
    {
        $query = $request->get('query', '');
        $perPage = $request->get('perPage', 10);

        $signals = Signal::when($query, function ($q) use ($query) {
            $q->where('coin_name', 'like', "%$query%")
                ->orWhere('b_price', 'like', "%$query%")
                ->orWhere('last_price', 'like', "%$query%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['query' => $query, 'perPage' => $perPage]);

        if ($request->ajax()) {
            return view('admin.signals.list', compact('signals'))
                ->renderSections()['tableBody'] ?? '';
        }

        return view('admin.signals.list', compact('signals'));
    }

    public function create()
    {
        return view('admin.signals.create');
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'coin_name' => 'required|string',
    //         'b_price' => 'required|numeric',
    //         'tp1' => 'nullable|numeric',
    //         'tp2' => 'nullable|numeric',
    //         'tp3' => 'nullable|numeric',
    //         'tp4' => 'nullable|numeric',
    //         'icon1' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
    //         'icon2' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
    //         'last_price' => 'nullable|numeric',
    //         'status' => 'required|boolean'
    //     ]);

    //     if ($request->hasFile('icon1')) {
    //         $data['icon1'] = $request->file('icon1')->store('uploads/icons', 'public');
    //     }

    //     if ($request->hasFile('icon2')) {
    //         $data['icon2'] = $request->file('icon2')->store('uploads/icons', 'public');
    //     }

    //     Signal::create($data);

    //     return redirect()->route('signals.index')->with('success', 'Signal added successfully');
    // }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'coin_name' => 'required|string',
            'b_price' => 'required|numeric',
            'tp1' => 'nullable|numeric',
            'tp2' => 'nullable|numeric',
            'tp3' => 'nullable|numeric',
            'tp4' => 'nullable|numeric',
            'icon1' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'icon2' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'last_price' => 'nullable|numeric',
            'status' => 'required|boolean'
        ]);
    
        if ($request->hasFile('icon1')) {
            $data['icon1'] = $request->file('icon1')->store('uploads/icons', 'public');
        }
    
        if ($request->hasFile('icon2')) {
            $data['icon2'] = $request->file('icon2')->store('uploads/icons', 'public');
        }
    
        $signal = \App\Models\Signal::create($data);
    
    // 🔔 Send notification ONLY if status = 1 (active)
    if ($signal->status == 1) {
        $tokens = \App\Models\User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        if (!empty($tokens)) {
            app(\App\Services\FcmService::class)->sendToTokens(
                $tokens,
                '📢 New Signal',
                $signal->coin_name . ' | Buy: ' . $signal->b_price,
                [
                    'type' => 'signal',
                    'signal_id' => (string) $signal->id,
                    'coin' => $signal->coin_name,
                ]
            );
        }
    }
    
    
        return redirect()->route('signals.index')->with('success', 'Signal added successfully');
    }



    public function edit(Signal $signal)
    {
        return view('admin.signals.edit', compact('signal'));
    }

    public function update(Request $request, Signal $signal)
    {
        $data = $request->validate([
            'coin_name' => 'required|string',
            'b_price' => 'required|numeric',
            'tp1' => 'nullable|numeric',
            'tp2' => 'nullable|numeric',
            'tp3' => 'nullable|numeric',
            'tp4' => 'nullable|numeric',
            'icon1' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'icon2' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'last_price' => 'nullable|numeric',
            'status' => 'required|boolean'
        ]);

        // Handle icon1
        if ($request->hasFile('icon1')) {
            // Delete old icon1 if exists
            if ($signal->icon1 && Storage::disk('public')->exists($signal->icon1)) {
                Storage::disk('public')->delete($signal->icon1);
            }

            // Store new icon1
            $data['icon1'] = $request->file('icon1')->store('uploads/icons', 'public');
        }

        // Handle icon2
        if ($request->hasFile('icon2')) {
            // Delete old icon2 if exists
            if ($signal->icon2 && Storage::disk('public')->exists($signal->icon2)) {
                Storage::disk('public')->delete($signal->icon2);
            }

            // Store new icon2
            $data['icon2'] = $request->file('icon2')->store('uploads/icons', 'public');
        }

        $signal->update($data);

        return redirect()->route('signals.index')->with('success', 'Signal updated successfully');
    }


    public function destroy(Signal $signal)
    {
        // Delete icon1 if it exists
        if ($signal->icon1 && Storage::disk('public')->exists($signal->icon1)) {
            Storage::disk('public')->delete($signal->icon1);
        }

        // Delete icon2 if it exists
        if ($signal->icon2 && Storage::disk('public')->exists($signal->icon2)) {
            Storage::disk('public')->delete($signal->icon2);
        }

        // Delete the signal record
        $signal->delete();

        return redirect()->route('signals.index')->with('success', 'Signal deleted successfully');
    }
}
