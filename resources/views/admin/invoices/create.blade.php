@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-file-pdf-o"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.generate_invoice') }}</h1>
      <small>Create a new Invoice</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.add_invoice') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-success" href="{{ route('invoices.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.invoices_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form action="{{ route('invoices.store') }}" method="POST">
              @csrf

              {{-- User Selection --}}
              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.full_name') }}</label>
                <div class="text-danger">{{ $errors->first('user_id') }}</div>
                <select name="user_id" id="userSelect" class="form-control select2" required>
                  <option value="">{{ __('messages.select_user') }}</option>
                  @foreach($users as $user)
                  <option value="{{$user->id}}" data-phone="{{ $user->phone ?? 'N/A' }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                    {{$user->f_name . ' ' . $user->last_name}}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.phone') }}</label>
                <div class="text-danger">{{ $errors->first('phone') }}</div>
                <input type="text" id="userPhone" class="form-control" readonly value="{{ old('phone') }}">
              </div>

              {{-- Package Selection --}}
              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.package') }}</label>
                <div class="text-danger">{{ $errors->first('package_id') }}</div>
                <select name="package_id" id="packageSelect" class="form-control" required>
                  <option value="">{{ __('messages.select_package') }}</option>
                  @foreach($packages as $package)
                  <option
                    value="{{$package->id}}"
                    data-price="{{ $package->price ?? 0 }}"
                    data-lyd-price="{{ $package->lyd_price ?? 0 }}"
                    data-days="{{ $package->duration_days ?? 30 }}"
                    {{ old('package_id') == $package->id ? 'selected' : '' }}>
                    {{$package->name}}
                  </option>
                  @endforeach
                </select>
              </div>

              {{-- Currency Selection --}}
              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.currency') }}</label>
                <div class="text-danger">{{ $errors->first('currency') }}</div>
                <select name="currency" id="currencySelect" class="form-control" required>
                  <option value="usd" {{ old('currency') == 'usd' ? 'selected' : '' }}>USD</option>
                  <option value="lyd" {{ old('currency') == 'lyd' ? 'selected' : '' }}>LYD</option>
                </select>
              </div>

              {{-- Amount --}}
              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.amount') }}</label>
                <div class="text-danger">{{ $errors->first('amount') }}</div>
                <input type="text" id="packagePrice" name="amount" class="form-control" readonly value="{{ old('amount') }}">
              </div>

              {{-- Duration (auto-filled from package) --}}
              <div class="form-group col-md-6 mb-3">
                <label>{{ __('messages.duration_days') }}</label>
                <input type="text" id="durationDays" name="duration" class="form-control" readonly>
              </div>

              {{-- Buttons --}}
              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.generate_pdf_save') }}</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{-- ✅ Script Section --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('#userSelect').select2({
      placeholder: "{{ __('messages.select_user') }}",
      allowClear: true,
      width: '100%'
    });

    const packageSelect = document.getElementById('packageSelect');
    const packagePrice = document.getElementById('packagePrice');
    const currencySelect = document.getElementById('currencySelect');
    const durationDaysField = document.getElementById('durationDays');

    // ✅ Update phone number when user selected
    $('#userSelect').on('change', function() {
      const phone = $(this).find(':selected').data('phone') || '';
      $('#userPhone').val(phone);
    });

    // ✅ When package or currency changes
    $('#packageSelect, #currencySelect').on('change', function() {
      updatePackageDetails();
    });

    function updatePackageDetails() {
      const pkg = packageSelect.options[packageSelect.selectedIndex];
      if (!pkg) {
        durationDaysField.value = '';
        packagePrice.value = '';
        return;
      }

      const currency = currencySelect.value;
      const durationDays = parseInt(pkg.dataset.days || 30);
      durationDaysField.value = durationDays;

      let basePrice = 0;
      if (currency === 'usd') {
        basePrice = parseFloat(pkg.dataset.price || 0);
      } else if (currency === 'lyd') {
        basePrice = parseFloat(pkg.dataset.lydPrice || 0);
      }

      packagePrice.value = basePrice.toFixed(2);
    }

    // Initialize on load
    updatePackageDetails();
  });
</script>

@endsection