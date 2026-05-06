@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-line-chart"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.add_signal') }}</h1>
      <small>{{ __('messages.create_new_signal') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.add_signal') }}</li>
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
              <a class="btn btn-success" href="{{ route('signals.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.signals_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form action="{{ route('signals.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <!-- Coin Name & B Price -->
                <div class="form-group col-md-6">
                  <label for="coin_name">{{ __('messages.coin_name') }}</label>
                  <input type="text" class="form-control" id="coin_name" name="coin_name" value="{{ old('coin_name') }}">
                  <div class="text-danger">{{ $errors->first('coin_name') }}</div>
                </div>

                <div class="form-group col-md-6">
                  <label for="b_price">{{ __('messages.b_price') }}</label>
                  <input type="text" class="form-control" id="b_price" name="b_price" value="{{ old('b_price') }}">
                  <div class="text-danger">{{ $errors->first('b_price') }}</div>
                </div>

                <!-- Last Price -->
                <div class="form-group col-md-6">
                  <label for="last_price">{{ __('messages.last_price') }}</label>
                  <input type="text" class="form-control" id="last_price" name="last_price" value="{{ old('last_price') }}">
                  <div class="text-danger">{{ $errors->first('last_price') }}</div>
                </div>

                <!-- Status -->
                <div class="form-group col-md-6">
                  <label for="status">{{ __('messages.status') }}</label>
                  <select class="form-control" id="status" name="status">
                    <option value="">{{ __('messages.select_status') }}</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                  </select>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                </div>

                <!-- TP1-TP4 -->
                @for ($i = 1; $i <= 4; $i++)
                  <div class="form-group col-md-3">
                  <label for="tp{{ $i }}">{{ __('messages.tp') }}{{ $i }}</label>
                  <input type="text" class="form-control" id="tp{{ $i }}" name="tp{{ $i }}" value="{{ old('tp'.$i) }}">
                  <div class="text-danger">{{ $errors->first('tp'.$i) }}</div>
              </div>
              @endfor


              <!-- Icon1 & Icon2 Images -->
              <div class="form-group col-md-6">
                <label for="icon1">{{ __('messages.icon1') }}</label>
                <input type="file" class="form-control" id="icon1" name="icon1" onchange="previewImage(event, 'icon1_preview')">
                <div class="text-danger">{{ $errors->first('icon1') }}</div>
                <div id="icon1_preview" class="mt-2"></div>
              </div>

              <div class="form-group col-md-6">
                <label for="icon2">{{ __('messages.icon2') }}</label>
                <input type="file" class="form-control" id="icon2" name="icon2" onchange="previewImage(event, 'icon2_preview')">
                <div class="text-danger">{{ $errors->first('icon2') }}</div>
                <div id="icon2_preview" class="mt-2"></div>
              </div>






          </div>

          <div class="col-sm-12 reset-button">
            <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
            <button type="submit" class="btn btn-success">{{ __('messages.add_signal') }}</button>
          </div>
          </form>
        </div>
      </div>
    </div>
</div>
</section>
</div>

<script>
  function previewImage(event, previewId) {
    var reader = new FileReader();
    reader.onload = function() {
      document.getElementById(previewId).innerHTML = '<img src="' + reader.result + '" width="100px" alt="Icon">';
    };
    reader.readAsDataURL(event.target.files[0]);
  }
</script>
@endsection