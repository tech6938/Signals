@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-edit"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.edit_value') }}</h1>
      <small>{{ __('messages.update_value') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li><a href="{{ route('values.index') }}">{{ __('messages.values') }}</a></li>
        <li class="active">{{ __('messages.edit_value') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
             <div class="btn-group">
              <a class="btn btn-success" href="{{ route('values.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.values_table') }}
              </a>
            </div>
            <!-- <h4>{{ __('messages.edit_value') }}</h4> -->
          </div>

          <div class="panel-body">
            <form action="{{ route('values.update', $value->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="form-group col-md-6">
                <label for="coin_name">{{ __('messages.coin_name') }}</label>
                <input type="text" class="form-control" id="coin_name" name="coin_name"
                  value="{{ old('coin_name', $value->coin_name) }}">
                <div class="text-danger">{{ $errors->first('coin_name') }}</div>
              </div>

              <div class="form-group col-md-6">
                <label for="h_value">{{ __('messages.high_value') }}</label>
                <input type="number" class="form-control" id="h_value" name="h_value" step="0.01"
                  value="{{ old('h_value', $value->h_value) }}">
                <div class="text-danger">{{ $errors->first('h_value') }}</div>
              </div>

              <div class="form-group col-md-6">
                <label for="l_value">{{ __('messages.low_value') }}</label>
                <input type="number" class="form-control" id="l_value" name="l_value" step="0.01"
                  value="{{ old('l_value', $value->l_value) }}">
                <div class="text-danger">{{ $errors->first('l_value') }}</div>
              </div>
              
              <div class="form-group col-md-6">
                <label for="b_price">{{ __('messages.current_buy_price') }}</label>
                <input type="number" class="form-control" id="b_price" name="b_price" step="0.01"
                  value="{{ old('b_price', $value->b_price) }}">
                <div class="text-danger">{{ $errors->first('b_price') }}</div>
              </div>
              
              <div class="form-group col-md-6">
                <label for="s_price">{{ __('messages.current_sell_price') }}</label>
                <input type="number" class="form-control" id="s_price" name="s_price" step="0.01"
                  value="{{ old('s_price', $value->s_price) }}">
                <div class="text-danger">{{ $errors->first('s_price') }}</div>
              </div>

              <div class="form-group col-md-6">
                <label for="status">{{ __('messages.status') }}</label>
                <select class="form-control" id="status" name="status">
                  <option value="1" {{ $value->status ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                  <option value="0" {{ !$value->status ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                </select>
              </div>

              <div class="col-sm-12 reset-button">
                <a href="{{ route('values.index') }}" class="btn btn-warning">{{ __('messages.cancel') }}</a>
                <button type="submit" class="btn btn-success">{{ __('messages.update_value') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection