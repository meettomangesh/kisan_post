@extends('layouts.admin')
@section('content')
@can('communication_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.communications.create') }}">
            {{ trans('global.add') }} {{ trans('cruds.communication.title_singular') }}
        </a>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.communication.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Communication">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th> {{ trans('cruds.communication.fields.id') }} </th>

                        <th width='10%'>{{ trans('cruds.communication.fields.notification') }} </th>
                        <!-- <th width='15%'>{{ trans('cruds.communication.fields.message-type') }} </th> -->
                        <th width='20%'>{{ trans('cruds.communication.fields.message-title') }} </th>
                        <th width='20%'>{{ trans('cruds.communication.fields.message-send-date-time') }} </th>
                        <th width='10%'>{{ trans('cruds.communication.fields.email_count') }}</th>
                        <th width='10%'>{{ trans('cruds.communication.fields.sms_count') }} </th>
                        <th width='10%'>{{ trans('cruds.communication.fields.status') }} </th>
                        <th width='10%'>{{ trans('cruds.communication.fields.processed') }} </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userCommunicationMessages as $key => $userCommunicationMessage)
                    <tr data-entry-id="{{ $userCommunicationMessage->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $userCommunicationMessage->id ?? '' }}
                        </td>
                        <td>
                            {{ $userCommunicationMessage->notifyStr ?? '' }}
                        </td>
                        <!-- <td>
                            {{ $userCommunicationMessage->message_type ?? '' }}
                        </td> -->
                        <td>
                            {{ $userCommunicationMessage->message_title ?? '' }}
                        </td>
                        <td>
                            {{ $userCommunicationMessage->message_send_time ?? '' }}
                        </td>
                        <td>
                            {{ $userCommunicationMessage->email_count ?? '' }}
                        </td>
                        <td>
                            {{ $userCommunicationMessage->sms_count ?? '' }}
                        </td>
                        <td>
                            {{ ($userCommunicationMessage->status == 1?"Active":"Inactive") ?? '' }}
                        </td>
                        <td>
                            {{ ($userCommunicationMessage->processed == 1?"Yes":"No") ?? '' }}
                        </td>
                        <td>
                            @can('country_show')
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.communications.show', $userCommunicationMessage->id) }}">
                                {{ trans('global.view') }}
                            </a>
                            @endcan
                            @can('country_edit')
                            @if(date('Y-m-d', strtotime($userCommunicationMessage->message_send_time)) > date('Y-m-d'))
                            <a class="btn btn-xs btn-info" href="{{ route('admin.communications.edit', $userCommunicationMessage->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                            @endif
                            @endcan

                            @can('country_delete')
                            <form action="{{ route('admin.communications.destroy', $userCommunicationMessage->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                            </form>
                            @endcan

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('communication_delete')
        let deleteButtonTrans = '{{ trans('
        global.datatables.delete ') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.communications.massDestroy') }}",
            className: 'btn-danger',
            action: function(e, dt, node, config) {
                var ids = $.map(dt.rows({
                    selected: true
                }).nodes(), function(entry) {
                    return $(entry).data('entry-id')
                });

                if (ids.length === 0) {
                    alert('{{ trans('
                        global.datatables.zero_selected ') }}')

                    return
                }

                if (confirm('{{ trans('
                        global.areYouSure ') }}')) {
                    $.ajax({
                            headers: {
                                'x-csrf-token': _token
                            },
                            method: 'POST',
                            url: config.url,
                            data: {
                                ids: ids,
                                _method: 'DELETE'
                            }
                        })
                        .done(function() {
                            location.reload()
                        })
                }
            }
        }
        dtButtons.push(deleteButton)
        @endcan

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [
                [1, 'desc']
            ],
            pageLength: 100,
        });
        let table = $('.datatable-Communication:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    })
</script>
@endsection