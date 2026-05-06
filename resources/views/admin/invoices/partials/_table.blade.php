@forelse($invoices as $invoice)
<tr>
    <td>{{ $invoice->id }}</td>
    <td>{{ $invoice->name ?? 'N/A' }}</td>
    <td>{{ $invoice->phone ?? 'N/A' }}</td>
    <td>{{ $invoice->duration ?? 'N/A' }}</td>
    <td>{{ isset($invoice->amount) ? number_format($invoice->amount, 2) : '0.00' }}</td>
    <td><a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-sm btn-info">View</a></td>
    <td>{{ $invoice->end_date?->format('Y-m-d') ?? 'N/A' }}</td>
    <td>
        @if($invoice->warning_notification_sent)
            <span class="badge badge-success">✓ Sent</span>
            <small>{{ $invoice->warning_notification_sent_at?->format('M d, Y') }}</small>
        @else
            <span class="badge badge-secondary">Not Sent</span>
        @endif
    </td>
    <td>
        @if($invoice->expiration_notification_sent)
            <span class="badge badge-success">✓ Sent</span>
            <small>{{ $invoice->expiration_notification_sent_at?->format('M d, Y') }}</small>
        @else
            <span class="badge badge-secondary">Not Sent</span>
        @endif
    </td>
    <td>{{ $invoice->created_at?->format('Y-m-d') ?? 'N/A' }}</td>
    <td class="no-print">
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                <i class="fa fa-trash-o"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="11">No invoices found.</td>
</tr>
@endforelse
