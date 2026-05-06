@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-user"></i></div>
    <div class="header-title">
      <h1>{{ $user->f_name }} {{ $user->last_name }} - {{ __('messages.user_package_details', ['name' => $user->f_name . ' ' . $user->last_name]) }}</h1>
      <small>{{ __('messages.purchase_history_subtitle') }}</small>
    </div>
    <div class="header-action">
      <a href="{{ route('wallet.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> {{ __('messages.back_to_wallet') }}
      </a>
    </div>
  </section>

  <section class="content">
    <!-- Summary Cards -->
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_lyd_spent') }}</h5>
          <h3 class="text-success">{{ number_format($totalLYD, 2) }} LYD</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_usd_spent') }}</h5>
          <h3 class="text-info">{{ number_format($totalUSD, 2) }} USD</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_packages') }}</h5>
          <h3 class="text-primary">{{ $totalPackages }}</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.user_type') }}</h5>
          <h3>
            <span class="label label-{{ $user->type === 'subscriber' ? 'success' : 'warning' }}">
              {{ ucfirst(str_replace('_', ' ', $user->type)) }}
            </span>
          </h3>
        </div>
      </div>
    </div>

    <!-- User Information -->
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <h4><i class="fa fa-user"></i> {{ __('messages.user_information') }}</h4>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-md-3">
                <strong>{{ __('messages.full_name') }}:</strong><br>
                {{ $user->f_name }} {{ $user->last_name }}
              </div>
              <div class="col-md-3">
                <strong>{{ __('messages.email') }}:</strong><br>
                {{ $user->email }}
              </div>
              <div class="col-md-3">
                <strong>{{ __('messages.phone') }}:</strong><br>
                {{ $user->phone ?? __('messages.not_provided') }}
              </div>
              <div class="col-md-3">
                <strong>{{ __('messages.status') }}:</strong><br>
                <span class="label label-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                  {{ ucfirst($user->status) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Package Details Table -->
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <h4><i class="fa fa-box"></i> {{ __('messages.package_purchase_history') }}</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('messages.package_name') }}</th>
                <th>{{ __('messages.amount') }}</th>
                <th>{{ __('messages.currency') }}</th>
                <th>{{ __('messages.start_date') }}</th>
                <th>{{ __('messages.end_date') }}</th>
                <th>{{ __('messages.purchase_date') }}</th>
                <th>{{ __('messages.status') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($invoices as $invoice)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $invoice->package->name ?? __('messages.unknown_package') }}</strong></td>
                <td>
                  <span class="label label-{{ strtolower($invoice->currency) === 'lyd' ? 'success' : 'info' }}">
                    {{ number_format($invoice->amount, 2) }}
                  </span>
                </td>
                <td>{{ strtoupper($invoice->currency) }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->start_date)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->end_date)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y H:i') }}</td>
                <td>
                  @php
                    $isActive = \Carbon\Carbon::parse($invoice->end_date)->isFuture();
                  @endphp
                  @if($isActive)
                    <span class="label label-success">{{ __('messages.active') }}</span>
                  @else
                    <span class="label label-danger">{{ __('messages.expired') }}</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">{{ __('messages.no_package_purchases') }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
