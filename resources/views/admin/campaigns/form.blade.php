@section('page-level-styles')
@parent

<link href="{{ asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/uniform/css/uniform.default.min.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" />
<link href="{{ asset('global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" />
@stop

<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 required" for="campaign_category_id">{{ trans('cruds.campaign.fields.campaign_category_id') }}</label>
                <div class="col-md-8 ">
                    @if($from == 'create_campaign')
                        <select class="form-control select2 {{ $errors->has('campaign_category_id') ? 'is-invalid' : '' }}" name="campaign_category_id" id="campaign_category_id" required>
                            @foreach($campaignCategoriesMaster as $id => $category)
                            <option value="{{ $id }}">{{ $category }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('campaign_category_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('campaign_category_id') }}
                        </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.campaign.fields.campaign_category_id_helper') }}</span>
                    @else
                    <span> {{$campaigns->campaignCategory->name}} </span>
                    @endif
                </div>


            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 required" for="campaign_master_id">{{ trans('cruds.campaign.fields.campaign_master_id') }}</label>
                <div class="col-md-8 ">
                    @if($from == 'create_campaign')
                    <select class="form-control select2 {{ $errors->has('campaign_master_id') ? 'is-invalid' : '' }}" name="campaign_master_id" id="campaign_master_id" required>
                        <option>Please select</option>
                    </select>
                    @if($errors->has('campaign_master_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('campaign_master_id') }}
                    </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.campaign.fields.campaign_master_id_helper') }}</span>
                    @else
                    <span> {{$campaigns->campaignMaster->name}} </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.title') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                    @if($from == 'create_campaign')
                    {!! Form::text('title', '', ['maxlength'=>100,'class'=>'form-control', 'id'=>'title', 'data-rule-required'=>'true', 'data-msg-required'=>"Campaign title is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Campaign title should be less than 100 chars" ])!!}
                    @else
                    {!! Form::text('title', $campaigns->title, ['disabled'=>true,'maxlength'=>100,'class'=>'form-control', 'id'=>'title', 'data-rule-required'=>'true', 'data-msg-required'=>"Campaign title is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Campaign title should be less than 100 chars" ])!!}
                    @endif
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.description') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                @if($from == 'create_campaign')
                    {!! Form::textArea('description','', ['class'=>'form-control','rows'=>8,'id'=>'description','data-rule-required'=>'false', 'data-msg-required'=>"Please enter the description", 'maxlength'=>320, 'data-rule-maxlength'=>'320', 'data-msg-maxlength'=>"Description should be 320 chars" ])!!}
                    @else
                    {!! Form::textArea('description', ($campaigns)?$campaigns->description:'', ['class'=>'form-control','rows'=>8,'id'=>'description','data-rule-required'=>'false', 'data-msg-required'=>"Please enter the description", 'maxlength'=>320, 'data-rule-maxlength'=>'320', 'data-msg-maxlength'=>"Description should be 320 chars" ])!!}
                    @endif   
                    <div class="help-block"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.code_type') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                    <div class="radio-list">

                        @if($from == 'create_campaign')
                        <label class="radio-inline">{!! Form::radio('code_type', '1', true) !!} {{ trans('cruds.campaign.fields.generic') }}</label>
                        <label class="radio-inline">{!! Form::radio('code_type', '2') !!} {{ trans('cruds.campaign.fields.unique') }}</label>
                        @else
                        <label class="radio-inline"><input disabled type="radio" name="code_type" value="{{ old('code_type', '1') }}" {{ $campaigns->code_type == '1' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.generic') }}</label>
                        <label class="radio-inline"><input disabled  type="radio" name="code_type" value="{{ old('code_type', '2') }}" {{ $campaigns->code_type == '2' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.unique') }}</label>
                        @endif


                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.reward_value') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                    @if($from == 'create_campaign')
                    {!! Form::text('reward_value', '', ['maxlength'=>100,'class'=>'form-control', 'id'=>'reward_value', 'data-rule-required'=>'true', 'data-msg-required'=>"Reward value is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Reward value should be less than 100 chars" ])!!}
                    @else
                    {!! Form::text('reward_value', $campaigns->reward_type_x_value, ['disabled'=>true,'maxlength'=>100,'class'=>'form-control', 'id'=>'reward_value', 'data-rule-required'=>'true', 'data-msg-required'=>"Reward value is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Reward value should be less than 100 chars" ])!!}
                    @endif
                    <div class="help-block"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4" for="start_date">{{ trans('cruds.campaign.fields.start_date') }}</label>
                <div class="col-md-8 ">
                @if($from == 'create_campaign')
                    <input class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date', '') }}" startDateValid="true">
                @else
                <input disabled class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date', $campaigns->start_date) }}" startDateValid="true">
                @endif
                    <span class="help-block">{{ trans('cruds.campaign.fields.start_date_helper') }}</span>
                    <div id="form_start_date_error"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4" for="end_date">{{ trans('cruds.campaign.fields.end_date') }}</label>
                <div class="col-md-8 ">
                    @if($from == 'create_campaign')
                    <input class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date','') }}" endDateValid="true">
                    @else
                    <input disabled class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date', $campaigns->end_date) }}" endDateValid="true">
                    @endif
                    <span class="help-block">{{ trans('cruds.campaign.fields.end_date_helper') }}</span>
                    <div id="form_end_date_error"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.campaign_use') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                    <div class="radio-list">
                        <label class="radio-inline">{!! Form::radio('campaign_use', '1', true) !!} {{ trans('cruds.campaign.fields.unlimited') }}</label>
                        <label class="radio-inline">{!! Form::radio('campaign_use', '2') !!} {{ trans('cruds.campaign.fields.limited') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.campaign_use_value') }}
                    <span class="required"> </span>
                </label>
                <div class="col-md-8">
                {!! Form::number('campaign_use_value', null, ['min' => '0','maxlength'=>100,'class'=>'form-control', 'id'=>'campaign_use_value', 'data-rule-required'=>'true', 'data-msg-required'=>"Code usage value is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code usage value length should be less than 100 chars" ])!!}
                    <div class="help-block"></div>
                </div>
            </div>
        </div>

    </div> -->
    <div id="coupon_code_format">
        <h3 class="block">Code Format</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.code_prefix') }}
                       
                    </label>
                    <div class="col-md-8">
                        @if($from == 'create_campaign')
                        {!! Form::text('code_prefix', '', ['maxlength'=>100,'class'=>'form-control', 'id'=>'code_prefix', 'data-rule-required'=>'false', 'data-msg-required'=>"Code prefix is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code prefix should be less than 100 chars" ])!!}
                        @else
                        {!! Form::text('code_prefix', ($campaigns->promoCodesFormat && $campaigns->promoCodesFormat->code_prefix !='null')?$campaigns->promoCodesFormat->code_prefix:'', ['disabled'=>true,'maxlength'=>100,'class'=>'form-control', 'id'=>'code_prefix', 'data-rule-required'=>'false', 'data-msg-required'=>"Code prefix is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code prefix should be less than 100 chars" ])!!}
                        @endif
                    <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.code_suffix') }}
                        
                    </label>
                    <div class="col-md-8">
                    @if($from == 'create_campaign')
                        {!! Form::text('code_suffix', '', ['maxlength'=>100,'class'=>'form-control', 'id'=>'code_suffix', 'data-rule-required'=>'false', 'data-msg-required'=>"Code suffix is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code suffix should be less than 100 chars" ])!!}
                        @else
                        {!! Form::text('code_suffix', ($campaigns->promoCodesFormat && $campaigns->promoCodesFormat->code_suffix !='null')?$campaigns->promoCodesFormat->code_suffix:'', ['disabled'=>true,'maxlength'=>100,'class'=>'form-control', 'id'=>'code_suffix', 'data-rule-required'=>'false', 'data-msg-required'=>"Code suffix is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code suffix should be less than 100 chars" ])!!}
                        @endif   
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-4">{{ trans('cruds.campaign.fields.code_length') }}
                        <span class="required"> </span>
                    </label>
                    <div class="col-md-8">
                        @if($from == 'create_campaign')
                        {!! Form::number('code_length','', ['min' => '0','max' => '100','maxlength'=>100,'class'=>'form-control', 'id'=>'code_length', 'data-rule-required'=>'true', 'data-msg-required'=>"Code length is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code length should be less than 100 chars" ])!!}
                        @else
                        {!! Form::number('code_length',($campaigns->promoCodesFormat)?$campaigns->promoCodesFormat->code_length:0, ['disabled'=>true,'min' => '0','max' => '100','maxlength'=>100,'class'=>'form-control', 'id'=>'code_length', 'data-rule-required'=>'true', 'data-msg-required'=>"Code length is require", 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>"Code length should be less than 100 chars" ])!!}
                        @endif  
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <h3 class="block">Whom to Send</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{{ trans('cruds.campaign.fields.users') }} <span class="required" aria-required="true"></span></label>
                <div class="col-md-8">
                    <div class="radio-list">
                        @if($from == 'create_campaign')
                        <label class="radio-inline">{!! Form::radio('target_customer', '1', true) !!} {{ trans('cruds.campaign.fields.all') }}</label>
                        <label class="radio-inline">{!! Form::radio('target_customer', '2') !!} {{ trans('cruds.campaign.fields.custom') }}</label>
                        @else
                        <label class="radio-inline"><input type="radio" disabled name="target_customer" value="{{ old('target_customer', '1') }}" {{ $campaigns->target_customer == '1' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.all') }}</label>
                        <label class="radio-inline"><input type="radio" disabled name="target_customer" value="{{ old('target_customer', '2') }}" {{ $campaigns->target_customer == '2' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.custom') }}</label>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="user-selection-div" style="display:none">
        <div class="col-md-12">
            <div class="form-group">
                <label class="col-md-4 control-label required" for="target_customer_value">{{ trans('cruds.campaign.fields.users') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                @if($from == 'create_campaign')
                <select class="form-control select2 {{ $errors->has('target_customer_value') ? 'is-invalid' : '' }}" name="target_customer_value[]" id="target_customer_value" multiple data-rule-required="true" data-msg-required="Please select the target users.">
               
                    @foreach($users as $id => $users)

                    <option value="{{ $id }}" {{ (in_array($id, old('users', [])) ) ? 'selected' : '' }}>{{ $users }}</option>
                    @endforeach



                </select>
                @else
                <select disabled class="form-control select2 {{ $errors->has('target_customer_value') ? 'is-invalid' : '' }}" name="target_customer_value[]" id="target_customer_value" multiple data-rule-required="true" data-msg-required="Please select the target users.">
                
                @foreach($users as $id => $users)

                    <option value="{{ $id }}" {{ (in_array($id, old('users', [])) || $promocodeUsers->contains($id)) ? 'selected' : '' }}>{{ $users }}</option>
                    @endforeach



                    </select>
                @endif
                @if($errors->has('target_customer_value'))
                <div class="invalid-feedback">
                    {{ $errors->first('target_customer_value') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.users_helper') }}</span>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{{ trans('cruds.campaign.fields.status') }}<span class="required" aria-required="true"></span></label></label>
                <div class="col-md-8">
                    <div class="radio-list">
                        @if($from == 'create_campaign')
                        <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {{ trans('cruds.campaign.fields.active') }}</label>
                        <label class="radio-inline">{!! Form::radio('status', '2') !!} {{ trans('cruds.campaign.fields.inactive') }}</label>
                        @else
                        <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '1') }}" {{ $campaigns->status == '1' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.active') }}</label>
                        <label class="radio-inline"><input type="radio" name="status" value="{{ old('status', '2') }}" {{ $campaigns->status == '2' ? 'checked' : '' }} required> {{ trans('cruds.campaign.fields.inactive') }}</label>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@section('page-level-scripts')
@parent
<script src="{{ asset('js/admin/campaign.js') }}"></script>
<script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>

@section('scripts')
<script>
    jQuery(document).ready(function() {
        siteObjJs.admin.campaignJs.init("<?php echo $from; ?>");
    });
</script>
@endsection
@stop