@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-file-pdf-o"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.generate_invoice') }}</h1>
      <small>Create a new Invoice</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.add_invoice') }}</li>
      </ol>
    </div>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-success" href="{ route('admin.package.purchases) }}">
                <i class="fa fa-table"></i> {{ __('messages.invoices_table') }}
              </a>
            </div>
          </div>
          @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          <div class="panel-body">
        <form action="{{ route('invoices.statusApproved') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- User Dropdown --}}
    <div class="form-group col-md-6 mb-3">
        <label>{{ __('messages.full_name') }}</label>
        <select name="user_id" id="userSelect" class="form-control select2" required>
            <option value="">{{ __('messages.select_user') }}</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->f_name }} {{ $user->last_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Package Dropdown --}}
    <div class="form-group col-md-6 mb-3">
        <label>{{ __('messages.package') }}</label>
        <select name="package_id" id="packageSelect" class="form-control" required>
            <option value="">{{ __('messages.select_package') }}</option>
        </select>
    </div>

    {{-- Screenshot Upload --}}
    <div class="form-group col-md-12 mb-3">
        <label for="image">{{ __('messages.attach_image_optional') }}</label>
        <input type="file" class="form-control" id="image" name="screenshot" accept="image/*">
        <small class="text-muted">{{ __('messages.max_2mb') }}</small>
    </div>

    <div class="col-sm-12 reset-button">
        <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
        <button type="submit" class="btn btn-success">{{ __('messages.submit') }}</button>
    </div>
</form>


          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{-- ✅ Script Section --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('#userSelect').select2({
      placeholder: "Select User",
      allowClear: true,
      width: '100%'
    });

    $('#userSelect').on('change', function() {
      const userId = $(this).val();

      if (userId) {
        $.ajax({
          url: `/user-packages/${userId}`,
          type: 'GET',
          success: function(response) {
            const packages = response.packages;
            const $pkgSelect = $('#packageSelect');

            $pkgSelect.empty().append('<option value="">Select Package</option>');

            packages.forEach(pkg => {
              let disabled = '';
              let statusBadge = '';

              if (!pkg.selectable && pkg.status) {
                const status = pkg.status.toLowerCase();
                if (status === 'pending') statusBadge = ' 🟡 (Pending)';
                if (status === 'approved') statusBadge = ' 🟢 (Approved)';
                if (status === 'rejected') statusBadge = ' 🔴 (Rejected)';
                disabled = 'disabled';
              }

              $pkgSelect.append(`
                            <option value="${pkg.id}" ${disabled}>
                                ${pkg.name}${statusBadge}
                            </option>
                        `);
            });
          },
          error: function() {
            alert('Error loading packages for this user.');
          }
        });
      } else {
        $('#packageSelect').empty().append('<option value="">Select Package</option>');
      }
    });
  });
</script>

@endsection