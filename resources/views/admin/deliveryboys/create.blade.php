@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.deliveryboy.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.deliveryboys.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.deliveryboy.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('first_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('first_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="last_name">{{ trans('cruds.deliveryboy.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', '') }}" required>
                @if($errors->has('last_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('last_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.last_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="mobile_number">{{ trans('cruds.deliveryboy.fields.mobile_number') }}</label>
                <input class="form-control {{ $errors->has('mobile_number') ? 'is-invalid' : '' }}" type="mobile_number" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}" required>
                @if($errors->has('mobile_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('mobile_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.mobile_number_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.deliveryboy.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="password">{{ trans('cruds.deliveryboy.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                @if($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.password_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="roles">{{ trans('cruds.deliveryboy.fields.roles') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required>
                    @foreach($roles as $id => $roles)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $roles }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <div class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.roles_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="regions">{{ trans('cruds.deliveryboy.fields.regions') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('regions') ? 'is-invalid' : '' }}" name="regions[]" id="regions"  required>
                    @foreach($regions as $id => $regions)
                        <option value="{{ $id }}" {{ in_array($id, old('regions', [])) ? 'selected' : '' }}>{{ $regions }}</option>
                    @endforeach
                </select>
                @if($errors->has('regions'))
                    <div class="invalid-feedback">
                        {{ $errors->first('regions') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.regions_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="status">{{ trans('cruds.deliveryboy.fields.status') }}</label>
                <div class="radio-list">
                        <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" checked required> {!! trans('cruds.deliveryboy.fields.active') !!}</label>
                        <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" required> {!! trans('cruds.deliveryboy.fields.inactive') !!}</label>
                </div>

                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.customers.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection