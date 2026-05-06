@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-users"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.package_purchases') }}</h1>
      <small>{{ __('messages.manage_user_purchases') }}</small>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="{{ route('invoices.approved') }}">
                <i class="fa fa-plus"></i>{{ __('messages.purchase_list') }}
              </a>
            </div>
          </div>

          <div class="panel-body">

            {{-- 🔍 Filter Bar --}}
            <div class="row panel-header mb-3">
              <div class="col-sm-3">
                <label>{{ __('messages.display') }}
                  <select id="recordsPerPage" onchange="fetchData(1)">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select> {{ __('messages.records_per_page') }}
                </label>
              </div>

              <div class="col-sm-3">
                <select id="statusFilter" class="form-control" onchange="fetchData(1)">
                  <option value="">{{ __('messages.select_status') }}</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>

              <div class="col-sm-6">
                <div class="input-group">
                  <input type="search" id="searchInput" class="form-control" placeholder="Search by name or email...">
                  <span class="input-group-btn">
                    <button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>

            {{-- 📋 Table --}}
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Price</th>
                    <th>Screenshot</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @include('admin.packages.partials.purchases_table')
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{-- Toast Container --}}
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

{{-- Toast CSS (Bootstrap 3 compatible) --}}
<style>
  .toast {
    min-width: 250px;
    margin-top: 10px;
    padding: 15px 20px;
    border-radius: 4px;
    color: #fff;
    opacity: 0.95;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
    font-size: 14px;
  }

  .toast-success {
    background-color: #5cb85c;
  }

  .toast-error {
    background-color: #d9534f;
  }

  .toast .close {
    color: #fff;
    opacity: 0.8;
    float: right;
    font-size: 16px;
    font-weight: bold;
    line-height: 1;
    cursor: pointer;
  }
</style>

<script>
  function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;
    const status = document.getElementById('statusFilter').value;

    fetch(`{{ route('admin.package.purchases') }}?page=${page}&query=${query}&perPage=${perPage}&status=${status}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('tableBody').innerHTML = data;
      })
      .catch(error => console.error(error));
  }

  // 🔄 Search + Filter Events
  document.getElementById('searchInput').addEventListener('keyup', () => fetchData(1));
  document.querySelector('.input-group-btn button').addEventListener('click', () => fetchData(1));

  document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
      e.preventDefault();
      const page = new URL(e.target.closest('a').href).searchParams.get('page');
      fetchData(page);
    }
  });

  // Toast function (Bootstrap 3)
  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = `
        ${message}
        <span class="close" onclick="this.parentElement.remove()">&times;</span>
    `;
    container.appendChild(toast);

    setTimeout(() => {
      if (toast) toast.remove();
    }, 4000);
  }

  // Show toast if session exists
  @if(session('success'))
  showToast("{{ session('success') }}", 'success');
  @endif

  @if(session('error'))
  showToast("{{ session('error') }}", 'error');
  @endif
</script>
@endsection