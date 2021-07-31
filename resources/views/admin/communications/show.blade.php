@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.communication.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.communications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.id') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.notification') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->notifyStr }}
                        </td>
                    </tr>
                   
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.message-title') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->message_title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.message-send-date') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->message_send_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.message-send-time') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->message_send_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.email_count') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->email_count }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.sms_count') }}
                        </th>
                        <td>
                            {{ $userCommunicationMessages->sms_count }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.regions') }}
                        </th>
                        <td>
                            
                            @foreach($userCommunicationMessages->regions as $key => $regions)
                                <span class="label label-info">{{ $regions->region_name }}</span>
                            @endforeach
                        </td>
                    </tr>


                    <tr>
                        <th>
                            {{ trans('cruds.communication.fields.users') }}
                        </th>
                        <td>
                            
                            @foreach($users as $key => $users)
                                <span class="label label-info">{{ $users->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.communications.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection