@extends('admin.includes.layout')

@section('content')

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-university"></i>
        </div>
        <div class="header-title">
            <h1>{{ __('messages.bank_details') }}</h1>
            <small>{{ __('messages.add_manage_bank_info') }}</small>
            <ol class="breadcrumb hidden-xs">
                <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                <li class="active">{{ __('messages.bank_details') }}</li>
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
                                <i class="fa fa-table"></i> {{ __('messages.bank_details_table') }}
                            </a>
                        </div>
                    </div>

                    <div class="panel-body">
                        {{-- Show Validation Errors --}}
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Create Bank Detail Form --}}
                        <form action="{{ route('bankDetails.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- Bank Name -->
                                <div class="form-group col-md-6">
                                    <label for="bank_name">{{ __('messages.bank_name') }}</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control"
                                        placeholder="{{ __('messages.enter_bank_name') }}" value="{{ old('bank_name') }}" required>
                                    <div class="text-danger">{{ $errors->first('bank_name') }}</div>
                                </div>

                                <!-- Account Title -->
                                <div class="form-group col-md-6">
                                    <label for="account_title">{{ __('messages.account_title') }}</label>
                                    <input type="text" name="account_title" id="account_title" class="form-control"
                                        placeholder="{{ __('messages.enter_account_title') }}" value="{{ old('account_title') }}" required>
                                    <div class="text-danger">{{ $errors->first('account_title') }}</div>
                                </div>
                                <!-- account_number -->
                                <div class="form-group col-md-6">
                                    <label for="account_title">{{ __('messages.account_number') }}</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control"
                                        placeholder="{{ __('messages.account_number') }}" value="{{ old('account_number') }}" required>
                                    <div class="text-danger">{{ $errors->first('account_number') }}</div>
                                </div>

                                <!-- IBAN -->
                                <div class="form-group col-md-6">
                                    <label for="iban">{{ __('messages.iban') }}</label>
                                    <input type="text" name="iban" id="iban" class="form-control"
                                        placeholder="{{ __('messages.enter_iban') }}" value="{{ old('iban') }}" required>
                                    <div class="text-danger">{{ $errors->first('iban') }}</div>
                                </div>

                                <!-- Swift Code -->
                                <div class="form-group col-md-6">
                                    <label for="swift_code">{{ __('messages.swift_code') }}</label>
                                    <input type="text" name="swift_code" id="swift_code" class="form-control"
                                        placeholder="{{ __('messages.enter_swift_code') }}" value="{{ old('swift_code') }}">
                                    <div class="text-danger">{{ $errors->first('swift_code') }}</div>
                                </div>
                                
                                 <div class="form-group col-md-6">
                                    <label for="is_active">{{ __('messages.status') }}</label>
                                    <select name="is_active" id="is_active" class="form-control" required>
                                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                            {{ __('messages.active') }}
                                        </option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                            {{ __('messages.inactive') }}
                                        </option>
                                    </select>
                                    <div class="text-danger">{{ $errors->first('is_active') }}</div>
                                </div>

                                <!-- Description -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('messages.description') }}</label>
                                    <textarea name="description" class="form-control" rows="4"
                                        placeholder="{{ __('messages.enter_description') }}"></textarea>
                                </div>

                                <!-- Status -->
                               
                            </div>

                            <div class="col-sm-12 reset-button">
                                <button type="reset" class="btn btn-warning">{{ __('messages.reset') }}</button>
                                <button type="submit" class="btn btn-success">{{ __('messages.save_bank_details') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
