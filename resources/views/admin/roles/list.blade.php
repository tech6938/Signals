@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-chart-line"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.roles') }}</h1>
      <small>{{ __('messages.roles_table') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.roles') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="#">
                <i class="fa fa-plus" aria-hidden="true"></i> {{ __('messages.add_roles') }}
              </a>
            </div>
          </div>





          <div class="panel-body">
            <div class="row panel-header mb-3">
              <div class="col-sm-4">
                <label>{{ __('messages.display') }}
                  <select id="recordsPerPage" onchange="fetchData(1)">
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
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.guard_name') }}</th>
                    <!--<th class="no-print">{{ __('messages.action') }}</th>-->
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($roles as $role)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->guard_name }}</td>

                    <!--<td class="no-print">-->
                    <!--  <a href="{{ route('roles.edit', $role->id) }}"><i class="fa fa-pencil" style="color:#007BFF;"></i></a> |-->
                    <!--  <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">-->
                    <!--    @csrf-->
                    <!--    @method('DELETE')-->
                    <!--    <button type="submit" onclick="return confirm('Are you sure?')" style="border:none;background:none;padding:0;">-->
                    <!--      <i class="fa fa-trash-o" style="color:red;"></i>-->
                    <!--    </button>-->
                    <!--  </form>-->
                    <!--</td>-->
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8">{{ __('messages.no_records_found') }}</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>


              </table>

              <div class="d-flex justify-content-right" id="paginationLinks">
                {{ $roles->links() }}
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

    fetch(`{{ route('roles.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
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
</script>
@endsection