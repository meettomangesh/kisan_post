@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.category.title') }}
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
                        <th>{{ trans('cruds.category.fields.id') }}</th>
                        <td>{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.category.fields.cat_image') }}</th>
                        <td><img src="{{ asset($category->cat_image_name) }}" alt="" width="50" height="50"></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.category.fields.cat_name') }}</th>
                        <td><b>{{ $category->cat_name }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.category.fields.cat_description') }}</th>
                        <td>{{ $category->cat_description }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.category.fields.status') }}</th>
                        <td><b>
                            @if($category->status == 1)
                                {{ trans('cruds.category.fields.active') }}
                            @else
                                {{ trans('cruds.category.fields.inactive') }}
                            @endif</b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.categories.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection