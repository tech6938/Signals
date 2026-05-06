@forelse($notifications as $notification)
<tr>
    <td>{{ $notification->id }}</td>
    <td>{{ Str::limit($notification->title, 30) }}</td>
    <td>{{ Str::limit($notification->description, 50) }}</td>
    <td>
        <span class="label label-info">{{ $notification->send_to_text }}</span>
    </td>
    <!--<td>-->
    <!--    @if($notification->sent)-->
    <!--        <span class="label label-success">Sent</span>-->
    <!--    @else-->
    <!--        <span class="label label-warning">Pending</span>-->
    <!--    @endif-->
    <!--</td>-->
    <td>{{ $notification->sent_count }}</td>
    <td>
        @if($notification->sent_at)
            {{ $notification->sent_at->format('M d, Y H:i') }}
        @else
            -
        @endif
    </td>
    <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal{{ $notification->id }}">
                <i class="fa fa-eye"></i>
            </button>
            <!-- @if(!$notification->sent)
                <a href="{{ route('notifications.resend', $notification) }}" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to resend this notification?')">
                    <i class="fa fa-paper-plane"></i>
                </a>
            @endif --> |
            <form action="{{ route('notifications.destroy', $notification) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>

        <!-- View Modal -->
        <div class="modal fade" id="viewModal{{ $notification->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Notification Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Title:</strong></label>
                            <p>{{ $notification->title }}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Description:</strong></label>
                            <p>{{ $notification->description }}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Send To:</strong></label>
                            <p>{{ $notification->send_to_text }}</p>
                        </div>
                        @if($notification->send_to === 'individual' && $notification->target_users)
                            <div class="form-group">
                                <label><strong>Target Users:</strong></label>
                                <p>
                                    @php
                                        $users = \App\Models\User::whereIn('id', $notification->target_users)->get();
                                    @endphp
                                    @foreach($users as $user)
                                        <span class="label label-default">{{ $user->f_name }} {{ $user->last_name }}</span>
                                    @endforeach
                                </p>
                            </div>
                        @endif
                        <div class="form-group">
                            <label><strong>Status:</strong></label>
                            <p>
                                @if($notification->sent)
                                    <span class="label label-success">Sent</span>
                                @else
                                    <span class="label label-warning">Pending</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label><strong>Sent Count:</strong></label>
                            <p>{{ $notification->sent_count }}</p>
                        </div>
                        @if($notification->sent_at)
                            <div class="form-group">
                                <label><strong>Sent At:</strong></label>
                                <p>{{ $notification->sent_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center">No notifications found.</td>
</tr>
@endforelse
