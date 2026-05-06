@forelse($banks as $bank)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{ $bank->bank_name }}</td>
  <td>{{ $bank->account_title }}</td>
  <td>{{ $bank->iban }}</td>
  <td>{{ $bank->swift_code ?? 'N/A' }}</td>
  <td>
    @if($bank->is_active)
      <span class="badge badge-success">Active</span>
    @else
      <span class="badge badge-secondary">Inactive</span>
    @endif
  </td>
  <td class="no-print">
    @if(!$bank->is_active)
      <a href="{{ route('admin.bank-details.activate', $bank->id) }}"
        class="btn btn-sm btn-success"
        onclick="return confirm('Set this bank as active?')">
        <i class="fa fa-check"></i>
      </a>
    @else
      <button class="btn btn-sm btn-secondary" disabled>
        <i class="fa fa-check"></i>
      </button>
    @endif
    
    <a href="{{ route('admin.bank-details.edit', $bank->id) }}" class="btn btn-sm btn-primary">
      <i class="fa fa-pencil"></i>
    </a>

    <form action="{{ route('admin.bank-details.destroy', $bank->id) }}" method="POST" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to delete this bank detail?')">
        <i class="fa fa-trash-o"></i>
      </button>
    </form>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center">No bank details found.</td>
</tr>
@endforelse
