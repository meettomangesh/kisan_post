@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.region.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.regions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.region.fields.id') }}
                        </th>
                        <td>
                            {{ $region->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.region.fields.region_name') }}
                        </th>
                        <td>
                            {{ $region->region_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.region.fields.pin_codes') }}
                        </th>
                        <td>
                            @foreach($region->pin_codes as $key => $pin_codes)
                                <span class="label label-info">{{ $pin_codes->title }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.regions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection