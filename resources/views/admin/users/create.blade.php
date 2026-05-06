@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-user"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.staff') }}</h1>
      <small>{{ __('messages.create_new_staff') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">  {{ __('messages.staff') }}</li>
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
              <a class="btn btn-success" href="{{ route('users.staff') }}">
                <i class="fa fa-table"></i>{{ __('messages.staff_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="row">

                <!-- First Name -->
                <div class="form-group col-md-6">
                  <label for="f_name">{{ __('messages.first_name') }}</label>
                  <input type="text" class="form-control" id="f_name" name="f_name" value="{{ old('f_name') }}">
                  <div class="text-danger">{{ $errors->first('f_name') }}</div>
                </div>

                <!-- Last Name -->
                <div class="form-group col-md-6">
                  <label for="last_name">{{ __('messages.last_name') }}</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                  <div class="text-danger">{{ $errors->first('last_name') }}</div>
                </div>

                <!-- Email -->
                <div class="form-group col-md-6">
                  <label for="email">{{ __('messages.email') }}</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                  <div class="text-danger">{{ $errors->first('email') }}</div>
                </div>

                <!-- Password -->
                <div class="form-group col-md-6">
                  <label for="password">{{ __('messages.password') }}</label>
                  <input type="password" class="form-control" id="password" name="password">
                  <div class="text-danger">{{ $errors->first('password') }}</div>
                </div>

                

                <!-- Phone -->
                <div class="form-group col-md-6">
                  <label for="phone">{{ __('messages.phone') }}</label>
                  <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                  <div class="text-danger">{{ $errors->first('phone') }}</div>
                </div>

              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.add_staff') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
