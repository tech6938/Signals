@forelse($packages as $package)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $package->name }}</td>
    <td>${{ rtrim(rtrim(number_format($package->price, 2, '.', ''), '0'), '.') }}</td>
    <td>{{ rtrim(rtrim(number_format($package->lyd_price, 2, '.', ''), '0'), '.') }}</td>
    <td>{{ $package->duration_days }}</td>
    <td>{{ $package->signal_limit ?? 'Unlimited' }}</td>
    <td>{{ ucfirst($package->status) }}</td>
    <td class="no-print">
        <a href="{{ route('packages.edit', $package->id) }}">
            <i class="fa fa-pencil" style="color:#007BFF;"></i>
        </a> |
        <form action="{{ route('packages.destroy', $package->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure?')" style="border:none;background:none;padding:0;">
                <i class="fa fa-trash-o" style="color:red;"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="7">No records found.</td>
</tr>
@endforelse
