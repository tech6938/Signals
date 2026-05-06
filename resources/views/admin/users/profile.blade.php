@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-user"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.profile') }}</h1>
      <small>{{ __('messages.edit_profile') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="#"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.profile') }}</li>
      </ol>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="panel-title">
              <h4>{{ __('messages.update_password') }}</h4>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('profile.update') }}" method="POST" id="employeeForm">
              @csrf
              @method('PUT')

              <div class="row">
                <!-- Current Password -->
                <div class="form-group col-md-6 mb-3">
                  <label for="current_password">{{ __('messages.current_password') }}</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" required>
                  <div class="text-danger">{{ $errors->first('current_password') }}</div>
                </div>

                <!-- New Password -->
                <div class="form-group col-md-6 mb-3">
                  <label for="password">{{ __('messages.new_password') }}</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                  <div class="text-danger">{{ $errors->first('password') }}</div>
                </div>
              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.update_password') }}</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>
@endsection
