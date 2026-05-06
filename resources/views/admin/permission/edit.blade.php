@extends('admin.includes.layout')
@section('content')

<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-newspaper"></i>
    </div>
    <div class="header-title">
      <h1>Create permissions</h1>
      <small>Add permissions</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> Home</a></li>
        <li class="active">Edit per</li>
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
              <a class="btn btn-success" href="{{ route('permissions.index') }}">
                <i class="fa fa-table"></i> permissions Table
              </a>
            </div>
          </div>

          <div class="panel-body">
            <form class="col-sm-12" action="{{ route('permissions.update', $permission->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="row">

                <!-- Name -->
                <div class="form-group col-md-12 mb-3">
                  <label for="name">Name</label>
                  <div class="text-danger">{{ $errors->first('name') }}</div>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $permission->name) }}">
                </div>


              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button type="submit" class="btn btn-success">Update Permissions</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection