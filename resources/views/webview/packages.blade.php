<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@lang('messages.buy_packages')</title>

  {{-- Bootstrap 4 CSS --}}
  <link rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    body {
      background: #f8f9fa;
    }

    .card {
      border-radius: 12px;
    }

    .bank-card {
      background: #ffffff;
      border: 1px solid #ddd;
    }

    .package-title {
      font-weight: 600;
      font-size: 20px;
    }

    .selected-package {
      border: 2px solid #007bff !important;
      background-color: #f8f9ff !important;
    }

    .selected-bank {
      border: 2px solid #28a745 !important;
      background-color: #f8fff9 !important;
    }

    .total-section {
      background: #ffffff;
      border-radius: 12px;
      padding: 20px;
      margin-top: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .hidden {
      display: none;
    }

    @if(app()->getLocale() == 'ar')
    body {
      direction: rtl;
      text-align: right;
    }

    .text-right {
      text-align: left !important;
    }
    @endif
  </style>
</head>

<body>

  <div class="container py-4 mb-4">

    {{-- Language Switcher --}}
    <div class="text-right mb-3">
      <form action="{{ route('change.language') }}" method="GET">
        <select name="lang" onchange="this.form.submit()" class="form-control d-inline-block" style="width: 150px;">
          <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
          <option value="ar" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>العربية</option>
        </select>
      </form>
    </div>

    <h2 class="text-center mb-4">@lang('messages.available_packages')</h2>

    {{-- Packages List --}}
    <div class="row mb-4">
      @foreach($packages as $package)
      @php
        $status = $packageStatuses[$package->id] ?? 'available';
      @endphp
      <div class="col-md-4 col-sm-6 mb-4">
        <div class="card shadow-sm h-100 package-card"
          data-package-id="{{ $package->id }}"
          data-package-name="{{ $package->name }}"
          data-package-price-usd="{{ $package->price }}"
          data-package-price-lyd="{{ $package->lyd_price }}"
          data-package-duration="{{ $package->duration_days }}"
          data-status="{{ $status }}">
          <div class="card-body text-center">
            <div class="package-title">{{ $package->name }}</div>

            {{-- Status Label --}}
            @if($status === 'pending')
              <span class="badge badge-warning">@lang('messages.pending_approval')</span>
            @elseif($status === 'in_progress')
              <span class="badge badge-info">@lang('messages.payment_in_progress')</span>
            @elseif($status === 'active')
              <span class="badge badge-success">@lang('messages.active')</span>
            @elseif($status === 'expired')
              <span class="badge badge-secondary">@lang('messages.expired')</span>
            @else
              <span class="badge badge-primary">@lang('messages.available')</span>
            @endif

            <p class="text-muted mt-2">{{ $package->description }}</p>
            <p><strong>@lang('messages.price_usd'):</strong> ${{ $package->price }}</p>
            <p><strong>@lang('messages.price_lyd'):</strong> {{ $package->lyd_price }}</p>
            <p><strong>@lang('messages.duration'):</strong> {{ $package->duration_days }} @lang('messages.days')</p>
            <p><strong>@lang('messages.signals'):</strong> {{ $package->signal_limit ?? 'Unlimited' }}</p>

            {{-- Button behavior by status --}}
            <button class="btn btn-primary btn-sm select-package-btn"
              @if(in_array($status, ['pending','active','in_progress'])) disabled @endif>
              @if($status === 'pending')
                @lang('messages.pending_approval')
              @elseif($status === 'in_progress')
                @lang('messages.payment_in_progress')
              @elseif($status === 'active')
                @lang('messages.active')
              @elseif($status === 'expired')
                @lang('messages.renew_package')
              @else
                @lang('messages.select_package')
              @endif
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Bank Selection --}}
    <div class="row mt-5 mb-4" id="bank-selection" style="display: none;">
      <div class="col-md-12">
        <h3 class="text-center mb-4">@lang('messages.select_bank')</h3>
        <div class="row">
          @if($banks->count() > 0)
          @foreach($banks as $bank)
          <div class="col-md-6 mb-4">
            <div class="card bank-card shadow-sm bank-option"
              data-bank-id="{{ $bank->id }}"
              data-bank-name="{{ $bank->bank_name }}">
              <div class="card-header text-center bg-primary text-white">
                <h4>{{ $bank->bank_name }}</h4>
              </div>
              <div class="card-body text-center">
                <p><strong>@lang('messages.account_title'):</strong> {{ $bank->account_title }}</p>
                @if($bank->account_number)
                <p><strong>@lang('messages.account_number'):</strong> {{ $bank->account_number }}</p>
                @endif
                @if($bank->iban)
                <p><strong>IBAN:</strong> {{ $bank->iban }}</p>
                @endif
                @if($bank->swift_code)
                <p><strong>@lang('messages.swift_code'):</strong> {{ $bank->swift_code }}</p>
                @endif
                @if($bank->description)
                <p class="text-muted mt-3">{{ $bank->description }}</p>
                @endif
                <button class="btn btn-success btn-sm select-bank-btn">@lang('messages.select_bank')</button>
              </div>
            </div>
          </div>
          @endforeach
          @else
          <div class="col-md-12">
            <p class="text-center text-danger">@lang('messages.bank_not_available')</p>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Total and Invoice Generation --}}
    <div class="total-section hidden mb-5" id="total-section">
      <div class="row mb-4">
        <div class="col-md-6">
          <h4>@lang('messages.order_summary')</h4>
          <div id="order-details"></div>
        </div>
        <div class="col-md-6">
          <h4>@lang('messages.payment_details')</h4>
          <div id="payment-details"></div>
        </div>
      </div>

      <div class="row mt-4 mb-4">
        <div class="col-md-12 text-center">
          <div class="form-group">
            <label for="currency">@lang('messages.select_currency'):</label>
            <select class="form-control" id="currency" style="max-width: 200px; margin: 0 auto;">
              <option value="usd">USD</option>
              <option value="lyd">LYD</option>
            </select>
          </div>
          <button class="btn btn-success btn-lg" id="generate-invoice-btn">@lang('messages.generate_invoice')</button>
        </div>
      </div>
    </div>

    {{-- Hidden inputs --}}
    <input type="hidden" id="selected-package-id">
    <input type="hidden" id="selected-bank-id">
    <input type="hidden" id="user-token" value="{{ request('token', '') }}">
    <input type="hidden" id="user-id" value="{{ $user->id ?? '' }}">

  </div>

  {{-- Bootstrap JS --}}
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

  <script>
    let selectedPackage = null;
    let selectedBank = null;

    // Package selection
    $('.select-package-btn').click(function() {
      const card = $(this).closest('.package-card');
      const status = card.data('status');

      // Skip if not available or expired
      if (status !== 'available' && status !== 'expired') {
        return;
      }

      const packageId = card.data('package-id');
      const packageName = card.data('package-name');
      const packagePriceUsd = card.data('package-price-usd');
      const packagePriceLyd = card.data('package-price-lyd');
      const packageDuration = card.data('package-duration');

      $('.package-card').removeClass('selected-package');
      card.addClass('selected-package');

      selectedPackage = {
        id: packageId,
        name: packageName,
        priceUsd: packagePriceUsd,
        priceLyd: packagePriceLyd,
        duration: packageDuration
      };

      $('#selected-package-id').val(packageId);

      $('#bank-selection').slideDown(400);
      $('html, body').animate({
        scrollTop: $('#bank-selection').offset().top - 50
      }, 500);
    });

    // Bank selection
    $('.select-bank-btn').click(function() {
      const card = $(this).closest('.bank-option');
      const bankId = card.data('bank-id');
      const bankName = card.data('bank-name');

      $('.bank-option').removeClass('selected-bank');
      card.addClass('selected-bank');

      selectedBank = {
        id: bankId,
        name: bankName
      };

      $('#selected-bank-id').val(bankId);

      updateOrderSummary();
      $('#total-section').removeClass('hidden').slideDown(400);

      $('html, body').animate({
        scrollTop: $('#total-section').offset().top - 50
      }, 500);
    });

    // Currency change
    $('#currency').change(function() {
      updateOrderSummary();
    });

    function updateOrderSummary() {
      if (!selectedPackage || !selectedBank) return;

      const currency = $('#currency').val();
      const price = currency === 'lyd' ? selectedPackage.priceLyd : selectedPackage.priceUsd;
      const currencySymbol = currency === 'lyd' ? 'LYD' : 'USD';

      $('#order-details').html(`
        <p><strong>@lang('messages.package'):</strong> ${selectedPackage.name}</p>
        <p><strong>@lang('messages.duration'):</strong> ${selectedPackage.duration} @lang('messages.days')</p>
        <p><strong>@lang('messages.price'):</strong> ${currencySymbol} ${price}</p>
      `);

      $('#payment-details').html(`
        <p><strong>@lang('messages.bank'):</strong> ${selectedBank.name}</p>
        <p><strong>@lang('messages.amount'):</strong> ${currencySymbol} ${price}</p>
      `);
    }

    // Generate invoice
    $('#generate-invoice-btn').click(function() {
      if (!selectedPackage || !selectedBank) {
        alert('@lang("messages.select_package_and_bank")');
        return;
      }

      const currency = $('#currency').val();
      const userToken = $('#user-token').val();
      const userId = $('#user-id').val();

      $(this).prop('disabled', true).text('@lang("messages.generating")...');

      $.ajax({
        url: '{{ route("webview.generate-invoice") }}',
        method: 'POST',
        data: {
          package_id: selectedPackage.id,
          bank_id: selectedBank.id,
          currency: currency,
          token: userToken,
          user_id: userId,
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          if (response.success) {
            alert(response.message);
            $('#generate-invoice-btn').hide();
            const successMsg = $('<div>')
              .addClass('alert alert-success mt-3')
              .html('<strong>@lang("messages.success")!</strong> ' + response.message);
            $('#total-section').append(successMsg);
          } else {
            alert('Error: ' + response.message);
          }
        },
        error: function(xhr) {
          let errorMessage = '@lang("messages.error_generating_invoice")';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          alert(errorMessage);
          console.error('Error:', xhr.responseText);
        },
        complete: function() {
          $('#generate-invoice-btn').prop('disabled', false).text('@lang("messages.generate_invoice")');
        }
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
