@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-newspaper"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.edit_news') }}</h1>
      <small>{{ __('messages.edit_news') }} </small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.edit_news') }}</li>
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
              <a class="btn btn-success" href="{{ route('news.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.add_news') }}
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="row">

                <!-- Title -->
                <div class="form-group col-md-6 mb-3">
                  <label for="title">Title</label>
                  <div class="text-danger">{{ $errors->first('title') }}</div>
                  <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $news->title) }}">
                </div>

                <!-- URL -->
                <div class="form-group col-md-6 mb-3">
                  <label for="url">{{ __('messages.external_url') }}</label>
                  <div class="text-danger">{{ $errors->first('url') }}</div>
                  <input type="text" class="form-control" id="url" name="url" value="{{ old('url', $news->url) }}">
                </div>
                <div class="form-group col-md-6 mb-3">
                  <label for="date">{{ __('messages.date') }}</label>
                  <div class="text-danger">{{ $errors->first('date') }}</div>
                  <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $news->date) }}">
                </div>
                <div class="form-group col-md-6 mb-3">
                  <label for="time">{{ __('messages.time') }}</label>
                  <div class="text-danger">{{ $errors->first('time') }}</div>
<input type="time" 
       class="form-control" 
       id="time" 
       name="time" 
       value="{{ old('time', \Carbon\Carbon::parse($news->time)->format('H:i')) }}">
                </div>

                <!-- Image -->
                <div class="form-group col-md-6 mb-3">
                  <label for="image">{{ __('messages.upload_image') }}</label>
                  <div class="text-danger">{{ $errors->first('image') }}</div>
                  <input type="file" class="form-control" id="image" name="image" onchange="previewImage(event)">
                  <div id="image_preview" class="mt-2">
                    @if($news->image)
                    <img src="{{ asset('storage/' . $news->image) }}" width="100px" alt="News Image">
                    @endif
                  </div>
                </div>

                <script>
                  function previewImage(event) {
                    var reader = new FileReader();
                    reader.onload = function() {
                      var output = document.getElementById('image_preview');
                      output.innerHTML = '<img src="' + reader.result + '" width="100px" alt="News Image">';
                    };
                    reader.readAsDataURL(event.target.files[0]);
                  }
                </script>

                <!-- Status -->
                <div class="form-group col-md-6 mb-3">
                  <label for="status">{{ __('messages.status') }}</label>
                  <div class="text-danger">{{ $errors->first('status') }}</div>
                  <select class="form-control" id="status" name="status">
                    <option value="">{{ __('messages.select_status') }}</option>
                    <option value="1" {{ old('status', $news->status) == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="0" {{ old('status', $news->status) == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                  </select>
                </div>
<!-- Audience Type -->
<div class="form-group col-md-6 mb-3">
  <label for="audience_type">{{ __('messages.send_to') }} <span class="text-danger">*</span></label>
  <div class="text-danger">{{ $errors->first('audience_type') }}</div>
  <select class="form-control" name="audience_type" id="audience_type" onchange="toggleAudienceField()">
    <option value="all" {{ old('audience_type', $news->audience_type) == 'all' ? 'selected' : '' }}>{{ __('messages.all_users') }}</option>
    <option value="subscribers" {{ old('audience_type', $news->audience_type) == 'subscribers' ? 'selected' : '' }}>{{ __('messages.subscribers_only') }}</option>
    <option value="package" {{ old('audience_type', $news->audience_type) == 'package' ? 'selected' : '' }}>{{ __('messages.subscribers_of_package') }}</option>
  </select>
</div>

<!-- Package Dropdown (Shown only if audience_type == package) -->
<div class="form-group col-md-6 mb-3" id="packageField" style="display: none;">
  <label for="package_id">{{ __('messages.select_package') }} <span class="text-danger">*</span></label>
  <div class="text-danger">{{ $errors->first('package_id') }}</div>
  <select class="form-control" name="package_id" id="package_id">
    <option value="">{{ __('messages.select_package') }}</option>
    @foreach ($packages as $package)
      <option value="{{ $package->id }}" {{ old('package_id', $news->package_id) == $package->id ? 'selected' : '' }}>
        {{ $package->name }}
      </option>
    @endforeach
  </select>
</div>

                <!-- Description -->
                <div class="form-group col-md-12 mb-3">
                  <label for="description">{{ __('messages.description') }}</label>
                  <div class="text-danger">{{ $errors->first('description') }}</div>
                  <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $news->description) }}</textarea>
                </div>

              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.update_news') }}</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
function toggleAudienceField() {
    const audienceType = document.getElementById('audience_type').value;
    const packageField = document.getElementById('packageField');

    if (audienceType === 'package') {
        packageField.style.display = 'block';
    } else {
        packageField.style.display = 'none';
    }
}

// Auto-run on page load to handle old or existing values
document.addEventListener('DOMContentLoaded', function () {
    toggleAudienceField();
});
</script>

@endsection