@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.order.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
        <form method="POST" action="{{ route("admin.orders.update", [$customerOrder->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="delivery_date">{{ trans('cruds.order.fields.delivery_date') }}</label>
                        <div class="col-md-8 float-right">
                            <!-- div data-error-container="#form_special_price_start_date_error" class="input-group date form_datetime" data-date-start-date="+0d" -->
                            <input class="form-control {{ $errors->has('delivery_date') ? 'is-invalid' : '' }}" type="date" name="delivery_date" id="delivery_date" min="{{ ($customerOrder->special_price_start_date) ? $customerOrder->delivery_date : date('Y-m-d') }}" value="{{ old('delivery_date', $customerOrder->delivery_date) }}" startDateValid="true">
                            <!-- span class="input-group-btn">
                                    <button class="btn default date-set" type="button" id="date-picker-btn"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div -->
                            <span class="help-block">{{ trans('cruds.order.fields.delivery_date_helper') }}</span>
                            <div id="form_delivery_date_error"></div>
                        </div>
                        <input type="hidden" name="address_id" id="address_id" value="{{$customerOrder->shipping_address_id}}">
                        <input type="hidden" name="user_id" id="user_id" value="{{$customerOrder->customer_id}}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        console.log('ready is called');
        $('input[name="delivery_date"]').on('change', function() {
            var deliveryDate = $(this).val();
            var addressId = $("#address_id").val();
            var userId = $("#user_id").val();
            var actionUrl = '{{ route("admin.orders.checkDeliveryBoyAvailability", "") }}';
            var formData = new FormData();
            formData.append("delivery_details[date]", deliveryDate);
            formData.append("delivery_date", deliveryDate);
            formData.append("address_id", addressId);
            formData.append("user_id", userId);
            formData.append("is_admin", 1);
            if (deliveryDate) {
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.status === true) {
                            $('#form_delivery_date_error').html('<div class="alert alert-success" role="alert">'+data.message+'</div>');
                        } else {
                            $('#form_delivery_date_error').html('<div class="alert alert-warning" role="alert">'+data.message+'</div');
                        }

                        // var $ell = $('#' + currentForm).find("#users");
                        // $ell.empty();
                        // $ell.select2("val", '');
                        // $ell.empty(); // remove old options  

                        // $.each(data.user_details, function(value, key) {
                        //     $ell.append($('<option>', {
                        //         value: value,
                        //         text: key,
                        //     }));
                        // });
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $('#form_delivery_date_error').html('<div class="alert alert-warning" role="alert">'+"Please try again later."+'</div>');
                        // var errors = jqXhr.responseJSON;
                        // var errorsHtml = '';
                        // $.each(errors, function(key, value) {
                        //     errorsHtml += value[0] + '<br />';
                        // });
                        // Metronic.alert({
                        //     type: 'danger',
                        //     message: errorsHtml,
                        //     container: $('#ajax-response-text'),
                        //     place: 'prepend',
                        //     closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        // });
                    }
                });
                setTimeout(function() {
                    $('#form_delivery_date_error').html('');
                }, 5000);
            } else {

            }
            // formData.append('custom_region', customRegion);
            //  formData.append('region_type', regionTypeFlag);

            // //url = url.replace(':cid', countryID);
            // url = url+'/'+countryID;

            // if (countryID) {
            //     $.ajax({
            //         url: url,
            //         type: "GET",
            //         dataType: "json",
            //         success: function(data) {

            //             $('select[name="state_id"]').empty();
            //             $('select[name="state_id"]').append('<option value=""> Please select </option>');
            //             $.each(data, function(key, value) {
            //                 $('select[name="state_id"]').append('<option value="' + key + '">' + value + '</option>');
            //             });
            //         }
            //     });
            // } else {
            //     $('select[name="state_id"]').empty();
            // }
        });

    });
</script>
@endsection