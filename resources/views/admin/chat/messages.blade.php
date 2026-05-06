@forelse($messages as $message)
  @if($message->sender_id == auth()->id())
    <div class="chat-message admin">
      <div class="chat-bubble admin">
        <strong>You (Admin)</strong><br>
        {{ $message->message }}
        @if(!empty($message->image_path))
          <div style="margin-top:8px">
            <a href="{{ asset('storage/' . $message->image_path) }}" target="_blank">
              <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-width:200px;max-height:200px" />
            </a>
          </div>
        @endif
        <div class="chat-time">{{ $message->created_at->diffForHumans() }}</div>
      </div>
    </div>
  @else
    <div class="chat-message user">
      <div class="chat-bubble user">
        <div class="user-info-header">
          <strong>{{ $message->sender->f_name }} {{ $message->sender->last_name }}</strong>
          <div class="user-details-inline">
            <span class="user-phone">
              <i class="fa fa-phone"></i> {{ $message->sender->phone ?? __('messages.not_provided') }}
            </span>
            <span class="user-type-badge {{ $message->sender->type }}">
              <i class="fa fa-tag"></i> {{ ucfirst(str_replace('_', ' ', $message->sender->type)) }}
            </span>
          </div>
        </div>
        <div class="message-content">
          {{ $message->message }}
        </div>
        @if(!empty($message->image_path))
          <div style="margin-top:8px">
            <a href="{{ asset('storage/' . $message->image_path) }}" target="_blank">
              <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-width:200px;max-height:200px" />
            </a>
          </div>
        @endif
        <div class="chat-time">{{ $message->created_at->diffForHumans() }}</div>
      </div>
    </div>
  @endif
@empty
  <p class="text-center">{{ __('messages.no_messages_yet') }}</p>
@endforelse
