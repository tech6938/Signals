@extends('admin.includes.layout')


@section('content')
<style>
  .chat-box { height: 400px; overflow-y: auto; background: #f9f9f9; padding: 15px; }
  .chat-message { display: flex; margin-bottom: 15px; align-items: flex-end; }
  .chat-message.admin { justify-content: flex-start; }
  .chat-message.user { justify-content: flex-end; }
  .chat-bubble { max-width: 70%; padding: 10px; border-radius: 10px; font-size: 14px; }
  .chat-bubble.admin { background: #d1f7c4; text-align: left; }
  .chat-bubble.user { background: #e4e6eb; text-align: right; }
  .chat-time { font-size: 11px; color: #666; margin-top: 5px; }
  .input-with-attach { position: relative; }
  .input-with-attach .attach-inside { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #555; }
  .input-with-attach .form-control { padding-right: 38px; }
  
  /* User Information Panel Styles */
  .user-info-card { padding: 10px; }
  .user-details { margin-top: 15px; }
  .detail-item { 
    display: flex; 
    align-items: center; 
    margin-bottom: 12px; 
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  .detail-item:last-child { border-bottom: none; }
  .detail-item i { 
    width: 20px; 
    margin-right: 10px; 
    font-size: 14px;
  }
  .detail-item strong { 
    margin-right: 8px; 
    min-width: 80px;
    font-size: 13px;
  }
  .detail-item span { 
    flex: 1; 
    font-size: 13px;
  }
  .user-type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
  }
  .user-type-badge.subscriber {
    background-color: #d4edda;
    color: #155724;
  }
  .user-type-badge.simple_user {
    background-color: #cce5ff;
    color: #004085;
  }
  .status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
  }
  .status-badge.active {
    background-color: #d4edda;
    color: #155724;
  }
  .status-badge.inactive {
    background-color: #f8d7da;
    color: #721c24;
  }
  .status-badge.block {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  /* Chat Message User Info Styles */
  .user-info-header {
    margin-bottom: 8px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255,255,255,0.3);
  }
  .user-details-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
  }
  .user-details-inline span {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    color: #333;
  }
  .user-details-inline i {
    margin-right: 3px;
    font-size: 10px;
  }
  .user-type-badge.subscriber {
    background-color: rgba(212, 237, 218, 0.8) !important;
    color: #155724 !important;
  }
  .user-type-badge.simple_user {
    background-color: rgba(204, 229, 255, 0.8) !important;
    color: #004085 !important;
  }
  .message-content {
    margin-top: 5px;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .col-sm-3 { margin-bottom: 20px; }
    .detail-item { flex-direction: column; align-items: flex-start; }
    .detail-item strong { margin-bottom: 5px; }
    .user-details-inline {
      flex-direction: column;
      gap: 4px;
    }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-comments"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.chat_with') }} {{ $user->f_name }}</h1>
      <small>{{ __('messages.conversation_history') }}</small>
    </div>
    <div class="header-action">
      <button class="btn btn-info" data-toggle="modal" data-target="#userDetailsModal" onclick="populateUserModal()">
        <i class="fa fa-eye"></i> {{ __('messages.view_details') }}
      </button>
      <a href="{{ route('admin.chat.start_new') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> {{ __('messages.start_new_chat') }}
      </a>
      <a href="{{ route('admin.chat.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> {{ __('messages.back_to_chats') }}
      </a>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <!-- User Information Panel -->
      <div class="col-sm-3">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <h4 class="panel-title">
              <i class="fa fa-user"></i> {{ __('messages.user_information') }}
            </h4>
          </div>
          <div class="panel-body">
            <div class="user-info-card">
              <div class="text-center" style="margin-bottom: 20px;">
                <div class="user-avatar" style="width: 80px; height: 80px; background: #007bff; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">
                  {{ strtoupper(substr($user->f_name, 0, 1)) }}
                </div>
                <h4 style="margin: 0;">{{ $user->f_name }} {{ $user->last_name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
              </div>
              
              <div class="user-details">
                <div class="detail-item">
                  <i class="fa fa-phone text-primary"></i>
                  <strong>{{ __('messages.phone') }}:</strong>
                  <span>{{ $user->phone ?? __('messages.not_provided') }}</span>
                </div>
                
                
                
                
                <div class="detail-item">
                  <i class="fa fa-tag text-warning"></i>
                  <strong>{{ __('messages.user_type') }}:</strong>
                  <span class="user-type-badge {{ $user->type }}">
                    {{ ucfirst(str_replace('_', ' ', $user->type)) }}
                  </span>
                </div>
                
          <div class="detail-item" style="cursor: pointer;" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')">
  <i class="fa fa-circle text-{{ $user->status === 'active' ? 'success' : 'danger' }}"></i>
  <strong>{{ __('messages.status') }}:</strong>
  <span class="status-badge {{ $user->status }}">
    {{ ucfirst($user->status) }}
  </span>
</div>

                
                <div class="detail-item">
  <i class="fa fa-box text-info"></i>
  <strong>{{ __('messages.plan') }}:</strong>
  <span class="status-badge bg-info">
    {{ $latestPackagePurchase && $latestPackagePurchase->package ? $latestPackagePurchase->package->name : __('messages.no_package') }}
  </span>
</div>
<!--@if ($latestInvoice)-->
<!--  <div class="detail-item">-->
<!--    <i class="fa fa-play text-success"></i>-->
<!--    <strong>{{ __('messages.start_date') }}:</strong>-->
<!--    <span>{{ $latestInvoice->start_date->format('d M Y') }}</span>-->
<!--  </div>-->

<!--  <div class="detail-item">-->
<!--    <i class="fa fa-stop text-danger"></i>-->
<!--    <strong>{{ __('messages.end_date') }}:</strong>-->
<!--    <span>{{ $latestInvoice->end_date->format('d M Y') }}</span>-->
<!--  </div>-->
<!--@else-->
<!--  <div class="detail-item">-->
<!--    <i class="fa fa-calendar-times text-muted"></i>-->
<!--    <strong>{{ __('messages.subscription') }}:</strong>-->
<!--    <span>{{ __('messages.no_subscription') }}</span>-->
<!--  </div>-->
<!--@endif-->

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Panel -->
      <div class="col-sm-9">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <h4 class="panel-title">
              <i class="fa fa-comments"></i> {{ __('messages.conversation') }}
              <small class="text-muted">- {{ $user->f_name }}</small>
            </h4>
          </div>

          {{-- Chat messages --}}
          <div id="chat-box" class="panel-body chat-box">
            @include('admin.chat.messages')
          </div>

          {{-- Reply form --}}
          <div class="panel-footer">
            <form id="chatForm" method="POST" action="{{ route('admin.chat.reply', $user->id) }}" enctype="multipart/form-data">
              @csrf
              <div class="row" style="align-items:center;">
                <div class="col-sm-10 col-xs-12" style="margin-bottom:8px;">
                  <div class="input-with-attach">
                    <input type="text" name="message" id="messageInput" class="form-control" placeholder="{{ __('messages.type_your_reply') }}">
                    <label for="chatImageInput" class="attach-inside" title="{{ __('messages.attach_image') }}">
                      <i class="fa fa-paperclip"></i>
                    </label>
                    <input id="chatImageInput" type="file" name="image" accept="image/*" style="display:none;">
                  </div>
                  <small id="selectedFileName" class="text-muted"></small>
                </div>
                <div class="col-sm-2 col-xs-12" style="text-align:right;">
                  <button class="btn btn-success btn-block" type="submit">{{ __('messages.send') }}</button>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userDetailsModalLabel">
          <i class="fa fa-user"></i> {{ __('messages.user_details') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-4 text-center">
            <div class="user-avatar" style="width: 100px; height: 100px; background: #007bff; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: bold;">
              <span id="modal_user_avatar">{{ strtoupper(substr($user->f_name, 0, 1)) }}</span>
            </div>
            <h4 id="modal_user_name">{{ $user->f_name }} {{ $user->last_name }}</h4>
            <p class="text-muted" id="modal_user_email">{{ $user->email }}</p>
          </div>
          <div class="col-sm-8">
            <table class="table table-bordered">
              <tr>
                <th style="width: 30%">{{ __('messages.phone') }}</th>
                <td id="modal_user_phone">{{ $user->phone ?? __('messages.not_provided') }}</td>
              </tr>
              <tr>
                <th>{{ __('messages.user_type') }}</th>
                <td>
                  <span class="user-type-badge {{ $user->type }}">
                    {{ ucfirst(str_replace('_', ' ', $user->type)) }}
                  </span>
                </td>
              </tr>
              <tr>
                <th>{{ __('messages.subscription') }}</th>
                <td id="modal_user_subscription">
                  @php
                    $approvedPackages = $user->packages()->where('package_purchases.status', 'approved')->get();
                  @endphp
                  @if($approvedPackages->count() > 0)
                    @foreach($approvedPackages as $package)
                      <span class="label label-info">{{ $package->name }}</span>
                      @if(!$loop->last)<br>@endif
                    @endforeach
                  @else
                    -
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('messages.status') }}</th>
                <td>
                  <span class="status-badge {{ $user->status }}">
                    {{ ucfirst($user->status) }}
                  </span>
                </td>
              </tr>
              @if ($latestInvoice)
              <tr>
                <th>{{ __('messages.start_date') }}</th>
                <td id="modal_start_date">{{ $latestInvoice->start_date->format('d M Y') }}</td>
              </tr>
              <tr>
                <th>{{ __('messages.end_date') }}</th>
                <td id="modal_end_date">{{ $latestInvoice->end_date->format('d M Y') }}</td>
              </tr>
              @else
              <tr>
                <th>{{ __('messages.subscription') }}</th>
                <td>{{ __('messages.no_subscription') }}</td>
              </tr>
              @endif
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
        <button type="button" class="btn btn-primary" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')">
          <i class="fa fa-toggle-on"></i> {{ __('messages.toggle_status') }}
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  var chatBox = document.getElementById("chat-box");
  chatBox.scrollTop = chatBox.scrollHeight;

  // Reflect chosen file name
  $('#chatImageInput').on('change', function(){
    var name = this.files && this.files.length ? this.files[0].name : '';
    $('#selectedFileName').text(name);
  });

  // Send message via AJAX with FormData (supports image)
  $('#chatForm').on('submit', function(e) {
    e.preventDefault();

    var form = $(this)[0];
    var url = $(this).attr('action');
    var formData = new FormData(form);

    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function() {
        $('#messageInput').val('');
        $('#chatImageInput').val('');
        $('#selectedFileName').text('');
        refreshMessages();
      },
      error: function(xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Failed to send message";
        alert("Error: " + msg);
      }
    });
  });

  // Reload only messages
  function refreshMessages() {
    $.get("{{ route('admin.chat.show', $user->id) }}?ajax=1", function(data) {
      $('#chat-box').html(data);
      chatBox.scrollTop = chatBox.scrollHeight;
    });
  }

  // Auto-refresh every 5s
  setInterval(refreshMessages, 5000);
});

function populateUserModal() {
  // The modal is already populated with server-side data
  // This function is called when the modal is opened
  console.log('User details modal opened');
}

function toggleUserStatus(userId, currentStatus) {
  const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
  const token = '{{ csrf_token() }}';

  if (!confirm(`Are you sure you want to change status to ${newStatus}?`)) return;

  fetch(`/users/${userId}/update-status`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({
      status: newStatus
    })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload(); // Refresh to reflect status change
      } else {
        alert('Something went wrong.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error updating status.');
    });
}

</script>
@endsection
