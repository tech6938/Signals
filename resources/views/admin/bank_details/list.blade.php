@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-university"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.bank_details') }}</h1>
      <small>{{ __('messages.manage_bank_accounts') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li>
          <a href="{{ route('dashboard') }}">
            <i class="pe-7s-home"></i> {{ __('messages.home') }}
          </a>
        </li>
        <li class="active">{{ __('messages.bank_details') }}</li>
      </ol>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">

          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-primary" href="{{ route('bankDetails.create') }}">
                <i class="fa fa-plus"></i> {{ __('messages.add_bank_detail') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            {{-- Filter + Search --}}
            <div class="row panel-header mb-3">
              <div class="col-sm-4">
                <label>
                  {{ __('messages.display') }}
                  <select id="recordsPerPage" onchange="fetchData(1)">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                  {{ __('messages.records_per_page') }}
                </label>
              </div>
              <div class="col-sm-4 text-center"></div>
              <div class="col-sm-4">
                <div class="input-group">
                  <input type="search" id="searchInput" class="form-control" placeholder="{{ __('messages.search_placeholder') }}">
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
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.bank_name') }}</th>
                    <th>{{ __('messages.account_title') }}</th>
                    <th>{{ __('messages.iban') }}</th>
                    <th>{{ __('messages.swift_code') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th class="no-print">{{ __('messages.action') }}</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($banks as $bank)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $bank->bank_name }}</td>
                    <td>{{ $bank->account_title }}</td>
                    <td>{{ $bank->iban }}</td>
                    <td>{{ $bank->swift_code ?? __('messages.not_available') }}</td>
                    <td>
                      @if($bank->is_active)
                        <span class="badge badge-success">{{ __('messages.active') }}</span>
                      @else
                        <span class="badge badge-secondary">{{ __('messages.inactive') }}</span>
                      @endif
                    </td>
                    <td class="no-print">
                      
                      
                      {{-- Edit --}}
                      <a href="{{ route('bankDetails.edit', $bank->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-pencil"></i>
                      </a>

                      {{-- Delete --}}
                      <form action="{{ route('bankDetails.destroy', $bank->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                          onclick="return confirm('{{ __('messages.confirm_delete_bank') }}')">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center">{{ __('messages.no_bank_details_found') }}</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>
              </table>

              <div class="d-flex justify-content-end" id="paginationLinks">
                {{ $banks->links() }}
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

{{-- AJAX Filter/Search --}}
<script>
  function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;

    fetch(`{{ route('bankDetails.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(data => { document.getElementById('tableBody').innerHTML = data; })
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
