@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.pin_code.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pincodes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.pin_code.fields.id') }}
                        </th>
                        <td>
                            {{ $pincode->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pin_code.fields.pin_code') }}
                        </th>
                        <td>
                            {{ $pincode->pin_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pin_code.fields.country') }}
                        </th>
                        <td>
                            {{ $pincode->country->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pin_code.fields.state') }}
                        </th>
                        <td>
                            {{ $pincode->state->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pin_code.fields.city') }}
                        </th>
                        <td>
                            {{ $pincode->city->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pincodes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection