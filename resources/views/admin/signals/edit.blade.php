@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-line-chart"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.edit_signal') }}</h1>
      <small>{{ __('messages.update_existing_signal') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.edit_signal') }}</li>
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
            <form action="{{ route('signals.update', $signal->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="row">
                <!-- Coin Name -->
                <div class="form-group col-md-6">
                  <label for="coin_name">{{ __('messages.coin_name') }}</label>
                  <input type="text" class="form-control" id="coin_name" name="coin_name" value="{{ old('coin_name', $signal->coin_name) }}">
                  <div class="text-danger">{{ $errors->first('coin_name') }}</div>
                </div>

                <!-- B Price -->
                <div class="form-group col-md-6">
                  <label for="b_price">{{ __('messages.b_price') }}</label>
                  <input type="text" class="form-control" id="b_price" name="b_price"
                    value="{{ old('b_price', $signal->b_price != '00' ? rtrim(rtrim(number_format($signal->b_price, 8, '.', ''), '0'), '.') : '') }}">
                  <div class="text-danger">{{ $errors->first('b_price') }}</div>
                </div>

                <!-- Last Price -->
                <div class="form-group col-md-6">
                  <label for="last_price">{{ __('messages.last_price') }}</label>
                  <input type="text" class="form-control" id="last_price" name="last_price"
                    value="{{ old('last_price', $signal->last_price != '00' ? rtrim(rtrim(number_format($signal->last_price, 8, '.', ''), '0'), '.') : '') }}">
                  <div class="text-danger">{{ $errors->first('last_price') }}</div>
                </div>

                <!-- Status -->
                <div class="form-group col-md-6">
                  <label for="status">{{ __('messages.status') }}</label>
                  <select class="form-control" id="status" name="status">
                    <option value="">{{ __('messages.select_status') }}</option>
                    <option value="0" {{ old('status', $signal->status) == '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    <option value="1" {{ old('status', $signal->status) == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                  </select>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                </div>

                <!-- TP1 to TP4 -->
                @for ($i = 1; $i <= 4; $i++)
                  <div class="form-group col-md-3">
                  <label for="tp{{ $i }}">{{ __('messages.tp') }}{{ $i }}</label>
                  <input type="text" class="form-control" id="tp{{ $i }}" name="tp{{ $i }}"
                    value="{{ old('tp'.$i, $signal->{'tp'.$i} != '00' ? rtrim(rtrim(number_format($signal->{'tp'.$i}, 8, '.', ''), '0'), '.') : '') }}">
                  <div class="text-danger">{{ $errors->first('tp'.$i) }}</div>
              </div>
              @endfor

              <!-- Icon1 Upload -->
              <div class="form-group col-md-6 mb-3">
                <label for="icon1">{{ __('messages.icon1') }}</label>
                <div class="text-danger">{{ $errors->first('icon1') }}</div>
                <input type="file" class="form-control" id="icon1" name="icon1" onchange="previewImage(event, 'icon1_preview')">
                <div id="icon1_preview" class="mt-2">
                  @if($signal->icon1)
                  <img src="{{asset('storage/uploads/icons/' . basename($signal->icon1)) }}" width="100px" alt="Icon1">
                  @endif
                </div>
              </div>

              <!-- Icon2 Upload -->
              <div class="form-group col-md-6 mb-3">
                <label for="icon2">{{ __('messages.icon2') }}</label>
                <div class="text-danger">{{ $errors->first('icon2') }}</div>
                <input type="file" class="form-control" id="icon2" name="icon2" onchange="previewImage(event, 'icon2_preview')">
                <div id="icon2_preview" class="mt-2">
                  @if($signal->icon2)
                  <img src="{{ asset('storage/uploads/icons/' . basename($signal->icon2))  }}" width="100px" alt="Icon2">
                  @endif
                </div>
              </div>

          </div>

          <div class="col-sm-12 reset-button">
            <a href="{{ route('signals.index') }}" class="btn btn-default">{{ __('messages.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('messages.update_signal') }}</button>
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
      document.getElementById(previewId).innerHTML = '<img src="' + reader.result + '" width="100px" alt="Preview">';
    };
    reader.readAsDataURL(event.target.files[0]);
  }
</script>
@endsection