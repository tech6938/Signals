@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-chart-line"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.assign') }}</h1>
      <small>{{ __('messages.assign_table') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.assign') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.guard_name') }}</th>
                    <th>{{ __('messages.action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($roles as $role)
                  <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->guard_name }}</td>
                    <td>
                       @if($role->name !== 'admin')
                        <button class="btn btn-sm btn-primary" onclick="openEditSidebar({{ $role->id }})">
                          <i class="fa fa-pencil"></i> Edit
                        </button>
                      @else
                        <span class="text-muted">Protected</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <div class="d-flex justify-content-end">
                {{ $roles->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Sidebar for editing role -->
<div id="editSidebar" style="
  position: fixed;
  top: 0;
  right: -500px;
  width: 400px;
  height: 100%;
  background: #fff;
  box-shadow: -2px 0 5px rgba(0,0,0,0.5);
  transition: right 0.4s;
  overflow-y: auto;
  z-index: 1050;
  padding: 20px;
  pointer-events: auto;
">
  <button onclick="closeEditSidebar()" style="float:right;font-size:24px;background:none;border:none;">&times;</button>
  <h4>Assign Permission</h4>
  <form id="editRoleForm">
    @csrf
    <input type="hidden" name="role_id" id="role_id">
    <div class="form-group">
      <label>Role Name</label>
      <input type="text" id="role_name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Permissions</label>
      <div id="permissionsList"></div>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
  </form>
</div>

<!-- JavaScript -->
<script>
  function openEditSidebar(roleId) {
    fetch(`/assaign/${roleId}/edit`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        document.getElementById('role_id').value = data.role.id;
        document.getElementById('role_name').value = data.role.name;

        const list = document.getElementById('permissionsList');
        list.innerHTML = '';

        data.permissions.forEach(permission => {
          const isChecked = data.role_permissions.includes(permission.id);
          list.innerHTML += `
            <div class="form-check mb-1">
              <input type="checkbox" class="form-check-input" id="perm-${permission.id}" name="permissions[]" value="${permission.id}" ${isChecked ? 'checked' : ''}>
              <label class="form-check-label" for="perm-${permission.id}">${permission.name}</label>
            </div>
          `;
        });

        document.getElementById('editSidebar').style.right = '0';
      })
      .catch(err => {
        console.error('Error fetching role data:', err);
        alert('Failed to load role data.');
      });
  }

  function closeEditSidebar() {
    document.getElementById('editSidebar').style.right = '-500px';
  }

document.getElementById('editRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const roleId = document.getElementById('role_id').value;
    const formData = new FormData(form);

    // Laravel requires method override for PUT
    formData.append('_method', 'PUT');

    fetch(`/assaign/${roleId}`, {
        method: 'POST', // method spoofing via _method=PUT
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        if (!res.ok) {
            const errData = await res.json();
            throw new Error(errData.message || 'Request failed');
        }
        return res.json();
    })
    .then(data => {
        alert(data.message); // ✅ success alert
        closeEditSidebar();
        location.reload(); // Optional: refresh to see updates
    })
    .catch(err => {
        console.error('Update failed:', err);
        alert('Error updating role. Please try again.');
    });
});

</script>
@endsection
