@extends('admin.includes.layout')

@section('content')
<style>
  .user-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
  }
  .user-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.15);
  }
  .user-card.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
  }
  .user-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .user-details h5 {
    margin: 0 0 5px 0;
    color: #333;
  }
  .user-details p {
    margin: 0;
    color: #666;
    font-size: 14px;
  }
  .user-type {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
  }
  .user-type.subscriber {
    background-color: #d4edda;
    color: #155724;
  }
  .user-type.simple_user {
    background-color: #cce5ff;
    color: #004085;
  }
  .message-form {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
  }
  .input-with-attach {
    position: relative;
  }
  .input-with-attach .attach-inside {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #555;
  }
  .input-with-attach .form-control {
    padding-right: 38px;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-plus-circle"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.start_new_chat') }}</h1>
      <small>{{ __('messages.select_user_to_chat') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li><a href="{{ route('admin.chat.index') }}">{{ __('messages.messages') }}</a></li>
        <li class="active">{{ __('messages.start_new_chat') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <h4 class="panel-title">{{ __('messages.select_user') }}</h4>
          </div>

          <div class="panel-body">
            @if($users->count() > 0)
              <div class="row">
                @foreach($users as $user)
                  <div class="col-md-6 col-lg-4">
                    <div class="user-card" data-user-id="{{ $user->id }}" data-user-name="{{ $user->f_name }}">
                      <div class="user-info">
                        <div class="user-details">
                          <h5>{{ $user->f_name }} {{ $user->last_name }}</h5>
                          <p><i class="fa fa-envelope"></i> {{ $user->email }}</p>
                          @if($user->phone)
                            <p><i class="fa fa-phone"></i> {{ $user->phone }}</p>
                          @endif
                        </div>
                        <div class="user-type {{ $user->type }}">
                          {{ ucfirst(str_replace('_', ' ', $user->type)) }}
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              <!-- Message Form (Hidden initially) -->
              <div class="message-form" id="messageForm" style="display: none;">
                <h4>{{ __('messages.send_message_to') }} <span id="selectedUserName"></span></h4>
                
                <form id="startChatForm" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-sm-10">
                      <div class="input-with-attach">
                        <input type="text" name="message" id="messageInput" class="form-control" 
                               placeholder="{{ __('messages.type_your_message') }}" required>
                        <label for="chatImageInput" class="attach-inside" title="{{ __('messages.attach_image') }}">
                          <i class="fa fa-paperclip"></i>
                        </label>
                        <input id="chatImageInput" type="file" name="image" accept="image/*" style="display:none;">
                      </div>
                      <small id="selectedFileName" class="text-muted"></small>
                    </div>
                    <div class="col-sm-2">
                      <button class="btn btn-success btn-block" type="submit">
                        <i class="fa fa-paper-plane"></i> {{ __('messages.send') }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            @else
              <div class="text-center">
                <i class="fa fa-users fa-3x text-muted"></i>
                <h4 class="text-muted">{{ __('messages.no_users_available') }}</h4>
                <p>{{ __('messages.no_users_to_chat') }}</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  let selectedUserId = null;
  let selectedUserName = null;

  // Handle user selection
  $('.user-card').on('click', function() {
    // Remove previous selection
    $('.user-card').removeClass('selected');
    
    // Add selection to clicked card
    $(this).addClass('selected');
    
    // Get user data
    selectedUserId = $(this).data('user-id');
    selectedUserName = $(this).data('user-name');
    
    // Update form action and show form
    $('#startChatForm').attr('action', '{{ route("admin.chat.send_initial", ":userId") }}'.replace(':userId', selectedUserId));
    $('#selectedUserName').text(selectedUserName);
    $('#messageForm').slideDown();
    
    // Focus on message input
    $('#messageInput').focus();
  });

  // Handle file selection
  $('#chatImageInput').on('change', function(){
    var name = this.files && this.files.length ? this.files[0].name : '';
    $('#selectedFileName').text(name);
  });

  // Handle form submission
  $('#startChatForm').on('submit', function(e) {
    e.preventDefault();
    
    if (!selectedUserId) {
      alert('{{ __("messages.please_select_user") }}');
      return;
    }

    var form = $(this)[0];
    var url = $(this).attr('action');
    var formData = new FormData(form);

    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Redirect to chat with the user
        window.location.href = '{{ route("admin.chat.show", ":userId") }}'.replace(':userId', selectedUserId);
      },
      error: function(xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('messages.failed_to_send_message') }}";
        alert("{{ __('messages.error') }}: " + msg);
      }
    });
  });
});
</script>
@endsection