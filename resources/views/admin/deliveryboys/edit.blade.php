@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.deliveryboy.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.deliveryboys.update", [$deliveryboy->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.deliveryboy.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', $deliveryboy->first_name) }}" required>
                @if($errors->has('first_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('first_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="last_name">{{ trans('cruds.deliveryboy.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', $deliveryboy->last_name) }}" required>
                @if($errors->has('last_name'))
                <div class="invalid-feedback">
                    {{ $errors->first('last_name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.last_name_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="mobile_number">{{ trans('cruds.deliveryboy.fields.mobile_number') }}</label>
                <input class="form-control {{ $errors->has('mobile_number') ? 'is-invalid' : '' }}" type="mobile_number" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $deliveryboy->mobile_number) }}" readonly required>
                @if($errors->has('mobile_number'))
                <div class="invalid-feedback">
                    {{ $errors->first('mobile_number') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.mobile_number_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.deliveryboy.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="mobile_number" value="{{ old('email', $deliveryboy->email) }}" readonly required>
                @if($errors->has('email'))
                <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.deliveryboy.fields.email_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="password">{{ trans('cruds.deliveryboy.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password">
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
                    <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $deliveryboy->roles->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
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
                <select class="form-control select2 {{ $errors->has('regions') ? 'is-invalid' : '' }}" name="regions[]" id="regions" required>
                    @foreach($regions as $id => $regions)
                    <option value="{{ $id }}" {{ (in_array($id, old('regions', [])) || $deliveryboy->regions->contains($id)) ? 'selected' : '' }}>{{ $regions }}</option>
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
                    <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" {{ $deliveryboy->status == '1' ? 'checked' : '' }} required> {!! trans('cruds.deliveryboy.fields.active') !!}</label>
                    <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" {{ $deliveryboy->status == '0' ? 'checked' : '' }} required> {!! trans('cruds.deliveryboy.fields.inactive') !!}</label>
                </div>

                @if($errors->has('status'))
                <div class="invalid-feedback">
                    {{ $errors->first('status') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.customers.fields.status_helper') }}</span>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.user_photo') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->user_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                        <th><input type="file" name="user_photo" id="user_photo"></th>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.aadhar_photo') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->aadhar_card_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                        <th><input type="file" name="aadhar_card_photo" id="aadhar_card_photo"></th>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <!-- <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.pan_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.pan_photo') }}</th>
                    </tr>
                    <tr>
                        <th style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->pan_number:'' }}</th>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->pan_card_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody> -->
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.pan_photo') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->pan_card_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                        <th><input type="file" name="pan_card_photo" id="pan_card_photo"></th>
                </tbody>

            </table>

            <table class="table table-bordered table-striped">
                <!-- <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.license_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.license_card_photo') }}</th>
                    </tr>
                    <tr>
                        <td style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->license_number:'' }}</td>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->license_card_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody> -->
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.license_card_photo') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->license_card_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                        <th><input type="file" name="license_card_photo" id="license_card_photo"></th>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <!-- <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.vehicle_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.rc_book_photo') }}</th>
                    </tr>
                    <tr>
                        <td style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->vehicle_number:'' }}</td>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->rc_book_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody> -->
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.rc_book_photo') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->rc_book_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                        <th><input type="file" name="rc_book_photo" id="rc_book_photo"></th>
                </tbody>
            </table>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection