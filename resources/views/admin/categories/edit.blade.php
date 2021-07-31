@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.category.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" id="edit-category" action="{{ route("admin.categories.update", [$category->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cat_parent_id">{{ trans('cruds.product.fields.category') }}</label>
                        <div class="col-md-8 float-right">
                            <select class="form-control select2 {{ $errors->has('category') ? 'is-invalid' : '' }}" name="cat_parent_id" id="cat_parent_id">
                                @foreach($categories as $id => $cat)
                                    <option value="{{ $id }}" {{ $category->cat_parent_id == $id ? 'selected' : '' }}>{{ $cat }}</option>
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
                            <input class="form-control {{ $errors->has('cat_name') ? 'is-invalid' : '' }}" type="text" name="cat_name" id="cat_name" maxlength="50" value="{{ old('cat_name', $category->cat_name) }}" required>
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
                            <textarea class="form-control {{ $errors->has('cat_description') ? 'is-invalid' : '' }}" rows="2" name="cat_description" id="cat_description">{{ old('cat_description', $category->cat_description) }}</textarea>
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
                            <input type="file" name="cat_image_name" class="cat_image_name" id="cat_image_name" accept="image/*" disabled required/>
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
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" {{ $category->status == '1' ? 'checked' : '' }} required> {!! trans('cruds.category.fields.active') !!}</label>
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" {{ $category->status == '0' ? 'checked' : '' }} required> {!! trans('cruds.category.fields.inactive') !!}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row category-image-div">
                <div class="col-md-8">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-offset-3">
                            <div class="files-table table-container">
                                <table role="presentation" class="table table-striped table-bordered clearfix table-border-separate" id="image-preview-table">
                                    <thead>
                                        <tr>
                                            <th width="1%" class="text-center">#</th>
                                            <th class="text-center">{{ trans('cruds.category.fields.preview') }}</th>
                                            <th class="text-center">{{ trans('cruds.category.fields.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="files" id="dvPreview">
                                        <tr class="row-for-blank" id="blank-row-{{ $category->id }}">
                                            <td class="text-center">1</td>
                                            <td class="text-center"><img src="{{ asset($category->cat_image_name) }}" alt="" width="60" height="60"></td>
                                            <td class="text-center"><span class="btn red" id="remove-btn" data-image-id="{{ $category->id }}">Remove</span></td>
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