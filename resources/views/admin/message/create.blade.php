@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa-solid fa-message"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.create_message') }}</h1>
      <small>{{ __('messages.add_new_message') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.messages') }}</li>
      </ol>
    </div>
  </section>
@if ($errors->any()) <div class="alert alert-danger"> <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul> </div> @endif
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
          <div class="panel-heading">
            <div class="btn-group">
              <a class="btn btn-success" href="{{ route('userMessages.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.messages_table') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('userMessages.store') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <div class="row">

                <!-- Title -->
                <div class="form-group col-md-6 mb-3">
                  <label for="title">{{ __('messages.title') }}</label>
                  <div class="text-danger">{{ $errors->first('title') }}</div>
                  <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">
                </div>


                <!-- Status -->
                <div class="form-group col-md-6 mb-3">
                  <label for="status">{{ __('messages.status') }}</label>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                  <select class="form-control" id="status" name="status">
                    <option value="">Select status</option>
                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                  </select>
                </div>

                <!-- Description -->
                <div class="form-group col-md-12 mb-3">
                  <label for="description">{{ __('messages.description') }}</label>
                  <div class="text-danger">{{ $errors->first('description') }}</div>
                  <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>
                
                <div class="form-group col-md-6 mb-3">
                  <label for="date">{{ __('messages.date') }}</label>
                  <div class="text-danger">{{ $errors->first('date') }}</div>
                  <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}">
                </div>
                <div class="form-group col-md-6 mb-3">
                  <label for="time">{{ __('messages.time') }}</label>
                  <div class="text-danger">{{ $errors->first('time') }}</div>
                  <input type="time" class="form-control" id="time" name="time" value="{{ old('time') }}">
                </div>

                <!-- Image upload for the message -->
                <div class="form-group col-md-12 mb-3">
                  <label for="image">{{ __('messages.attach_image_optional') }}</label>
                  <input type="file" class="form-control" id="image" name="image" accept="image/*">
                  <small class="text-muted">{{ __('messages.max_2mb') }}</small>
                </div>

                <!-- Notification Section -->
                <div class="form-group col-md-12 mb-3">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4><i class="fa fa-bell"></i> {{ __('messages.send_notification_optional') }}</h4>
                    </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="send_notification">{{ __('messages.send_notification') }}</label>
                            <select class="form-control" id="send_notification" name="send_notification" onchange="toggleNotificationOptions()">
                              <option value="0" {{ old('send_notification') == '0' ? 'selected' : '' }}>No</option>
                              <option value="1" {{ old('send_notification') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="notification_send_to">{{ __('messages.send_to') }}</label>
                            <select class="form-control" id="notification_send_to" name="notification_send_to" onchange="toggleUserSelection()">
                              <option value="">{{ __('messages.select_recipient_type') }}</option>
                              <option value="all" {{ old('notification_send_to') == 'all' ? 'selected' : '' }}>{{ __('messages.all_users') }}</option>
                              <option value="subscribers" {{ old('notification_send_to') == 'subscribers' ? 'selected' : '' }}>{{ __('messages.subscribers_only') }}</option>
                              <option value="non_subscribers" {{ old('notification_send_to') == 'non_subscribers' ? 'selected' : '' }}>{{ __('messages.non_subscribers_only') }}</option>
                              <!--<option value="staff" {{ old('notification_send_to') == 'staff' ? 'selected' : '' }}>Staff Only</option>-->
                              <option value="individual" {{ old('notification_send_to') == 'individual' ? 'selected' : '' }}>{{ __('messages.individual_users') }}</option>
<option value="package_subscribers" {{ old('notification_send_to') == 'package_subscribers' ? 'selected' : '' }}>
  {{ __('messages.subscribers_of_package') }}
</option>

                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- Value (Service) selector for value_subscribers -->
                     
<!-- Package selector for package_subscribers -->
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


                      <!-- Individual User Selection (hidden by default) -->
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

                        <div id="searchResults" class="mt-2" style="display: none;">
                          <!-- Search results will be populated here -->
                        </div>

                        <div id="selectedUsers" class="mt-2">
                          <label>{{ __('messages.selected_users') }}</label>
                          <div id="selectedUsersList" class="selected-users-container">
                            <!-- Selected users will be displayed here -->
                          </div>
                        </div>
                      </div>

                      <!-- Hidden input to store selected user IDs -->
                      <input type="hidden" id="target_users" name="target_users[]" value="">
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.add_new_message') }}</button>
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

  function toggleNotificationOptions() {
    const sendNotification = document.getElementById('send_notification').value;
    const notificationPanel = document.querySelector('#userSelection').closest('.panel-body');
    
    if (sendNotification === '1') {
      notificationPanel.style.display = 'block';
    } else {
      notificationPanel.style.display = 'none';
      selectedUsers = [];
      updateSelectedUsersDisplay();
      updateHiddenInput();
    }
  }
function toggleUserSelection() {
  const sendTo = document.getElementById('notification_send_to').value;
  const userSelection = document.getElementById('userSelection');
  // Remove or comment out this line:
  // const valueSelection = document.getElementById('valueSelection');
  const packageSelection = document.getElementById('packageSelection');

  if (sendTo === 'individual') {
    userSelection.style.display = 'block';
    // valueSelection.style.display = 'none'; // remove this
    packageSelection.style.display = 'none';
  } else if (sendTo === 'value_subscribers') {
    userSelection.style.display = 'none';
    // valueSelection.style.display = 'block'; // remove or comment out
    packageSelection.style.display = 'none';
  } else if (sendTo === 'package_subscribers') {
    userSelection.style.display = 'none';
    // valueSelection.style.display = 'none'; // remove or comment out
    packageSelection.style.display = 'block';
  } else {
    userSelection.style.display = 'none';
    // valueSelection.style.display = 'none'; // remove or comment out
    packageSelection.style.display = 'none';
  }

  if (sendTo !== 'individual') {
    selectedUsers = [];
    updateSelectedUsersDisplay();
    updateHiddenInput();
  }
}



  function searchUsers() {
    const query = document.getElementById('userSearch').value.trim();
    console.log('Searching for:', query);

    if (query.length < 2) {
      document.getElementById('searchResults').style.display = 'none';
      return;
    }

    // Show loading state
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
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Search results:', data);
        displaySearchResults(data);
      })
      .catch(error => {
        console.error('Error searching users:', error);
        resultsDiv.innerHTML = '<p class="text-danger">Error searching users. Please try again.</p>';
        resultsDiv.style.display = 'block';
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
          ${isSelected ? '<small class="text-muted">(Already selected)</small>' : ''}
        </div>
      `;
      });
      resultsDiv.innerHTML = html;
    }

    resultsDiv.style.display = 'block';
  }

  function selectUser(id, text, email, type) {
    // Check if user is already selected
    if (selectedUsers.some(u => u.id === id)) {
      return;
    }

    selectedUsers.push({
      id,
      text,
      email,
      type
    });
    updateSelectedUsersDisplay();
    updateHiddenInput();

    // Hide search results
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

    if (selectedUsers.length === 0) {
      container.innerHTML = '<p class="text-muted">No users selected.</p>';
    } else {
      let html = '';
      selectedUsers.forEach(user => {
        html += `
        <span class="selected-user-item">
          ${user.text}
          <span class="remove-user" onclick="removeUser(${user.id})">&times;</span>
        </span>
      `;
      });
      container.innerHTML = html;
    }
  }

  function updateHiddenInput() {
    const targetUsersInput = document.getElementById('target_users');
    const userIds = selectedUsers.map(u => u.id);
    targetUsersInput.value = userIds.join(',');
  }

  // Handle search input
  function handleSearchInput() {
    const query = document.getElementById('userSearch').value.trim();
    console.log('Search input changed:', query);
    if (query.length >= 2) {
      searchUsers();
    } else {
      document.getElementById('searchResults').style.display = 'none';
    }
  }

  // Search on Enter key
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('userSearch').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        searchUsers();
      }
    });

    // Initialize the form based on old input
    const sendNotification = '{{ old("send_notification") }}';
    if (sendNotification === '1') {
      document.getElementById('send_notification').value = '1';
      toggleNotificationOptions();

      const sendTo = '{{ old("notification_send_to") }}';
      if (sendTo) {
        document.getElementById('notification_send_to').value = sendTo;
        toggleUserSelection();

        // Restore selected users from old input
        const targetUsers = '{{ old("target_users.0") }}';
        if (targetUsers && targetUsers !== '') {
          const userIds = targetUsers.split(',');
          // Note: We can't easily restore the full user objects from just IDs
          // The user will need to re-select users, but the form will remember the send_to type
        }
      }
    }
  });

  // Service dropdown selection - no additional JavaScript needed
  // The dropdown will automatically submit the selected value_id when the form is submitted
</script>
@endsection