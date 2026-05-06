@extends('admin.includes.layout')

@section('content')

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-plus-circle"></i>
        </div>
        <div class="header-title">
            <h1>{{ __('messages.add_value_subscription') }}</h1>
            <small>{{ __('messages.create_new_value_subscription') }}</small>
            <ol class="breadcrumb hidden-xs">
                <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                <li class="active">{{ __('messages.add_value_subscription') }}</li>
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
                            <a class="btn btn-success" href="{{ route('admin.value-subscriptions.index') }}">
                                <i class="fa fa-table"></i> {{ __('messages.value_subscriptions_table') }}
                            </a>
                        </div>
                    </div>

                    
                    <div class="panel-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('admin.value-subscriptions.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- User -->
                                <div class="form-group col-md-6">
                                    <label for="user_id">{{ __('messages.user') }}</label>
                                    <select name="user_id" id="user_id" class="form-control select2" required>
                                        <option value="">{{ __('messages.select_user') }}</option>
                                        @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->f_name }} {{ $u->last_name }} ({{ $u->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger">{{ $errors->first('user_id') }}</div>
                                </div>

                                <!-- Package -->
                                <div class="form-group col-md-6">
                                    <label for="package_id">{{ __('messages.value_service') }} <small class="text-muted">(Only purchased packages)</small></label>
                                    <select name="package_id" id="package_id" class="form-control" required disabled>
                                        <option value="">Please select a user first</option>
                                    </select>
                                    <div class="text-danger">{{ $errors->first('package_id') }}</div>
                                    <div id="package-warning" class="alert alert-warning" style="display: none; margin-top: 10px;">
                                        <strong><i class="fa fa-exclamation-triangle"></i> Warning:</strong> <span id="warning-text"></span>
                                    </div>
                                    <small class="help-block text-info">
                                        <i class="fa fa-info-circle"></i> Only packages that the user has purchased (paid for) will be available for assignment.
                                    </small>
                                </div>
                            </div>

                            <div class="col-sm-12 reset-button">
                                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                                <button type="submit" class="btn btn-success">{{ __('messages.save_subscription') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        

    </section>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for user select box
    $('#user_id').select2({
        placeholder: "{{ __('messages.select_user') }}",
        allowClear: true,
        width: '100%'
    });

    // Handle user selection change
    $('#user_id').on('change', function() {
        var userId = $(this).val();
        var packageSelect = $('#package_id');
        var warningDiv = $('#package-warning');
        var warningText = $('#warning-text');
        
        // Reset package select and warning
        packageSelect.empty().prop('disabled', true);
        warningDiv.hide();
        
        if (!userId) {
            packageSelect.append('<option value="">Please select a user first</option>');
            return;
        }
        
        // Show loading state
        packageSelect.append('<option value="">Loading purchased packages...</option>');
        
        // Fetch user's purchased packages
// Fetch user's purchased packages
$.ajax({
    url: '{{ route("admin.value-subscriptions.user-packages") }}',
    method: 'GET',
    data: { user_id: userId },
    success: function(response) {
        packageSelect.empty();

        if (response.length === 0) {
            packageSelect.append('<option value="">No purchased packages found</option>');
            warningDiv.show();
            warningText.text('This user has not purchased any packages yet.');
            return;
        }

        packageSelect.append('<option value="">Select a purchased package</option>');

        $.each(response, function(index, pkg) {
    let optionText = pkg.name + ' (' + pkg.status + ')';

    if (pkg.is_assigned) {
        optionText += ' - Already assigned';
        packageSelect.append('<option value="' + pkg.id + '" disabled>' + optionText + '</option>');
    } else {
        packageSelect.append('<option value="' + pkg.id + '">' + optionText + '</option>');
    }
});


        packageSelect.prop('disabled', false);
    },
    error: function(xhr) {
        packageSelect.empty();
        packageSelect.append('<option value="">Error loading packages</option>');
        console.error('Error loading packages:', xhr);
    }
});
    });

    // Handle package selection change
    $('#package_id').on('change', function() {
        var packageId = $(this).val();
        var warningDiv = $('#package-warning');
        var warningText = $('#warning-text');
        
        if (!packageId) {
            warningDiv.hide();
            return;
        }
        
        // Check if this package is already assigned to the user
        var userId = $('#user_id').val();
        if (userId) {
            // You can add additional validation here if needed
            warningDiv.hide();
        }
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        var userId = $('#user_id').val();
        var packageId = $('#package_id').val();
        
        if (!userId) {
            e.preventDefault();
            alert('Please select a user first.');
            return false;
        }
        
        if (!packageId) {
            e.preventDefault();
            alert('Please select a package to assign.');
            return false;
        }
        
        // Additional validation can be added here
        return true;
    });
});
</script>
@endsection
@endsection