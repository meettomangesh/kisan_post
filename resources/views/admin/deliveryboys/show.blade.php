@extends('layouts.admin')
@section('content')
<style>
    .switch-field {
        display: flex;

        overflow: hidden;
    }

    .switch-field input {
        position: absolute !important;
        clip: rect(0, 0, 0, 0);
        height: 1px;
        width: 1px;
        border: 0;
        overflow: hidden;
    }

    .switch-field label {
        background-color: #e4e4e4;
        color: rgba(0, 0, 0, 0.6);
        font-size: 14px;
        line-height: 1;
        text-align: center;
        padding: 8px 16px;
        margin-right: -1px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
        transition: all 0.1s ease-in-out;
    }

    .switch-field label:hover {
        cursor: pointer;
    }

    .switch-field input:checked+label {
        background-color: #a5dc86;
        box-shadow: none;
    }

    .switch-field input:checked+label.approved {
        background-color: green;
        box-shadow: none;
    }

    .switch-field input:checked+label.rejected {
        background-color: red;
        box-shadow: none;
    }

    .switch-field label:first-of-type {
        border-radius: 4px 0 0 4px;
    }

    .switch-field label:last-of-type {
        border-radius: 0 4px 4px 0;
    }

    /* This is just for CodePen. */

    .form {
        max-width: 600px;
        font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
        font-weight: normal;
        line-height: 1.625;
        margin: 8px auto;
        padding: 16px;
    }

    h2 {
        font-size: 18px;
        margin-bottom: 8px;
    }
</style>
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.deliveryboy.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.deliveryboys.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            @csrf
            <input type="hidden" value="{{$deliveryboy->id}}" name="user_id" id="user_id" >
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.id') }}
                        </th>
                        <td>
                            {{ $deliveryboy->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.user_photo') }}
                        </th>
                        <td>
                            <img src="{{ ($deliveryboy->details)?asset($deliveryboy->details->user_photo):'' }}" width="150" height="150">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.first_name') }}
                        </th>
                        <td>
                            {{ $deliveryboy->first_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.last_name') }}
                        </th>
                        <td>
                            {{ $deliveryboy->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.email') }}
                        </th>
                        <td>
                            {{ $deliveryboy->email }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.roles') }}
                        </th>
                        <td>
                            @foreach($deliveryboy->roles as $key => $roles)
                            <span class="label label-info">{{ $roles->title }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.regions') }}
                        </th>
                        <td>
                            @foreach($deliveryboy->regions as $key => $regions)
                            <span class="label label-info">{{ $regions->region_name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.bank_name') }}
                        </th>
                        <td>
                            {{ ($deliveryboy->details)?$deliveryboy->details->bank_name:'' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.account_number') }}
                        </th>
                        <td>
                            {{ ($deliveryboy->details)?$deliveryboy->details->account_number:'' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.deliveryboy.fields.ifsc_code') }}
                        </th>
                        <td>
                            {{ ($deliveryboy->details)?$deliveryboy->details->ifsc_code:'' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.deliveryboy.fields.kyc_verified') }}</th>
                        <td>
                            <?php
                            if ($deliveryboy->details) {
                            ?>
                                <div class="switch-field">
                                    <input date-user="{{$deliveryboy->id}}" data-id="{{$deliveryboy->details->id}}" data-status="0" class="toggle-class" type="radio" id="radio-two" name="switch-two" value="0" {{ $deliveryboy->details->status == 0 ? 'checked': '' }} />
                                    <label class="" for="radio-two">{{ trans('cruds.deliveryboy.fields.new') }}</label>
                                    <input date-user="{{$deliveryboy->id}}" data-id="{{$deliveryboy->details->id}}" data-status="1" class="toggle-class" type="radio" id="radio-three" name="switch-two" value="1" {{ $deliveryboy->details->status == 1 ? 'checked': '' }} />
                                    <label class="" for="radio-three">{{ trans('cruds.deliveryboy.fields.submitted') }}</label>
                                    <input date-user="{{$deliveryboy->id}}" data-id="{{$deliveryboy->details->id}}" data-status="2" class="toggle-class" type="radio" id="radio-four" name="switch-two" value="2" {{ $deliveryboy->details->status == 2 ? 'checked': '' }} />
                                    <label class="approved" for="radio-four">{{ trans('cruds.deliveryboy.fields.approved') }}</label>
                                    <input date-user="{{$deliveryboy->id}}" data-id="{{$deliveryboy->details->id}}" data-status="3" class="toggle-class" type="radio" id="radio-five" name="switch-two" value="3" {{ $deliveryboy->details->status == 3 ? 'checked': '' }} />
                                    <label class="rejected" for="radio-five">{{ trans('cruds.deliveryboy.fields.rejected') }}</label>
                                </div>
                            <?php } ?>
                        </td>
                        <!-- <td><span class="{{ $deliveryboy->status == 1 ? 'btn btn-success':'btn btn-danger' }}"> {{ ($deliveryboy->status == 1 ?trans('cruds.deliveryboy.fields.active'):trans('cruds.deliveryboy.fields.inactive')) ?? '' }}</span></td> -->
                    </tr>

                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.aadhar_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.aadhar_photo') }}</th>
                    </tr>
                    <tr>
                        <th style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->aadhar_number:'' }}</th>

                        <th rowspan="2"><a href="#" class="pop">
                                
                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->aadhar_card_photo:'') }}" width="150" height="150">
                            </a></td>
                        </th>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.pan_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.pan_photo') }}</th>
                    </tr>
                    <tr>
                        <th style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->pan_number:'' }}</th>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->pan_card_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.license_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.license_card_photo') }}</th>
                    </tr>
                    <tr>
                        <td style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->license_number:'' }}</td>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->license_card_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width:33%">{{ trans('cruds.deliveryboy.fields.vehicle_number') }}</th>

                        <th>{{ trans('cruds.deliveryboy.fields.rc_book_photo') }}</th>
                    </tr>
                    <tr>
                        <td style="width:33%">{{ ($deliveryboy->details)?$deliveryboy->details->vehicle_number:'' }}</td>

                        <td rowspan="2"><a href="#" class="pop">

                                <img src="{{ asset(($deliveryboy->details)?$deliveryboy->details->rc_book_photo:'') }}" width="150" height="150">
                            </a></td>
                    </tr>
                </tbody>
            </table>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.deliveryboys.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <img src="" class="imagepreview" style="width: 100%;">
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(function() {
        $('.pop').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');
        });

        $('.toggle-class').change(function() {
            //var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $('#user_id').val();
            var id = $(this).data('id');
            var status = $(this).data('status');
            var formData = new FormData();
            formData.append("user_id", user_id);
            formData.append("id", id);
            formData.append("status", status);


            $.ajax({
                type: "POST",
                data: formData,
                url: "{{ route('admin.deliveryboys.changeKYCStatus') }}",
                dataType: "json",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    //console.log(data)
                }
            });
        })
    });
</script>
@endsection