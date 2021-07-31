@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.banner.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.banners.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.id') }}</th>
                        <td>{{ $banner->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.image') }}</th>
                        <td><img src="{{ asset($banner->image_name) }}" alt="" width="50" height="50"></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.type') }}</th>
                        <td>
                            @if($banner->type == 1)
                                {{ trans('cruds.banner.fields.banner') }}
                            @else
                                {{ trans('cruds.banner.fields.slider_image') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.name') }}</th>
                        <td>{{ $banner->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.description') }}</th>
                        <td>{{ $banner->description ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.url') }}</th>
                        <td>{{ $banner->url ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.banner.fields.status') }}</th>
                        <td>{{ ($banner->status == 1) ? trans('cruds.banner.fields.active') : trans('cruds.banner.fields.inactive') }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.banners.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection