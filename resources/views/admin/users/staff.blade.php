@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-users"></i></div>
    <div class="header-title">
      <h1>Staff</h1>
      <small>{{ __('messages.staff_table') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">Staff</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <!-- <div class="btn-group">
              <a class="btn btn-primary" href="#">
                <i class="fa fa-plus"></i> Add User
              </a>
            </div> -->
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
                    <th> {{ __('messages.name') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <!-- <th>Status</th> -->
                    <th>{{ __('messages.staff') }}</th>
                    <th class="no-print">{{ __('messages.action') }}</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @section('tableBody')
                  @forelse($users as $user)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->f_name . ' '.$user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <!-- <td>
                      @if($user->status === 'active')
                      <span class="label label-success">Active</span>
                      @elseif($user->status === 'inactive')
                      <span class="label label-danger">Inactive</span>
                      @endif

                    </td> -->
                    <td>
                      @if(!empty($user->staff_type) && $user->staff_type == 'staff')

                      <span class="label label-warning">{{ $user->staff_type }}</span>
                      @else
                      N/A
                      @endif
                    </td>

                    <td class="no-print">
                      <!-- <a href="#"
                        class="editUser"
                        data-id="{{ $user->id }}"
                        data-status="{{ $user->status ? 'active' : 'inactive' }}"
                        data-toggle="modal"
                        data-target="#exampleModal">
                        <i class="fa fa-pencil" style="color:#007BFF;"></i>
                      </a> | -->
                      <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" style="border:none;background:none;padding:0;">
                          <i class="fa fa-trash-o" style="color:red;"></i>
                        </button>
                      </form>  |

                      @if(!empty($user->staff_type) && $user->staff_type === 'staff')
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                          <button class="btn btn-sm btn-primary" onclick="openRoleSidebar({{ $user->id }})">
                            <i class="fa fa-lock"></i> Assign Role
                          </button>
                        @endif
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8">{{ __('messages.no_records_found') }}</td>
                  </tr>
                  @endforelse
                  @show
                </tbody>
              </table>

              {{-- Pagination --}}
              <div class="d-flex pull-right" id="paginationLinks">
                {{ $users->links() }}
              </div>




              <!-- Modal -->
              <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.status') }}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="updateStatusForm">
                        @csrf
                        <input type="hidden" name="user_id" id="modal_user_id">

                        <div class="form-group">
                          <label for="status"> {{ __('messages.status') }}</label>
                          <select name="status" id="status" class="form-control">
                            <option value="">{{ __('messages.select_status') }}</option>
                            <option value="active">{{ __('messages.active') }}</option>
                            <option value="inactive">{{ __('messages.inactive') }}</option>
                          </select>
                        </div>
                      </form>


                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                      <button type="button" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- 🔹 Slide-in Role Sidebar -->
<div id="roleSidebar" style="
    position: fixed;
    top: 0;
    right: -400px;
    width: 400px;
    height: 100%;
    background: #fff;
    box-shadow: -2px 0 5px rgba(0,0,0,.3);
    transition: right 0.4s;
    padding: 20px;
    overflow-y: auto;
    z-index: 1050;
">
  <button onclick="closeRoleSidebar()" style="float:right;font-size:22px;background:none;border:none;">&times;</button>

  <h4 class="mb-3">{{ __('messages.assign_roles') }}</h4>
  <hr>
  <form id="roleForm">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" id="sidebar_user_id">

    <!-- ✅ Disabled input for full name -->
    <div class="form-group mb-3">
      <label>{{ __('messages.name') }}</label>
      <input type="text" id="sidebar_user_name" class="form-control" disabled>
    </div>

    <div class="form-group mb-3">
      <label>{{ __('messages.roles') }}</label>
      <div id="rolesList" style="
        display: flex;
        flex-wrap: wrap;
        gap: 15px;     /* space between checkboxes */
        align-items: center;
    "></div>
    </div>

    <button type="submit" class="btn btn-success mt-3">{{ __('messages.save_changes') }}</button>
  </form>
</div>



<script>
  function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;

    fetch(`{{ route('users.staff') }}?page=${page}&query=${query}&perPage=${perPage}`, {
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


  let selectedUserId = null;

  // When clicking the edit button
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.editUser');
    if (btn) {
      e.preventDefault();
      selectedUserId = btn.dataset.id;
      document.getElementById('modal_user_id').value = selectedUserId;
      document.getElementById('status').value = btn.dataset.status;
    }
  });

  // When clicking the Save changes button in modal
  document.querySelector('#exampleModal .btn-primary').addEventListener('click', function() {
    const status = document.getElementById('status').value;
    const token = document.querySelector('input[name="_token"]').value;

    fetch(`/users/${selectedUserId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          status
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(data.message);

          // ✅ Refresh the page
          location.reload();
        }
      })
      .catch(err => console.error(err));
  });



  function openRoleSidebar(userId) {
    fetch(`/users/${userId}/edit-roles`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        // ✅ Set hidden ID
        document.getElementById('sidebar_user_id').value = data.user.id;

        // ✅ Show full name in disabled input
        const fullName = (data.user.f_name ?? '') + ' ' + (data.user.last_name ?? '');
        document.getElementById('sidebar_user_name').value = fullName.trim();

        // ✅ Populate roles horizontally
        const list = document.getElementById('rolesList');
        list.innerHTML = '';
        
        if (data.roles.length === 0) {
          list.innerHTML = '<p class="text-muted">No roles available for assignment.</p>';
        } else {
          data.roles.forEach(role => {
            const checked = data.user_roles.includes(role.id) ? 'checked' : '';
            list.innerHTML += `
                <div class="form-check mb-0">
                    <input type="checkbox" name="roles[]" value="${role.id}" id="role-${role.id}" ${checked} class="form-check-input">
                    <label for="role-${role.id}" class="form-check-label">${role.name}</label>
                </div>
            `;
          });
        }

        // ✅ Show sidebar
        document.getElementById('roleSidebar').style.right = '0';
      });
  }

  function closeRoleSidebar() {
    document.getElementById('roleSidebar').style.right = '-400px';
  }

  document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('sidebar_user_id').value;
    const formData = new FormData(this);

    fetch(`/users/${userId}/update-roles`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        closeRoleSidebar();
      })
      .catch(err => console.error(err));
  });
</script>
@endsection