@forelse($userMessages as $msg)
<tr>
  <td>{{ method_exists($userMessages, 'firstItem') ? $userMessages->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>{{ $msg->title }}</td>
  <td>{{ Str::limit($msg->description, 30) }}</td>
  <td>{{ $msg->status ? 'Active' : 'Inactive' }}</td>

  <td>
    @php
        // Check and set date value
        if (!empty($msg->date) && $msg->date !== '0000-00-00') {
            try {
                $dateValue = \Carbon\Carbon::parse($msg->date)->format('Y-m-d');
            } catch (\Exception $e) {
                $dateValue = '-';
            }
        } else {
            $dateValue = '-';
        }
    @endphp
    {{ $dateValue }}
</td>

<td>
    @php
        // Check and set time value
        if (!empty($msg->time) && $msg->time !== '00:00:00') {
            try {
                $timeValue = \Carbon\Carbon::createFromFormat('H:i:s', $msg->time)->format('g:i A');
            } catch (\Exception $e) {
                $timeValue = '-';
            }
        } else {
            $timeValue = '-';
        }
    @endphp
    {{ $timeValue }}
</td>


  <td class="no-print">
    <!--<a href="{{ route('userMessages.edit', $msg->id) }}">-->
    <!--  <i class="fa fa-pencil" style="color:#007BFF;"></i>-->
    <!--</a> |-->
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
  <td colspan="7" class="text-center">No records found.</td>
</tr>
@endforelse
