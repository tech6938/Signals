@extends('admin.includes.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="header-icon">
      <i class="fa fa-university"></i>
    </div>
    <div class="header-title">
      <h1>{{ __('messages.edit_bank_details') }}</h1>
      <small>{{ __('messages.update_bank_account_info') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.edit_bank') }}</li>
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
              <a class="btn btn-success" href="{{ route('bankDetails.index') }}">
                <i class="fa fa-table"></i> {{ __('messages.all_banks') }}
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

            <form action="{{ route('bankDetails.update', $bank->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="row">
                <!-- Bank Name -->
                <div class="form-group col-md-6">
                  <label>{{ __('messages.bank_name') }}</label>
                  <input type="text" name="bank_name" class="form-control" value="{{ $bank->bank_name }}" required>
                </div>

                <!-- Account Title -->
                <div class="form-group col-md-6">
                  <label>{{ __('messages.account_title') }}</label>
                  <input type="text" name="account_title" class="form-control" value="{{ $bank->account_title }}" required>
                </div>
                <div class="form-group col-md-6">
                  <label>{{ __('messages.account_number') }}</label>
                  <input type="text" name="account_number" class="form-control" value="{{ $bank->account_number }}" required>
                </div>

                <!-- IBAN -->
                <div class="form-group col-md-6">
                  <label>{{ __('messages.iban') }}</label>
                  <input type="text" name="iban" class="form-control" value="{{ $bank->iban }}">
                </div>

                <!-- Branch Code -->
                <div class="form-group col-md-6">
                  <label>{{ __('messages.branch_code') }}</label>
                  <input type="text" name="branch_code" class="form-control" value="{{ $bank->branch_code }}">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="is_active">{{ __('messages.status') }}</label>
                    <select name="is_active" id="is_active" class="form-control" required>
                        <option value="1" {{ old('is_active',$bank->is_active) == '1' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="0" {{ old('is_active',$bank->is_active) == '0' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                    <div class="text-danger">{{ $errors->first('is_active') }}</div>
                </div>

                <!-- Description -->
                <div class="form-group col-md-12">
                  <label>{{ __('messages.description') }}</label>
                  <textarea name="description" class="form-control" rows="4" placeholder="{{ __('messages.enter_description') }}">{{ $bank->description }}</textarea>
                </div>
                
              </div>

              <div class="col-sm-12 reset-button">
                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.update') }}</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection
