@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.loginlogs.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-LoginLogs">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.loginlogs.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginlogs.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginlogs.fields.platform') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginlogs.fields.login_time') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginlogs.fields.is_login') }}
                        </th>

                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th></th>
                        <th>
                            <!--<input type="text" placeholder="Search" />
                             <select>
                                <option value="">Select</option>
                                <option value="1">Login</option>
                                <option value="2">Logout</option>
                            </select> -->
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userLoginLogs as $key => $userLoginLog)
                    <tr data-entry-id="{{ $userLoginLog->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $userLoginLog->id ?? '' }}
                        </td>
                        <td>
                            {{ ( $userLoginLog->users? $userLoginLog->users->first_name." ".$userLoginLog->users->last_name:"") ?? '' }}
                        </td>

                        <td>
                            {{ ($userLoginLog->platform == 1?"Android":"IOS") ?? '' }}
                        </td>

                        <td>
                            {{ ($userLoginLog->login_time?date_format(date_create($userLoginLog->login_time),"Y/m/d h:i:s A"):"") ?? '' }}
                        </td>
                        <td>
                            {{ ($userLoginLog->is_login == 1?"Login":"Logout") ?? '' }}
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



        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [
                [1, 'desc']
            ],
            pageLength: 100,
        });

        $('.datatable-LoginLogs:not(.ajaxTable) thead tr:eq(1) th').each(function(i) {
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        })
        let table = $('.datatable-LoginLogs:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    })
</script>
@endsection