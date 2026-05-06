@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa-solid fa-message"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.edit_message') }}</h1>
      <small>{{ __('messages.update_message_details') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li><a href="{{ route('userMessages.index') }}">{{ __('messages.messages') }}</a></li>
        <li class="active">{{ __('messages.edit_message') }}</li>
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
              <a class="btn btn-success" href="{{ route('userMessages.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.messages_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('userMessages.update', $userMessage->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="row">

                <!-- Title -->
                <div class="form-group col-md-6 mb-3">
                  <label for="title">{{ __('messages.title') }}</label>
                  <div class="text-danger">{{ $errors->first('title') }}</div>
                  <input type="text" class="form-control" id="title" name="title"
                    value="{{ old('title', $userMessage->title) }}">
                </div>

                <!-- Status -->
                <div class="form-group col-md-6 mb-3">
                  <label for="status">{{ __('messages.status') }}</label>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                  <select class="form-control" id="status" name="status">
                    <option value="">Select status</option>
                    <option value="1" {{ old('status', $userMessage->status) == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="0" {{ old('status', $userMessage->status) == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                  </select>
                </div>

                <!-- Description -->
                <div class="form-group col-md-12 mb-3">
                  <label for="description">{{ __('messages.description') }}</label>
                  <div class="text-danger">{{ $errors->first('description') }}</div>
                  <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $userMessage->description) }}</textarea>
                </div>
                 <div class="form-group col-md-6 mb-3">
                  <label for="date">{{ __('messages.date') }}</label>
                  <div class="text-danger">{{ $errors->first('date') }}</div>
                  <input type="date" class="form-control" id="date" name="date"
                    value="{{ old('date', $userMessage->date) }}">
                </div>
                 <div class="form-group col-md-6 mb-3">
                  <label for="time">{{ __('messages.time') }}</label>
                  <div class="text-danger">{{ $errors->first('time') }}</div>
                  <input type="time" class="form-control" id="time" name="time"
                    value="{{ old('time', $userMessage->time) }}">
                </div>


              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.update_message') }}</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection