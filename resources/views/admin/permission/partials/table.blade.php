@forelse($permissions as $permission)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $permission->name }}</td>
    <td>{{ $permission->guard_name }}</td>
    <td class="no-print">
        <a href="{{ route('permissions.edit', $permission->id) }}">
            <i class="fa fa-pencil" style="color:#007BFF;"></i>
        </a> |
        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;">
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
    <td colspan="4">No records found.</td>
</tr>
@endforelse

<div class="d-flex justify-content-end" id="paginationLinks">
    {{ $permissions->links() }}
</div>
