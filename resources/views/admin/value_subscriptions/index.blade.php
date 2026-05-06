@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="header-icon"><i class="fa fa-users"></i></div>
        <div class="header-title">
            <h1>{{ __('messages.value_subscriptions') }}</h1>
            <small>Subscriptions Table</small>
            <ol class="breadcrumb hidden-xs">
                <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                <li class="active">{{ __('messages.value_subscriptions') }}</li>
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
                            <a class="btn btn-primary" href="{{ route('admin.value-subscriptions.create') }}">
                                <i class="fa fa-plus-circle"></i> {{ __('messages.add_subscription') }}
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

                            <div class="col-sm-4 text-center">
                                <div class="input-group">
                                    <input type="text" id="serviceInput" class="form-control" placeholder="Type package name..">
                                    <input type="hidden" id="serviceId">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="clearService">{{ __('messages.clear') }}</button>
                                    </span>
                                </div>
                                <small id="subscriberCountLabel" class="text-muted"></small>
                                <div id="serviceSuggestions" class="list-group" style="position:absolute; z-index:1000; width:100%; display:none;"></div>
                            </div>

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
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.subscribed_packages') }}</th>
                                        <th class="no-print">{{ __('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @include('admin.value_subscriptions.partials._table_body')
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            @include('admin.value_subscriptions.partials._pagination')
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
        const packageId = document.getElementById('serviceId').value || '';

        fetch(`{{ route('admin.value-subscriptions.index') }}?page=${page}&query=${query}&perPage=${perPage}&package_id=${packageId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.tableBody) document.getElementById('tableBody').innerHTML = data.tableBody;
            if (data.paginationLinks) document.getElementById('paginationLinks').outerHTML = data.paginationLinks;
            if (typeof data.subscriberCount !== 'undefined')
                document.getElementById('subscriberCountLabel').textContent = `Subscribers: ${data.subscriberCount}`;
        })
        .catch(console.error);
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

    // Autocomplete for packages
    const serviceInput = document.getElementById('serviceInput');
    const serviceId = document.getElementById('serviceId');
    const suggestions = document.getElementById('serviceSuggestions');
    const clearBtn = document.getElementById('clearService');

    let debounceTimer;
    serviceInput.addEventListener('keyup', function() {
        const q = serviceInput.value.trim();
        clearTimeout(debounceTimer);
        if (q.length < 1) { suggestions.style.display = 'none'; return; }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('admin.value-subscriptions.searchValues') }}?q=${encodeURIComponent(q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.json())
                .then(items => {
                    suggestions.innerHTML = '';
                    items.forEach(it => {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.className = 'list-group-item list-group-item-action';
                        a.textContent = it.text;
                        a.dataset.id = it.id;
                        a.addEventListener('click', (e) => {
                            e.preventDefault();
                            serviceInput.value = it.text;
                            serviceId.value = it.id;
                            suggestions.style.display = 'none';
                            fetchData(1);
                        });
                        suggestions.appendChild(a);
                    });
                    suggestions.style.display = items.length ? 'block' : 'none';
                })
                .catch(() => { suggestions.style.display = 'none'; });
        }, 250);
    });

    clearBtn.addEventListener('click', function() {
        serviceInput.value = '';
        serviceId.value = '';
        document.getElementById('subscriberCountLabel').textContent = '';
        suggestions.style.display = 'none';
        fetchData(1);
    });
</script>
@endsection
