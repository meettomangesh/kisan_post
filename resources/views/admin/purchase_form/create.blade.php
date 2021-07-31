@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.purchase_form.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" id="create-purchase-form" action="{{ route('admin.purchase_form.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="supplier_name">{{ trans('cruds.purchase_form.fields.supplier_name') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('supplier_name') ? 'is-invalid' : '' }}" type="text" name="supplier_name" id="supplier_name" maxlength="250" value="{{ old('supplier_name', '') }}" required>
                            @if($errors->has('supplier_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('supplier_name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.supplier_name_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="product_name">{{ trans('cruds.purchase_form.fields.product_name') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('product_name') ? 'is-invalid' : '' }}" type="text" name="product_name" id="product_name" maxlength="250" value="{{ old('product_name', '') }}" required>
                            @if($errors->has('product_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('product_name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.product_name_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="unit">{{ trans('cruds.purchase_form.fields.unit') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('unit') ? 'is-invalid' : '' }}" type="text" name="unit" id="unit" maxlength="20" value="{{ old('unit', '') }}" required>
                            @if($errors->has('unit'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('unit') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.unit_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="category">{{ trans('cruds.purchase_form.fields.category') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('category') ? 'is-invalid' : '' }}" type="text" name="category" id="category" maxlength="50" value="{{ old('category', '') }}" required>
                            @if($errors->has('category'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('category') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.category_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="price">{{ trans('cruds.purchase_form.fields.price') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-inr"></i>
                                </span>
                                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" name="price" id="price" value="{{ old('price', '') }}" greaterThanZero = "true" numberOnly="true" maxlength="10" autocomplete="off" required>
                            </div>
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.price_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>
                
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="total_in_kg">{{ trans('cruds.purchase_form.fields.total_in_kg') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('total_in_kg') ? 'is-invalid' : '' }}" type="text" name="total_in_kg" id="total_in_kg" maxlength="10" value="{{ old('total_in_kg', '') }}" required>
                            @if($errors->has('total_in_kg'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('total_in_kg') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.total_in_kg_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="total_units">{{ trans('cruds.purchase_form.fields.total_units') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('total_units') ? 'is-invalid' : '' }}" type="text" name="total_units" id="total_units" maxlength="10" value="{{ old('total_units', '') }}" required>
                            @if($errors->has('total_units'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('total_units') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.total_units_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="order_date">{{ trans('cruds.purchase_form.fields.order_date') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('order_date') ? 'is-invalid' : '' }}" type="date" name="order_date" id="order_date" max="{{ date('Y-m-d') }}" value="{{ old('order_date', '') }}"  required>
                            <span class="help-block">{{ trans('cruds.purchase_form.fields.order_date_helper') }}</span>
                            <div id="form_order_date_error"></div>
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
<script src="{{ asset('js/admin/product_units.js') }}"></script>
@endsection

@section('page-level-scripts')
<script src="{{ asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
@endsection

@section('scripts')
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.productUnitJs.init();
    });
</script>
@endsection