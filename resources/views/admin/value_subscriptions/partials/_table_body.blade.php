@foreach($subscriptions as $user)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $user->f_name }} {{ $user->last_name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @forelse($user->subscribedPackages as $package)
            <span class="badge bg-info text-dark">{{ $package->name }}</span>
        @empty
            <span class="text-muted">No subscriptions</span>
        @endforelse
    </td>
    <td class="no-print">
        @foreach($user->subscribedPackages as $package)
            <form action="{{ route('admin.value-subscriptions.destroy', [$user->id, $package->id]) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Remove subscription?')">
                    <i class="fa fa-trash"></i> {{ $package->name }}
                </button>
            </form>
        @endforeach
    </td>
</tr>
@endforeach

@if($subscriptions->isEmpty())
<tr>
    <td colspan="5" class="text-center">No data</td>
</tr>
@endif
