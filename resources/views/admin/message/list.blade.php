@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-envelope"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.user_messages') }}</h1>
      <small>{{ __('messages.user_messages_table') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.messages') }}</li>
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
              <a class="btn btn-primary" href="{{ route('userMessages.create') }}">
                <i class="fa fa-plus" aria-hidden="true"></i> {{ __('messages.add_message') }}
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
                    <button class="btn btn-primary" type="button" onclick="fetchData(1)"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <!-- Messages Table -->
            <div class="table-responsive">
              <table id="printTable" class="table table-bordered table-hover">
                <thead class="success">
                  <tr>
                    <th>#</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.time') }}</th>
                    <th class="no-print">{{ __('messages.action') }}</th>
                  </tr>
                </thead>

                <!-- ------------- THIS SECTION WILL BE RETURNED FOR AJAX ------------- -->
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($userMessages as $msg)
                  <tr>
                    <td>
                      {{ $userMessages->firstItem() ? $userMessages->firstItem() + $loop->index : $loop->iteration }}
                    </td>
                    <td>{{ $msg->title }}</td>
                    <td>{{ Str::limit($msg->description, 30) }}</td>
                    <td>{{ $msg->status ? 'Active' : 'Inactive' }}</td>
                  <td>
    @php
        // Check and set date value
        if (!empty($msg->date) && $msg->date !== '0000-00-00') {
            try {
                $dateValue = \Carbon\Carbon::parse($msg->date)->format('Y-m-d');
            } catch (\Exception $e) {
                $dateValue = '-';
            }
        } else {
            $dateValue = '-';
        }
    @endphp
    {{ $dateValue }}
</td>

<td>
    @php
        // Check and set time value
        if (!empty($msg->time) && $msg->time !== '00:00:00') {
            try {
                $timeValue = \Carbon\Carbon::createFromFormat('H:i:s', $msg->time)->format('g:i A');
            } catch (\Exception $e) {
                $timeValue = '-';
            }
        } else {
            $timeValue = '-';
        }
    @endphp
    {{ $timeValue }}
</td>

                    <td class="no-print">
                      <!-- <a href="{{ route('userMessages.edit', $msg->id) }}"><i class="fa fa-pencil" style="color:#007BFF;"></i></a> | -->
                      <form action="{{ route('userMessages.destroy', $msg->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" style="border:none;background:none;padding:0;">
                          <i class="fa fa-trash-o" style="color:red;"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center">{{ __('messages.no_records_found') }}</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>
                <!-- ------------------------------------------------------------------ -->

              </table>

              <!-- ------------- PAGINATION SECTION ALSO RETURNED FOR AJAX ------------- -->
              @section('pagination')
              <div class="d-flex justify-content-end" id="paginationLinks">
                {{ $userMessages->links() }}
              </div>
              @show
              <!-- --------------------------------------------------------------------- -->

            </div> <!-- /.table-responsive -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div>
    </div>
  </section>
</div>

<!-- AJAX Script (wrapped to run after DOM loaded) -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    function fetchData(page = 1) {
      const query = encodeURIComponent(document.getElementById('searchInput').value || '');
      const perPage = encodeURIComponent(document.getElementById('recordsPerPage').value || 10);

      fetch(`{{ route('userMessages.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          // we expect JSON from the controller in AJAX branch
          return response.json().catch(() => null); // fallback
        })
        .then(json => {
          if (json && (json.table !== undefined || json.pagination !== undefined)) {
            // update rows and pagination
            if (json.table !== undefined) {
              document.getElementById('tableBody').innerHTML = json.table;
            }
            if (json.pagination !== undefined) {
              const p = document.getElementById('paginationLinks');
              if (p) p.innerHTML = json.pagination;
            }
            // update browser URL (optional)
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('query', decodeURIComponent(query));
            urlParams.set('perPage', decodeURIComponent(perPage));
            history.replaceState(null, '', '?' + urlParams.toString());
            return;
          }

          // If server returned plain HTML (older fallback), just replace tbody
          if (typeof json !== 'object') {
            // treat response as text and set table body
            document.getElementById('tableBody').innerHTML = json;
          }
        })
        .catch(err => {
          console.error('Fetch error', err);
        });
    }

    // listeners
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      let typingTimer;
      searchInput.addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => fetchData(1), 300); // debounce
      });
    }

    const recordsSelect = document.getElementById('recordsPerPage');
    if (recordsSelect) recordsSelect.addEventListener('change', () => fetchData(1));

    document.addEventListener('click', function(e) {
      const a = e.target.closest('.pagination a');
      if (a) {
        e.preventDefault();
        const page = new URL(a.href).searchParams.get('page') || 1;
        fetchData(page);
      }
    });
  });
</script>

@endsection