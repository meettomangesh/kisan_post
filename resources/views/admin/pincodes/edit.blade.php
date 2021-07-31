@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.pin_code.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.pincodes.update", [$pincode->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="pin_code">{{ trans('cruds.pin_code.fields.pin_code') }}</label>
                <input class="form-control {{ $errors->has('pin_code') ? 'is-invalid' : '' }}" type="text" name="pin_code" id="pin_code" value="{{ old('pin_code', $pincode->pin_code) }}" required>
                @if($errors->has('pin_code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pin_code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pin_code.fields.pin_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="country_id">{{ trans('cruds.pin_code.fields.country') }}</label>
                <select class="form-control select2 {{ $errors->has('country') ? 'is-invalid' : '' }}" name="country_id" id="country_id" required>
                    @foreach($countries as $id => $country)
                        <option value="{{ $id }}" {{ ($pincode->country ? $pincode->country->id : old('country_id')) == $id ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
                @if($errors->has('country'))
                    <div class="invalid-feedback">
                        {{ $errors->first('country') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pin_code.fields.country_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="state_id">{{ trans('cruds.pin_code.fields.state') }}</label>
                <select class="form-control select2 {{ $errors->has('state') ? 'is-invalid' : '' }}" name="state_id" id="state_id" required>
                    @foreach($states as $id => $state)
                        <option value="{{ $id }}" {{ ($pincode->state ? $pincode->state->id : old('state_id')) == $id ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
                @if($errors->has('state'))
                    <div class="invalid-feedback">
                        {{ $errors->first('state') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pin_code.fields.state_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="city_id">{{ trans('cruds.pin_code.fields.city') }}</label>
                <select class="form-control select2 {{ $errors->has('city') ? 'is-invalid' : '' }}" name="city_id" id="city_id" required>
                    @foreach($cities as $id => $city)
                        <option value="{{ $id }}" {{ ($pincode->city ? $pincode->city->id : old('city_id')) == $id ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
                @if($errors->has('city'))
                    <div class="invalid-feedback">
                        {{ $errors->first('city') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pin_code.fields.city_helper') }}</span>
            </div>

            <!-- <div class="form-group">
                <label class="required" for="state_id">{{ trans('cruds.pin_code.fields.state') }}</label>
                
                <select class="form-control select2 {{ $errors->has('state') ? 'is-invalid' : '' }}" name="state_id" id="state_id" required>

                    <option>Please select</option>
                </select>
                @if($errors->has('state'))
                <div class="invalid-feedback">
                    {{ $errors->first('state') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.pin_code.fields.state_helper') }}</span>
            </div> -->

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('select[name="country_id"]').on('change', function() {
            var countryID = $(this).val();
            var url = '{{ route("admin.pincodes.getStates", "") }}';
            //url = url.replace(':cid', countryID);
            url = url+'/'+countryID;

            if (countryID) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        
                        $('select[name="state_id"]').empty();
                        $('select[name="state_id"]').append('<option value=""> Please select </option>');
                        $.each(data, function(key, value) {
                            $('select[name="state_id"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('select[name="state_id"]').empty();
            }
        });

        $('select[name="state_id"]').on('change', function() {
            var countryID = $('#country_id').val();
            var stateID = $(this).val();
            var url = '{{ route("admin.pincodes.getCities", "","") }}';
            // url = url.replace(':cid', countryID);
            url = url+'/'+countryID+'/'+stateID;

            if (countryID && stateID) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        
                        $('select[name="city_id"]').empty();
                        $('select[name="city_id"]').append('<option value=""> Please select </option>');
                        $.each(data, function(key, value) {
                            $('select[name="city_id"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('select[name="city_id"]').empty();
            }
        });
    });
</script>
@endsection