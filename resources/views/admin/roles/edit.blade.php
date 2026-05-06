@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-newspaper"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.edit_roles') }}</h1>
      <small>{{ __('messages.edit_roles') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.edit_roles') }}</li>
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
              <a class="btn btn-success" href="{{ route('roles.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.roles_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('roles.update', $role->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="row">

                <!-- Name -->
                <div class="form-group col-md-12 mb-3">
                  <label for="name">{{ __('messages.name') }}</label>
                  <div class="text-danger">{{ $errors->first('name') }}</div>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}">
                </div>


              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.update_roles') }}</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection