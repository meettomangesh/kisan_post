@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.unit.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.units.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3 required" for="cat_id">{{ trans('cruds.product.fields.category') }}</label>
                    <div class="col-md-4">
                        <select class="form-control select2 {{ $errors->has('category') ? 'is-invalid' : '' }}" name="cat_id" id="cat_id" required>
                            @foreach($categories as $id => $category)
                                <option value="{{ $id }}" {{ old('cat_id') == $id ? 'selected' : '' }}>{{ $category }}</option>
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
                <div class="form-group">
                    <label class="col-md-3 required" for="unit">{{ trans('cruds.unit.fields.unit') }}</label>
                    <div class="col-md-4">
                        <input class="form-control {{ $errors->has('unit') ? 'is-invalid' : '' }}" type="text" name="unit" id="unit" value="{{ old('unit', '') }}" required>
                        @if($errors->has('unit'))
                            <div class="invalid-feedback">
                                {{ $errors->first('unit') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.unit.fields.unit_helper') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3" for="description">{{ trans('cruds.unit.fields.description') }}</label>
                    <div class="col-md-4">
                        <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="2" name="description" id="description">{{ old('description', '') }}</textarea>
                        @if($errors->has('description'))
                            <div class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.unit.fields.description_helper') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 required" for="status">{{ trans('cruds.unit.fields.status') }}</label>
                    <div class="col-md-4">
                        <div class="radio-list">
                            <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" checked required> {!! trans('cruds.unit.fields.active') !!}</label>
                            <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" required> {!! trans('cruds.unit.fields.inactive') !!}</label>
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
<script src="{{ asset('js/admin/unit.js') }}"></script>
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