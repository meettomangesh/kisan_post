@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.customers.title_singular') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.categories.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.customers.fields.id') }}</th>
                        <td>{{ $customer->id ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.customers.fields.name') }}</th>
                        <td>{{ $customer->first_name.' '.$customer->last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.customers.fields.mobile_number') }}</th>
                        <td>{{ $customer->mobile_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.customers.fields.email') }}</th>
                        <td>{{ $customer->email ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.customers.fields.status') }}</th>
                        <td><span class="{{ $customer->status == 1 ? 'btn btn-success':'btn btn-danger' }}"> {{ ($customer->status == 1 ?trans('cruds.customers.fields.active'):trans('cruds.customers.fields.inactive')) ?? '' }}</span></td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.customers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection