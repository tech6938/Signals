@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-wallet"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.admin_wallet') }}</h1>
      <small>{{ __('messages.income_subscription_summary') }}</small>
    </div>
  </section>

  <section class="content">
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_lyd_income') }}</h5>
          <h3>{{ number_format($totalLYD, 2) }} LYD</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_usd_income') }}</h5>
          <h3>{{ number_format($totalUSD, 2) }} USD</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.total_subscribers') }}</h5>
          <h3>{{ $totalSubscribers }}</h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-2 text-center">
          <h5>{{ __('messages.average_per_plan') }}</h5>
          @foreach($averagePerPlan as $plan => $avg)
          <div>{{ $plan }}: {{ number_format($avg, 2) }}</div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <h4>{{ __('messages.invoices_table') }}</h4>
      </div>
      <div class="panel-body">
        <div class="row mb-3">
          <div class="col-sm-3">
            <input type="date" id="start_date" class="form-control" placeholder="{{ __('messages.start_date') }}">
          </div>
          <div class="col-sm-3">
            <input type="date" id="end_date" class="form-control" placeholder="{{ __('messages.end_date') }}">
          </div>
          <div class="col-sm-3">
            <select id="active_only" class="form-control">
              <option value="">{{ __('messages.all_subscriptions') }}</option>
              <option value="1">{{ __('messages.active_only') }}</option>
            </select>
          </div>
          <div class="col-sm-3">
            <button class="btn btn-primary" onclick="filterData()">{{ __('messages.filter') }}</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('messages.user_name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.phone') }}</th>
                <th>{{ __('messages.total_lyd') }}</th>
                <th>{{ __('messages.total_usd') }}</th>
                <th>{{ __('messages.packages') }}</th>
                <th>{{ __('messages.first_purchase') }}</th>
                <th>{{ __('messages.last_expiry') }}</th>
                <th>{{ __('messages.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->user_fname }} {{ $user->user_lname }}</td>
                <td>{{ $user->user_email }}</td>
                <td>{{ $user->user_phone ?? __('messages.not_available') }}</td>
                <td>
                  @if($user->total_lyd > 0)
                    <span class="label label-success">{{ number_format($user->total_lyd, 2) }} LYD</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($user->total_usd > 0)
                    <span class="label label-info">{{ number_format($user->total_usd, 2) }} USD</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-primary">{{ $user->total_packages }}</span>
                </td>
                <td>{{ $user->first_purchase ? \Carbon\Carbon::parse($user->first_purchase)->format('d M Y') : '-' }}</td>
                <td>{{ $user->last_expiry ? \Carbon\Carbon::parse($user->last_expiry)->format('d M Y') : '-' }}</td>
                <td>
                  <a href="{{ route('wallet.user.detail', $user->user_id) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-eye"></i> {{ __('messages.view_details') }}
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="text-center">{{ __('messages.no_records_found') }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          <div class="d-flex justify-content-end">
            {{ $users->links() }}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  function filterData() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    const active = document.getElementById('active_only').value;
    let url = `{{ route('wallet.index') }}?start_date=${start}&end_date=${end}&active_only=${active}`;
    window.location.href = url;
  }
</script>
@endsection
