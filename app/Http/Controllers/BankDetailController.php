<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller  
{
    public function index(Request $request)
    {
        $query = BankDetail::query();

        if ($search = $request->query('query')) {
            $query->where('bank_name', 'like', "%{$search}%")
                ->orWhere('account_title', 'like', "%{$search}%")
                ->orWhere('iban', 'like', "%{$search}%");
        }

        $perPage = $request->query('perPage', 10);
        $banks = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.bank_details.partials.table-body', compact('banks'))->render();
        }

        return view('admin.bank_details.list', compact('banks'));
    }


    public function create()
    {
        return view('admin.bank_details.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_title' => 'required|string|max:255',
            'account_number' => 'required',
            'iban' => 'required|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // If active → deactivate others
        // if ($request->is_active) {
        //     BankDetail::query()->update(['is_active' => false]);
        // }

        BankDetail::create($validated);

        return redirect()->route('bankDetails.index')->with('success', 'Bank details added successfully!');
    }
    public function edit($id)
    {
        $bank = BankDetail::findOrFail($id);
        return view('admin.bank_details.edit', compact('bank'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_title' => 'required|string|max:255',
            'account_number' => 'required',
            'iban' => 'nullable|string|max:255',
            'branch_code' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',


        ]);

        $bank = BankDetail::findOrFail($id);
        $bank->update($request->all());

        return redirect()->route('bankDetails.index')->with('success', 'Bank details updated successfully!');
    }


  public function activate($id)
{
    $bank = BankDetail::findOrFail($id);

    // Toggle active status instead of forcing only one active
    $bank->update(['is_active' => !$bank->is_active]);

    return back()->with('success', 'Bank activation status updated.');
}


    public function destroy($id)
    {
        BankDetail::findOrFail($id)->delete();
        return back()->with('success', 'Bank detail deleted successfully!');
    }
    public function showBuyPackages()
    {
        $bank = \App\Models\BankDetail::latest()->first(); // get the most recent or default bank record
        return view('webview.packages.buy', compact('bank'));
    }
}
