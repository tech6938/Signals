@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="header-icon"><i class="fa fa-file-invoice"></i></div>
        <div class="header-title">
            <h1>{{ __('messages.invoices') }}</h1>
            <small>{{ __('messages.invoices_table') }}</small>
            <ol class="breadcrumb hidden-xs">
                <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                <li class="active">{{ __('messages.invoices') }}</li>
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
                            <a class="btn btn-primary" href="{{ route('invoices.create') }}">
                                <i class="fa fa-plus"></i> {{ __('messages.add_invoice') }}
                            </a>
                            <button class="btn btn-warning" onclick="checkSubscriptionExpiration()">
                                <i class="fa fa-bell"></i> Check Expiration
                            </button>
                        </div>
                    </div>

                    <div class="panel-body">
                        <!-- Search & Per Page -->
                        <div class="row panel-header mb-3">
                            <div class="col-sm-4">
                                <label>{{ __('messages.display') }}
                                    <select id="recordsPerPage" onchange="fetchData(1)">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
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

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.id') }}</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.phone') }}</th>
                                        <th>{{ __('messages.duration') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.pdf') }}</th>
                                        <th>End Date</th>
                                        <th>Warning Sent</th>
                                        <th>Expired Sent</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                        <th class="no-print">{{ __('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @include('admin.invoices.partials._table', ['invoices' => $invoices])
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end" id="paginationLinks">
                                {{ $invoices->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Fetch table data (AJAX)
    function fetchData(page = 1) {
    const query = document.getElementById('searchInput').value;
    const perPage = document.getElementById('recordsPerPage').value;

    fetch(`{{ route('invoices.index') }}?page=${page}&query=${query}&perPage=${perPage}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('tableBody').innerHTML = data;
    })
    .catch(error => console.error(error));
}

// Live search
document.getElementById('searchInput').addEventListener('keyup', () => fetchData(1));
document.querySelector('.input-group-btn button').addEventListener('click', () => fetchData(1));

// Pagination
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        const page = new URL(e.target.closest('a').href).searchParams.get('page');
        fetchData(page);
    }
});


    // Check subscription expiration
    function checkSubscriptionExpiration() {
        if (confirm('Are you sure you want to check subscription expiration? This will send notifications to users.')) {
            fetch('{{ route("invoices.check-expiration") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Subscription expiration check completed!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error checking subscription expiration');
            });
        }
    }
</script>
@endsection
