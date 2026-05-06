@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-bell"></i>
    </div>
    <div class="header-title">
      <h1>Edit Notification</h1>
      <small>Edit notification details</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> Home</a></li>
        <li><a href="{{ route('notifications.index') }}">Notifications</a></li>
        <li class="active">Edit Notification</li>
      </ol>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="{{ route('notifications.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Notifications
              </a>
            </div>
          </div>

          <div class="panel-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('notifications.update', $notification) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Notification Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title', $notification->title) }}" placeholder="Enter notification title" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Send To</label>
                            <p class="form-control-static">{{ $notification->send_to_text }}</p>
                            <small class="text-muted">Recipient type cannot be changed after creation.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Notification Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Enter notification description" required>{{ old('description', $notification->description) }}</textarea>
                    <small class="help-block">You can include emojis, links, and basic formatting.</small>
                </div>

                @if($notification->send_to === 'individual' && $notification->target_users)
                    <div class="form-group">
                        <label>Target Users</label>
                        <div class="form-control-static">
                            @php
                                $users = \App\Models\User::whereIn('id', $notification->target_users)->get();
                            @endphp
                            @foreach($users as $user)
                                <span class="label label-info">{{ $user->f_name }} {{ $user->last_name }} ({{ $user->email }})</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label>Status</label>
                    <p class="form-control-static">
                        @if($notification->sent)
                            <span class="label label-success">Sent</span>
                            <small class="text-muted">Sent to {{ $notification->sent_count }} users on {{ $notification->sent_at->format('M d, Y H:i') }}</small>
                        @else
                            <span class="label label-warning">Pending</span>
                        @endif
                    </p>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Notification
                    </button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
