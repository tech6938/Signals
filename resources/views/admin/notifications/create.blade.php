@extends('admin.includes.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-bell"></i>
        </div>
        <div class="header-title">
            <h1>{{ __('messages.send_notification') }}</h1>
            <small>{{ __('messages.create_send_notifications') }}</small>
            <ol class="breadcrumb hidden-xs">
                <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                <li><a href="{{ route('notifications.index') }}">{{ __('messages.send_notification') }}</a></li>
            </ol>
        </div>
    </section>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="btn-group">
                            <a class="btn btn-primary" href="{{ route('notifications.index') }}">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('messages.back_to_notifications') }}
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('notifications.store') }}" method="POST" id="notificationForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">{{ __('messages.notification_title') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ old('title') }}" placeholder="Enter notification title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="send_to">{{ __('messages.send_to') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="send_to" name="send_to" required onchange="toggleUserSelection()">
                                            <option value="">{{ __('messages.select_recipient_type') }}</option>
                                            <option value="all" {{ old('send_to') == 'all' ? 'selected' : '' }}>{{ __('messages.all_users') }}</option>
                                            <option value="subscribers" {{ old('send_to') == 'subscribers' ? 'selected' : '' }}>{{ __('messages.subscribers_only') }}</option>
                                            <option value="non_subscribers" {{ old('send_to') == 'non_subscribers' ? 'selected' : '' }}>{{ __('messages.non_subscribers_only') }}</option>
                                            <option value="individual" {{ old('send_to') == 'individual' ? 'selected' : '' }}>{{ __('messages.individual_users') }}</option>
                                            <option value="package_subscribers" {{ old('send_to') == 'package_subscribers' ? 'selected' : '' }}>
                                                {{ __('messages.subscribers_of_package') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">{{ __('messages.notification_description') }} <span class="text-danger">*</span></label>
                                <!-- Editor Toolbar -->
                                <div class="editor-toolbar btn-group" role="group" aria-label="Editor toolbar">
                                    <button type="button" class="btn btn-default" onclick="edCmd('bold')"><i class="fa fa-bold"></i></button>
                                    <button type="button" class="btn btn-default" onclick="edCmd('italic')"><i class="fa fa-italic"></i></button>
                                    <button type="button" class="btn btn-default" onclick="edCmd('underline')"><i class="fa fa-underline"></i></button>
                                    <button type="button" class="btn btn-default" onclick="edCmd('insertUnorderedList')"><i class="fa fa-list-ul"></i></button>
                                    <button type="button" class="btn btn-default" onclick="edCmd('insertOrderedList')"><i class="fa fa-list-ol"></i></button>
                                    <button type="button" class="btn btn-default" onclick="insertLink()"><i class="fa fa-link"></i></button>
                                    <button type="button" class="btn btn-default" onclick="insertImageUrl()"><i class="fa fa-image"></i></button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>
                                        <ul class="dropdown-menu" style="min-width:180px;padding:6px 8px;">
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('grinning')">grinning</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('joy')">joy</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('heart_eyes')">heart_eyes</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('thumbsup')">thumbsup</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('pray')">pray</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('fire')">fire</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('tada')">tada</li>
                                            <li style="display:inline-block;padding:4px;cursor:pointer;" onclick="insertEmoji('bulb')">bulb</li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-default" onclick="edCmd('removeFormat')"><i class="fa fa-eraser"></i></button>
                                </div>
                                <div id="editor" class="form-control" contenteditable="true" style="min-height:200px; overflow:auto;">
                                    {!! old('description') !!}
                                </div>
                                <textarea id="description" name="description" style="display:none;">{{ old('description') }}</textarea>
                                <small class="help-block">You can include emojis, links, images, and formatting.</small>
                            </div>

                            <!-- Package Selector (for package_subscribers) -->
                            <div id="packageSelection" class="form-group" style="display:none;">
                                <label for="packageDropdown">{{ __('messages.select_package') }}</label>
                                <select class="form-control" id="packageDropdown" name="package_id">
                                    <option value="">{{ __('messages.select_package') }}</option>
                                    @foreach(\App\Models\Package::where('status', 1)->orderBy('name')->get() as $package)
                                        <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('messages.select_package_to_notify_subscribers') }}</small>
                            </div>

                            <!-- Individual User Selection -->
                            <div id="userSelection" class="form-group" style="display: none;">
                                <label for="userSearch">{{ __('messages.search_select_users') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="userSearch"
                                        placeholder="Type to search users (name or email)" onkeyup="handleSearchInput()">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="searchUsers()">
                                            <i class="fa fa-search"></i> {{ __('messages.search') }}
                                        </button>
                                    </span>
                                </div>
                                <div id="searchResults" class="mt-2" style="display: none;"></div>
                                <div id="selectedUsers" class="mt-2">
                                    <label>{{ __('messages.selected_users') }}</label>
                                    <div id="selectedUsersList" class="selected-users-container"></div>
                                </div>
                            </div>

                            <!-- Hidden input for selected user IDs -->
                            <input type="hidden" id="target_users" name="target_users" value="">

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i> {{ __('messages.send_notification') }}
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-default">{{ __('messages.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .selected-users-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        background-color: #f9f9f9;
    }
    .selected-user-item {
        display: inline-block;
        background-color: #337ab7;
        color: white;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 3px;
        position: relative;
    }
    .selected-user-item .remove-user {
        margin-left: 8px;
        cursor: pointer;
        font-weight: bold;
    }
    .search-result-item {
        padding: 8px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    .search-result-item:hover {
        background-color: #f5f5f5;
    }
    .search-result-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<script>
    let selectedUsers = [];

    function toggleUserSelection() {
        const sendTo = document.getElementById('send_to').value;
        const userSelection = document.getElementById('userSelection');
        const packageSelection = document.getElementById('packageSelection');

        // Hide all
        userSelection.style.display = 'none';
        if (packageSelection) packageSelection.style.display = 'none';

        // Show relevant section
        if (sendTo === 'individual') {
            userSelection.style.display = 'block';
        } else if (sendTo === 'package_subscribers') {
            packageSelection.style.display = 'block';
        }

        // Clear users if not individual
        if (sendTo !== 'individual') {
            selectedUsers = [];
            updateSelectedUsersDisplay();
            updateHiddenInput();
        }
    }

    function searchUsers() {
        const query = document.getElementById('userSearch').value.trim();
        if (query.length < 2) {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        const resultsDiv = document.getElementById('searchResults');
        resultsDiv.innerHTML = '<p class="text-muted">Searching...</p>';
        resultsDiv.style.display = 'block';

        fetch(`/test-notification-search?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => displaySearchResults(data))
        .catch(err => {
            resultsDiv.innerHTML = '<p class="text-danger">Error searching users.</p>';
        });
    }

    function displaySearchResults(users) {
        const resultsDiv = document.getElementById('searchResults');
        if (users.length === 0) {
            resultsDiv.innerHTML = '<p class="text-muted">No users found.</p>';
        } else {
            let html = '';
            users.forEach(user => {
                const isSelected = selectedUsers.some(u => u.id === user.id);
                const disabledClass = isSelected ? 'disabled' : '';
                html += `
                    <div class="search-result-item ${disabledClass}" onclick="selectUser(${user.id}, '${user.text}', '${user.email}', '${user.type}')">
                        <strong>${user.text}</strong>
                        ${isSelected ? '<small class="text-muted">(Selected)</small>' : ''}
                    </div>`;
            });
            resultsDiv.innerHTML = html;
        }
        resultsDiv.style.display = 'block';
    }

    function selectUser(id, text, email, type) {
        if (selectedUsers.some(u => u.id === id)) return;
        selectedUsers.push({ id, text, email, type });
        updateSelectedUsersDisplay();
        updateHiddenInput();
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('userSearch').value = '';
    }

    function removeUser(userId) {
        selectedUsers = selectedUsers.filter(u => u.id !== userId);
        updateSelectedUsersDisplay();
        updateHiddenInput();
    }

    function updateSelectedUsersDisplay() {
        const container = document.getElementById('selectedUsersList');
        container.innerHTML = selectedUsers.length === 0
            ? '<p class="text-muted">No users selected.</p>'
            : selectedUsers.map(u => `
                <span class="selected-user-item">
                    ${u.text}
                    <span class="remove-user" onclick="removeUser(${u.id})">×</span>
                </span>`).join('');
    }

    function updateHiddenInput() {
        document.getElementById('target_users').value = selectedUsers.map(u => u.id).join(',');
    }

    function handleSearchInput() {
        const query = document.getElementById('userSearch').value.trim();
        if (query.length >= 2) searchUsers();
        else document.getElementById('searchResults').style.display = 'none';
    }

    // Editor Commands
    function edCmd(cmd, value = null) {
        document.execCommand(cmd, false, value);
        document.getElementById('editor').focus();
    }
    function insertLink() {
        const url = prompt('Enter URL:');
        if (url) document.execCommand('createLink', false, url.startsWith('http') ? url : 'https://' + url);
    }
    function insertImageUrl() {
        const url = prompt('Enter image URL:');
        if (url) document.execCommand('insertImage', false, url);
    }
    function insertEmoji(char) {
        document.execCommand('insertText', false, char);
    }

    // Sync editor to hidden field
    document.getElementById('notificationForm').addEventListener('submit', function() {
        document.getElementById('description').value = document.getElementById('editor').innerHTML;
    });

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        const sendTo = '{{ old("send_to") }}';
        if (sendTo) {
            document.getElementById('send_to').value = sendTo;
            toggleUserSelection();
        }

        // Enter to search
        document.getElementById('userSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchUsers();
            }
        });
    });
</script>
@endsection