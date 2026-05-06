@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-newspaper"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.news') }}</h1>
      <small>{{ __('messages.news_table') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.news') }}</li>
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
              <a class="btn btn-primary" href="{{ route('news.create') }}">
                <i class="fa fa-plus" aria-hidden="true"></i>{{ __('messages.add_news') }}
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
                  <input type="search" id="searchInput" class="form-control" placeholder="Search..">
                  <span class="input-group-btn">
                    <button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <!-- News Table -->
            <div class="table-responsive">
              <table id="printTable" class="table table-bordered table-hover">
                <thead class="success">
                  <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.url') }}</th>
                    <th>{{ __('messages.image') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.time') }}</th>
                    <th class="no-print">{{ __('messages.action') }}</th>
                  </tr>
                </thead>
<tbody id="tableBody">
  @include('admin.news._table', ['newsList' => $newsList])
</tbody>

              </table>

              <div class="d-flex justify-content-end" id="paginationLinks">
                {{ $newsList->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- AJAX Live Search & Pagination -->
<script>
  function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;

    fetch(`{{ route('news.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('tableBody').innerHTML = data;

        // update pagination links
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('query', query);
        history.replaceState(null, null, '?' + urlParams.toString());
      })
      .catch(error => console.error(error));
  }

  // Trigger live search on typing
  document.getElementById('searchInput').addEventListener('keyup', function() {
    fetchData(1);
  });

  // Optional: trigger on pressing search button
  document.querySelector('.input-group-btn button').addEventListener('click', function() {
    fetchData(1);
  });


  document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
      e.preventDefault();
      const url = e.target.closest('a').href;
      const page = new URL(url).searchParams.get('page');
      fetchData(page);
    }
  });
</script>
@endsection