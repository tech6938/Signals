<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceApiController extends Controller
{
    /**
     * Return invoices for the authenticated user only.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) { $perPage = 10; }
        if ($perPage > 50) { $perPage = 50; }

        $paginator = Invoice::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        $data = $paginator->getCollection()->map(function (Invoice $invoice) {
            return [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'phone' => $invoice->phone,
                'service_type' => $invoice->service_type,
                'start_date' => $invoice->start_date,
                'end_date' => $invoice->end_date,
                'duration' => $invoice->duration,
                'amount' => (float) $invoice->amount,
                'pdf_url' => route('invoices.pdf', $invoice->id),
                'created_at' => optional($invoice->created_at)->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}


