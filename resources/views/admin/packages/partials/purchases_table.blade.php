@forelse($purchases as $purchase)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{ $purchase->user->f_name  }}</td>
  <td>{{ $purchase->user->email }}</td>
  <td>{{ $purchase->package->name }}</td>
  <td>${{ $purchase->package->price }}</td>
  <td>
    @if($purchase->screenshot)
      <a href="{{ asset('storage/'.$purchase->screenshot) }}" target="_blank">View</a>
    @else
      -
    @endif
  </td>
  <td>{{ ucfirst($purchase->status) }}</td>
  <td>
    <form action="{{ route('admin.package.purchases.updateStatus', $purchase->id) }}" method="POST">
      @csrf
      <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="pending" {{ $purchase->status=='pending'?'selected':'' }}>Pending</option>
        <option value="approved" {{ $purchase->status=='approved'?'selected':'' }}>Approved</option>
        <option value="rejected" {{ $purchase->status=='rejected'?'selected':'' }}>Rejected</option>
      </select>
    </form>
  </td>
</tr>
@empty
<tr>
  <td colspan="8">{{ __('messages.no_records_found') }}</td>
</tr>
@endforelse
<tr>
  <td colspan="8" class="text-end">
    {{ $purchases->links() }}
  </td>
</tr>
