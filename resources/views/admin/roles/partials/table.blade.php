@forelse($roles as $role)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $role->name }}</td>
    <td>{{ $role->guard_name }}</td>
    <td class="no-print">
        <a href="{{ route('roles.edit', $role->id) }}">
            <i class="fa fa-pencil" style="color:#007BFF;"></i>
        </a> |
        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
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
    {{ $roles->links() }}
</div>