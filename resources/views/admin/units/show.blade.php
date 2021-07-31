@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.unit.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.unit.fields.id') }}</th>
                        <td>{{ $unit->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.unit.fields.category') }}</th>
                        <td><b>{{ $unit->category->cat_name ?? '' }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.unit.fields.unit') }}</th>
                        <td><b>{{ $unit->unit }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.unit.fields.description') }}</th>
                        <td>{{ $unit->description }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.unit.fields.status') }}</th>
                        <td><b>
                            @if($unit->status == 1)
                                {{ trans('cruds.unit.fields.active') }}
                            @else
                                {{ trans('cruds.unit.fields.inactive') }}
                            @endif</b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection