@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.category.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" id="create-category" action="{{ route("admin.categories.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cat_parent_id">{{ trans('cruds.product.fields.category') }}</label>
                        <div class="col-md-8 float-right">
                            <select class="form-control select2 {{ $errors->has('category') ? 'is-invalid' : '' }}" name="cat_parent_id" id="cat_parent_id">
                                @foreach($categories as $id => $category)
                                    <option value="{{ $id }}" {{ old('cat_parent_id') == $id ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($errors->has('category'))
                        <div class="invalid-feedback">
                            {{ $errors->first('category') }}
                        </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.product.fields.category_helper') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="cat_name">{{ trans('cruds.category.fields.cat_name') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('cat_name') ? 'is-invalid' : '' }}" type="text" name="cat_name" id="cat_name" maxlength="50" value="{{ old('cat_name', '') }}" required>
                            @if($errors->has('cat_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('cat_name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.category.fields.cat_name_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cat_description">{{ trans('cruds.category.fields.cat_description') }}</label>
                        <div class="col-md-8 float-right">
                            <textarea class="form-control {{ $errors->has('cat_description') ? 'is-invalid' : '' }}" rows="2" name="cat_description" id="cat_description">{{ old('cat_description', '') }}</textarea>
                            @if($errors->has('cat_description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('cat_description') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.category.fields.cat_description_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>
                
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required">{{ trans('cruds.category.fields.cat_image') }}</label>
                        <div class="col-md-8 float-right">
                            <input type="file" name="cat_image_name" class="cat_image_name" id="cat_image_name" accept="image/*" required/>
                            <span class="fileupload-process"></span>
                            <span id="file-error-container"></span>
                            <span class="help-block">{{ trans('cruds.category.fields.cat_image_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="status">{{ trans('cruds.category.fields.status') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="radio-list">
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" checked required> {!! trans('cruds.category.fields.active') !!}</label>
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" required> {!! trans('cruds.category.fields.inactive') !!}</label>
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
<script src="{{ asset('js/admin/category.js') }}"></script>
@endsection

@section('page-level-scripts')
<script src="{{ asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
@endsection

@section('scripts')
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.categoryJs.init();
    });
</script>
@endsection