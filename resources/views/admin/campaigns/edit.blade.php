@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.campaign.title_singular') }}
    </div>

    <div class="card-body">

        {!! Form::open(['route' => ['admin.campaigns.update', [$campaigns->id]], 'method' => 'put', 'class' => 'form-horizontal merchant-campaign-form', 'id' => 'update_campaign', 'msg' => 'Campaign updated successfully.']) !!}

        @include('admin.campaigns.form', ['from'=>'update_campaign'])
        @section('page-level-scripts')
        @parent
        <script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
        @stop


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

    @parent
    <script src="{{ asset('global/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/admin/communications.js') }}"></script>
    <script src="{{ asset('js/admin/communications.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('global/scripts/components-bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>




    @endsection

    @section('template-level-styles')
    @parent
    <link href="{{ asset('global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    @stop

    @section('page-level-scripts')
    <script src="{{ asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-modal/js/bootstrap-modal.js') }}"></script>
    <script src="{{ asset('global/plugins/cubeportfolio/js/jquery.cubeportfolio.js') }}"></script>
    <script src="{{ asset('global/plugins/owl.carousel.min.js') }}"></script>
    @endsection

</div>