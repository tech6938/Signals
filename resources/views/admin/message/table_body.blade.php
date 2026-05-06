@forelse($userMessages as $msg)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{ $msg->title }}</td>
  <td>{{ Str::limit($msg->description, 30) }}</td>
  <td>{{ $msg->status ? 'Active' : 'Inactive' }}</td>
  <td class="no-print">
    <a href="{{ route('userMessages.edit', $msg->id) }}">
      <i class="fa fa-pencil" style="color:#007BFF;"></i>
    </a> |
    <form action="{{ route('userMessages.destroy', $msg->id) }}" method="POST" style="display:inline;">
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
  <td colspan="5">No records found.</td>
</tr>
@endforelse
