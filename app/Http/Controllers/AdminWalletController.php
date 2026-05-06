<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

class AdminWalletController extends Controller
{
    public function index(Request $request)
    {
        // 🧩 Base query: group invoices by user to show totals
        $query = Invoice::query()
            ->join('users', 'invoices.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.f_name as user_fname',
                'users.last_name as user_lname',
                'users.email as user_email',
                'users.phone as user_phone',
                'users.type as user_type',
                DB::raw('SUM(CASE WHEN LOWER(invoices.currency) = "lyd" THEN invoices.amount ELSE 0 END) as total_lyd'),
                DB::raw('SUM(CASE WHEN LOWER(invoices.currency) = "usd" THEN invoices.amount ELSE 0 END) as total_usd'),
                DB::raw('COUNT(invoices.id) as total_packages'),
                DB::raw('MIN(invoices.start_date) as first_purchase'),
                DB::raw('MAX(invoices.end_date) as last_expiry')
            )
            ->groupBy('users.id', 'users.f_name', 'users.last_name', 'users.email', 'users.phone', 'users.type');

        // 🔹 Filter by date range (optional)
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('invoices.created_at', [$request->start_date, $request->end_date]);
        }

        // 🔹 Filter for active subscriptions only
        if ($request->active_only) {
            $query->whereDate('invoices.end_date', '>=', now());
        }

        // 📄 Paginate results for table
        $users = $query->paginate($request->get('perPage', 10));

        // --- 🔹 Totals Section ---
        $baseQuery = Invoice::join('users', 'invoices.user_id', '=', 'users.id')
            ->whereIn('users.type', ['subscriber', 'simple_user']);

        // Apply same filters to totals
        if ($request->filled(['start_date', 'end_date'])) {
            $baseQuery->whereBetween('invoices.created_at', [$request->start_date, $request->end_date]);
        }
        if ($request->active_only) {
            $baseQuery->whereDate('invoices.end_date', '>=', now());
        }

        // 💰 Total LYD
        $totalLYD = (clone $baseQuery)
            ->where('invoices.currency', 'lyd')
            ->sum('invoices.amount');

        // 💵 Total USD
        $totalUSD = (clone $baseQuery)
            ->where('invoices.currency', 'usd')
            ->sum('invoices.amount');

        // 👥 Total subscribers
        $totalSubscribers = User::where('type', 'subscriber')->count();

        // 📊 Average income per plan
        $averagePerPlan = (clone $baseQuery)
            ->groupBy('invoices.package_id')
            ->select('invoices.package_id', DB::raw('AVG(invoices.amount) as avg_amount'))
            ->get()
            ->mapWithKeys(function ($item) {
                $package = Package::find($item->package_id);
                return [$package->name ?? 'Unknown Plan' => $item->avg_amount];
            });

        // 🧾 Return to view
        return view('admin.wallet.index', compact(
            'users',
            'totalLYD',
            'totalUSD',
            'totalSubscribers',
            'averagePerPlan'
        ));
    }

    public function userDetail($userId)
    {
        $user = User::findOrFail($userId);
        
        $invoices = Invoice::where('user_id', $userId)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals for this user
        $totalLYD = $invoices->where('currency', 'lyd')->sum('amount');
        $totalUSD = $invoices->where('currency', 'usd')->sum('amount');
        $totalPackages = $invoices->count();

        return view('admin.wallet.user-detail', compact(
            'user',
            'invoices',
            'totalLYD',
            'totalUSD',
            'totalPackages'
        ));
    }
}
