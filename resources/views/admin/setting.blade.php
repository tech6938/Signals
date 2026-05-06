@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-chart-line"></i></div>
    <div class="header-title">
      <h1>Signals</h1>
      <small>Signals Table</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> Home</a></li>
        <li class="active">Signals</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="{{ route('signals.create') }}">
                <i class="fa fa-plus"></i> Add Signal
              </a>
            </div>
          </div>

          <div class="panel-body">
            <div class="row panel-header mb-3">
              <div class="col-sm-4">
                <label>Display
                  <select id="recordsPerPage" onchange="fetchData(1)">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select> records per page
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

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Coin</th>
                    <th>B Price</th>
                    <th>TP1</th>
                    <th>TP2</th>
                    <th>Icon</th>
                    <th>Last Price</th>
                    <th>Status</th>
                    <th class="no-print">Action</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($signals as $signal)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $signal->coin_name }}</td>
                    <td>{{ rtrim(rtrim(number_format($signal->b_price, 2, '.', ''), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($signal->tp1, 2, '.', ''), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($signal->tp2, 2, '.', ''), '0'), '.') }}</td>
                    <td>
                      @if($signal->icon1)
                      <img src="{{ asset('storage/' . $signal->icon1) }}" alt="Icon1" width="40">
                      @else
                      N/A
                      @endif
                    </td>
                    <td>{{ rtrim(rtrim(number_format($signal->last_price, 2, '.', ''), '0'), '.') }}</td>
                    <td>{{ $signal->status ? 'Active' : 'Inactive' }}</td>
                    <td class="no-print">
                      <a href="{{ route('signals.edit', $signal->id) }}"><i class="fa fa-pencil" style="color:#007BFF;"></i></a> |
                      <form action="{{ route('signals.destroy', $signal->id) }}" method="POST" style="display:inline;">
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
                    <td colspan="8">No records found.</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>


              </table>

              <div class="d-flex justify-content-end" id="paginationLinks">
                {{ $signals->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- <script>
  function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;

    fetch(`{{ route('signals.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
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

  document.getElementById('searchInput').addEventListener('keyup', () => fetchData(1));
  document.querySelector('.input-group-btn button').addEventListener('click', () => fetchData(1));

  document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
      e.preventDefault();
      const page = new URL(e.target.closest('a').href).searchParams.get('page');
      fetchData(page);
    }
  });
</script> -->
@endsection