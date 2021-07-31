@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.banner.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" id="edit-banner" action="{{ route("admin.banners.update", [$banner->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="name">{{ trans('cruds.banner.fields.name') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $banner->name) }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.banner.fields.name_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="description">{{ trans('cruds.banner.fields.description') }}</label>
                        <div class="col-md-8 float-right">
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="2" name="description" id="description">{{ old('description', $banner->description) }}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.banner.fields.description_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="type">{{ trans('cruds.banner.fields.type') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="radio-list">
                                <label class="radio-inline"><input type="radio" name="type" value="{{ old('type', '1') }}" {{ $banner->type == '1' ? 'checked' : '' }} required> {!! trans('cruds.banner.fields.banner') !!}</label>
                                <label class="radio-inline"><input type="radio" name="type" value="{{ old('type', '2') }}" {{ $banner->type == '2' ? 'checked' : '' }} required> {!! trans('cruds.banner.fields.slider_image') !!}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="url">{{ trans('cruds.banner.fields.url') }}</label>
                        <div class="col-md-8 float-right">
                            <textarea class="form-control {{ $errors->has('url') ? 'is-invalid' : '' }}" rows="2" name="url" id="url" maxlength="1000">{{ old('url', $banner->url) }}</textarea>
                            @if($errors->has('url'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('url') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.banner.fields.url_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required">{{ trans('cruds.banner.fields.image') }}</label>
                        <div class="col-md-8 float-right">
                            <input type="file" name="image_name" class="image_name" id="image_name" accept="image/*" disabled required/>
                            <span class="fileupload-process"></span>
                            <span id="file-error-container"></span>
                            <span class="help-block">{{ trans('cruds.banner.fields.image_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="status">{{ trans('cruds.banner.fields.status') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="radio-list">
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" {{ $banner->status == '1' ? 'checked' : '' }} required> {!! trans('cruds.banner.fields.active') !!}</label>
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" {{ $banner->status == '0' ? 'checked' : '' }} required> {!! trans('cruds.banner.fields.inactive') !!}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row banner-image-div">
                <div class="col-md-8">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-offset-3">
                            <div class="files-table table-container">
                                <table role="presentation" class="table table-striped table-bordered clearfix table-border-separate" id="image-preview-table">
                                    <thead>
                                        <tr>
                                            <th width="1%" class="text-center">#</th>
                                            <th class="text-center">{{ trans('cruds.banner.fields.preview') }}</th>
                                            <th class="text-center">{{ trans('cruds.banner.fields.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="files" id="dvPreview">
                                        <tr class="row-for-blank" id="blank-row-{{ $banner->id }}">
                                            <td class="text-center">1</td>
                                            <td class="text-center"><img src="{{ asset($banner->image_name) }}" alt="" width="60" height="60"></td>
                                            <td class="text-center"><span class="btn red" id="remove-btn" data-image-id="{{ $banner->id }}">Remove</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

@section('template-level-scripts')
<script src="{{ asset('js/admin/banner.js') }}"></script>
@endsection

@section('page-level-scripts')
<script src="{{ asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
@endsection

@section('scripts')
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.bannerJs.init();
    });
</script>
@endsection