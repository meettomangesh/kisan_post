@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.region.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.regions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="region_name">{{ trans('cruds.region.fields.region_name') }}</label>
                <input class="form-control {{ $errors->has('region_name') ? 'is-invalid' : '' }}" type="text" name="region_name" id="region_name" value="{{ old('region_name', '') }}" required>
                @if($errors->has('region_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('region_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.region.fields.region_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pin_codes">{{ trans('cruds.region.fields.pin_codes') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('pin_codes') ? 'is-invalid' : '' }}" name="pin_codes[]" id="pin_codes" multiple required>
                    @foreach($pin_codes as $id => $pin_codes)
                        <option value="{{ $id }}" {{ in_array($id, old('pin_codes', [])) ? 'selected' : '' }}>{{ $pin_codes }}</option>
                    @endforeach
                </select>
                @if($errors->has('pin_codes'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pin_codes') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.region.fields.pin_codes_helper') }}</span>
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