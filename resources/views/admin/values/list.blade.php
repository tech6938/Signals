@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-line-chart"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.values') }}</h1>
      <small>{{ __('messages.values') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.values') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="{{ route('values.create') }}">
                <i class="fa fa-plus-circle"></i> {{ __('messages.add_value') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            {{-- Filters --}}
            <div class="row panel-header mb-3">
              <div class="col-sm-4">
                <label>{{ __('messages.display') }}
                  <select id="recordsPerPage" onchange="fetchData(1)">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select> {{ __('messages.records_per_page') }}
                </label>
              </div>
              <div class="col-sm-4 text-center"></div>
              <div class="col-sm-4">
                <div class="input-group">
                  <input type="search" id="searchInput" class="form-control" placeholder="Search..">
                  <span class="input-group-btn">
                    <button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>{{ __('messages.coin_name') }}</th>
                    <th>{{ __('messages.high_value') }}</th>
                    <th>{{ __('messages.low_value') }}</th>
                    <th>{{ __('messages.current_buy_price') }}</th>
                    <th>{{ __('messages.current_sell_price') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th class="no-print">{{ __('messages.action') }}</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($values as $value)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $value->coin_name }}</td>
                    <td>{{ $value->h_value }}</td>
                    <td>{{ $value->l_value }}</td>
                    <td>{{ $value->b_price }}</td>
                    <td>{{ $value->s_price }}</td>
                    <td>{{ $value->status ? 'Active' : 'Inactive' }}</td>
                    <td class="no-print">
                      <a href="{{ route('values.edit', $value->id) }}"><i class="fa fa-pencil" style="color:#007BFF;"></i></a> |
                      <form action="{{ route('values.destroy', $value->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" style="border:none;background:none;padding:0;">
                          <i class="fa fa-trash-o" style="color:red;"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6">{{ __('messages.no_records_found') }}</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>
              </table>

              {{-- Pagination --}}
              <div class="d-flex justify-content-end" id="paginationLinks">
                {{ $values->links() }}
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

    fetch(`{{ route('values.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('tableBody').innerHTML = data;
      })
      .catch(error => console.error(error));
  }

  document.getElementById('searchInput').addEventListener('keyup', () => fetchData(1));
  document.querySelector('.input-group-btn button').addEventListener('click', () => fetchData(1));

  document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
      e.preventDefault();
      const page = new URL(e.target.closest('a').href).searchParams.get('page');
      fetchData(page);
    }
  });
</script>
@endsection
