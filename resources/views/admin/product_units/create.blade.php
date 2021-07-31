@extends('layouts.admin')
@section('page-level-styles')
<link href="{{ asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/cubeportfolio/css/cubeportfolio.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/bootstrap-modal/css/bootstrap-modal.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" />
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.product.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" id="create-product-unit" action="{{ route("admin.product_units.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="products_id">{{ trans('cruds.product_unit.fields.product') }}</label>
                        <div class="col-md-8 float-right">
                            <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="products_id" id="products_id" required>
                                @foreach($products as $id => $product)
                                    <option value="{{ $id }}" {{ old('products_id') == $id ? 'selected' : '' }}>{{ $product }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($errors->has('product'))
                        <div class="invalid-feedback">
                            {{ $errors->first('product') }}
                        </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.product_unit.fields.product_helper') }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="opening_quantity">{{ trans('cruds.product_unit.fields.opening_quantity') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('opening_quantity') ? 'is-invalid' : '' }}" name="opening_quantity" id="opening_quantity" value="{{ old('opening_quantity', '') }}" greaterThanZero = "true" numberOnly="true" required>
                            @if($errors->has('opening_quantity'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('opening_quantity') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.product_unit.fields.opening_quantity_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="category_div" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="category_name">{{ trans('cruds.product_unit.fields.category') }}</label>
                        <div class="col-md-8 float-right">
                            <label class="category_name"></label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="units">{{ trans('cruds.product_unit.fields.units') }}</label>
                        <div class="col-md-8 float-right">
                            <select class="form-control select2 {{ $errors->has('units') ? 'is-invalid' : '' }}" name="unit_id" id="unit_id" required>
                                <option>Please select</option>
                            </select>
                            @if($errors->has('units'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('units') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.product_unit.fields.units_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="selling_price">{{ trans('cruds.product_unit.fields.selling_price') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-inr"></i>
                                </span>
                                <input class="form-control {{ $errors->has('selling_price') ? 'is-invalid' : '' }}" name="selling_price" id="selling_price" value="{{ old('selling_price', '') }}" greaterThanZero = "true" numberOnly="true" maxlength="10" autocomplete="off" required>
                            </div>
                            <span class="help-block">{{ trans('cruds.product_unit.fields.selling_price_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="special_price">{{ trans('cruds.product_unit.fields.special_price') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-inr"></i>
                                </span>
                                <input class="form-control {{ $errors->has('special_price') ? 'is-invalid' : '' }}" name="special_price" id="special_price" value="{{ old('special_price', '') }}" greaterThanZero = "true" numberOnly="true" priceRangeValid="true" maxlength="10" autocomplete="off">
                            </div>
                            <span class="help-block">{{ trans('cruds.product_unit.fields.special_price_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="special_price_start_date">{{ trans('cruds.product_unit.fields.special_price_start_date') }}</label>
                        <div class="col-md-8 float-right">
                            <!-- div data-error-container="#form_special_price_start_date_error" class="input-group date form_datetime" data-date-start-date="+0d" -->
                                <input class="form-control {{ $errors->has('special_price_start_date') ? 'is-invalid' : '' }}" type="date" name="special_price_start_date" id="special_price_start_date" min="{{ date('Y-m-d') }}" value="{{ old('special_price_start_date', '') }}" startDateValid="true">
                                <!-- span class="input-group-btn">
                                    <button class="btn default date-set" type="button" id="date-picker-btn"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div -->
                            <span class="help-block">{{ trans('cruds.product_unit.fields.special_price_start_date_helper') }}</span>
                            <div id="form_special_price_start_date_error"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="special_price_end_date">{{ trans('cruds.product_unit.fields.special_price_end_date') }}</label>
                        <div class="col-md-8 float-right">
                            <!-- div data-error-container="#form_special_price_end_date_error" class="input-group date form_datetime" data-date-start-date="+0d" -->
                                <input class="form-control {{ $errors->has('special_price_end_date') ? 'is-invalid' : '' }}" type="date" name="special_price_end_date" id="special_price_end_date" min="{{ date('Y-m-d') }}" value="{{ old('special_price_end_date', '') }}" endDateValid="true">
                                <!-- span class="input-group-btn">
                                    <button class="btn default date-set" type="button" id="date-picker-btn"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div -->
                            <span class="help-block">{{ trans('cruds.product_unit.fields.special_price_end_date_helper') }}</span>
                            <div id="form_special_price_end_date_error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="min_quantity">{{ trans('cruds.product_unit.fields.min_quantity') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('min_quantity') ? 'is-invalid' : '' }}" name="min_quantity" id="min_quantity" value="{{ old('min_quantity', '1') }}" greaterThanZero = "true" numberOnly="true" maxlength="10" autocomplete="off" required>
                            <span class="help-block">{{ trans('cruds.product_unit.fields.min_quantity_helper') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="max_quantity">{{ trans('cruds.product_unit.fields.max_quantity') }}</label>
                        <div class="col-md-8 float-right">
                            <input class="form-control {{ $errors->has('max_quantity') ? 'is-invalid' : '' }}" name="max_quantity" id="max_quantity" value="{{ old('max_quantity', '') }}" greaterThanZero = "true" numberOnly="true" quantityValid="true" maxlength="10" autocomplete="off" required>
                            <span class="help-block">{{ trans('cruds.product_unit.fields.max_quantity_helper') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 required" for="status">{{ trans('cruds.product_unit.fields.status') }}</label>
                        <div class="col-md-8 float-right">
                            <div class="radio-list">
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" checked required> {!! trans('cruds.product_unit.fields.active') !!}</label>
                                <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '0') }}" required> {!! trans('cruds.product_unit.fields.inactive') !!}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
                <!-- input type="hidden" name="images[]" id="images"/ -->
            </div>
        </form>
    </div>
</div>

@endsection

@section('template-level-scripts')
<script src="{{ asset('js/admin/product_units.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('page-level-scripts')
<script src="{{ asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-modal/js/bootstrap-modal.js') }}"></script>
<script src="{{ asset('global/plugins/cubeportfolio/js/jquery.cubeportfolio.js') }}"></script>
<script src="{{ asset('global/plugins/owl.carousel.min.js') }}"></script>
@endsection

@section('scripts')
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.productUnitJs.init('create-product-unit');

        $('select[name="products_id"]').on('change', function () {
            var productId = $(this).val();
            var url = '{{ route("admin.product_units.getUnits", "") }}';
            url = url+'/'+productId;
            if (productId) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $("#unit_id").empty();
                        if(data.is_unit_available > 0) {
                            $(".category_name").text(data.category);
                            $("#category_div").attr("style", "display: block");
                            $.each(data.units, function(key, value) {
                                $("#unit_id").append('<option value="' + key + '">' + value + '</option>');
                            });
                        } else {
                            $("#category_div").attr("style", "display: none");
                            $("#products_id option:selected").prop("selected", false)
                            $("#select2-products_id-container").text('');
                            alert('No unit is available');
                        }
                    }
                });
            } else {
                $("#unit_ids").empty();
            }
        });
    });
</script>
@endsection