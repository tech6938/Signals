@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-cube"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.add_package') }}</h1>
      <small>{{ __('messages.create_subscription_package') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li>
          <a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a>
        </li>
        <li class="active">{{ __('messages.add_package') }}</li>
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
              <a class="btn btn-success" href="{{ route('packages.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.packages_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form action="{{ route('packages.store') }}" method="POST">
              @csrf
              <div class="row">

                <!-- Package Name -->
                <div class="form-group col-md-6">
                  <label for="name">{{ __('messages.package_name') }}</label>
                  <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name') }}" placeholder="Enter package name">
                  <div class="text-danger">{{ $errors->first('name') }}</div>
                </div>

                <div class="form-group col-md-6">
                  <label for="status">{{ __('messages.status') }}</label>
                  <select class="form-control" id="status" name="status">
                    <option value="">{{ __('messages.select_status') }}</option>
                    <option value="active" {{ old('status')=='active'?'selected':'' }}>{{ __('messages.active') }}</option>
                    <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>{{ __('messages.inactive') }}</option>
                  </select>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                </div>
                
                <!-- Price -->
                <div class="form-group col-md-6">
                  <label for="price">{{ __('messages.price') }}</label>
                  <input type="text" class="form-control" id="price" name="price"
                    value="{{ old('price') }}" placeholder="e.g. 29.99">
                  <div class="text-danger">{{ $errors->first('price') }}</div>
                </div>
              
                <div class="form-group col-md-6">
                  <label for="price">{{ __('messages.lyd_price') }}</label>
                  <input type="text" class="form-control" id="lyd_price" name="lyd_price"
                    value="{{ old('lyd_price') }}" placeholder="e.g. 29.99">
                  <div class="text-danger">{{ $errors->first('lyd_price') }}</div>
                </div>
              

                <!-- Duration Days -->
                <div class="form-group col-md-6">
                  <label for="duration_days">{{ __('messages.duration_days') }}</label>
                  <input type="number" class="form-control" id="duration_days" name="duration_days"
                    value="{{ old('duration_days') }}" placeholder="e.g. 30">
                  <div class="text-danger">{{ $errors->first('duration_days') }}</div>
                </div>

                <!-- Signal Limit -->
                <div class="form-group col-md-6">
                  <label for="signal_limit">{{ __('messages.signal_limit') }}</label>
                  <input type="text" class="form-control" id="signal_limit" name="signal_limit"
                    value="{{ old('signal_limit') }}" placeholder="Leave blank for unlimited">
                  <div class="text-danger">{{ $errors->first('signal_limit') }}</div>
                </div>

                <!-- Status -->


                <!-- Description -->
                <div class="form-group col-md-12">
                  <label for="description">{{ __('messages.description') }}</label>
                  <textarea class="form-control" id="description" name="description" rows="4"
                    placeholder="Write package details...">{{ old('description') }}</textarea>
                  <div class="text-danger">{{ $errors->first('description') }}</div>
                </div>

              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.add_package') }}</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection