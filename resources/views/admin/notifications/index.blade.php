@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-bell"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.notifications') }}</h1>
      <small>{{ __('messages.notification_management') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.notifications') }}</li>
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
              <a class="btn btn-primary" href="{{ route('notifications.create') }}">
                <i class="fa fa-plus" aria-hidden="true"></i> {{ __('messages.send_notification') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <div class="row panel-header mb-3">
              <div class="col-sm-4">
                <label>{{ __('messages.display') }}
                  <select id="recordsPerPage" name="example_length" onchange="fetchData(1)">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select> {{ __('messages.records_per_page') }}
                </label>
              </div>
              <div class="col-sm-4 text-center"></div>
              <div class="col-sm-4">
                <div class="input-group custom-search-form">
                  <input type="search" id="searchInput" class="form-control" placeholder="Search notifications...">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="fetchData(1)">
                      <i class="fa fa-search"></i>
                    </button>
                  </span>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.send_to') }}</th>
                    <!--<th>{{ __('messages.status') }}</th>-->
                    <th>{{ __('messages.sent_count') }}</th>
                    <th>{{ __('messages.sent_at') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.action') }}</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @include('admin.notifications.partials.table-body', ['notifications' => $notifications])
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                  Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} 
                  of {{ $notifications->total() }} entries
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers">
                  {{ $notifications->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;
    
    const url = new URL('{{ route("notifications.index") }}', window.location.origin);
    url.searchParams.set('page', page);
    url.searchParams.set('query', query);
    url.searchParams.set('perPage', perPage);
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('tableBody').innerHTML = data;
    })
    .catch(error => console.error('Error:', error));
}

// Search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        fetchData(1);
    }
});
</script>
@endsection
