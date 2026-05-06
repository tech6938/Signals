@forelse($newsList as $news)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{ \Illuminate\Support\Str::words($news->title, 6, '') }}</td>
  <td>{{ Str::limit($news->description, 20, '') }}</td>
  <td>
    @if($news->url)
    <a href="{{ $news->url }}" target="_blank">{{ __('messages.link') }}</a>
    @endif
  </td>
  <td>
    @if($news->image)
    <img src="{{ asset('storage/' . $news->image) }}" width="50px" alt="News Image">
    @endif
  </td>
  <td>{{ $news->status ? 'Active' : 'Inactive' }}</td>
  <td>
    @php
        // Check and set date value
        if (!empty($news->date) && $news->date !== '0000-00-00') {
            try {
                $dateValue = \Carbon\Carbon::parse($news->date)->format('Y-m-d');
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
        if (!empty($news->time) && $news->time !== '00:00:00') {
            try {
                $timeValue = \Carbon\Carbon::createFromFormat('H:i:s', $news->time)->format('g:i A');
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
    <a href="{{ route('news.edit', $news->id) }}">
      <i class="fa fa-pencil" style="color:#007BFF;"></i>
    </a> |
    <form action="{{ route('news.destroy', $news->id) }}" method="POST" style="display:inline;">
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
  <td colspan="9" class="text-center">{{ __('messages.no_records_found') }}</td>
</tr>
@endforelse
