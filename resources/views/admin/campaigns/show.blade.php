@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.campaign.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.campaigns.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.id') }}
                        </th>
                        <td>
                            {{ $campaign->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.title') }}
                        </th>
                        <td>
                            {{ $campaign->title ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.description') }}
                        </th>
                        <td>
                            {{ $campaign->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.start_date') }}
                        </th>
                        <td>
                            {{ $campaign->start_date ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.end_date') }}
                        </th>
                        <td>
                            {{ $campaign->end_date ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.code_type') }}
                        </th>
                        <td>
                            {{ ($campaign->code_type == 1?"Generic":"Unique") ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.campaign.fields.status') }}
                        </th>
                        <td>
                            {{ ($campaign->status == 1?"Active":"Inactive") ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php 
            //print_r($campaign->promoCodes()->get()); exit;
            ?>
                <h3 class="block">Promo Codes</h3>
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover datatable datatable-Campaign">
                        <thead>
                            <tr>
                            <th width="2%"></th>
                                <th width='10%'>{{ trans('cruds.campaign.fields.user_name') }} </th>
                                <th width='10%'>{{ trans('cruds.campaign.fields.promo_code') }} </th>
                                <th width='10%'>{{ trans('cruds.campaign.fields.start_date') }} </th>
                                <th width='10%'>{{ trans('cruds.campaign.fields.end_date') }} </th>
                                <th width='10%'>{{ trans('cruds.campaign.fields.is_code_used') }} </th>
                                

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaign->promoCodes()->get() as $key => $campaign)
                            <tr data-entry-id="{{ $campaign->id }}">
                                <td></td>
                                <td>
                                    {{ ($campaign->userCustomer?$campaign->userCustomer->first_name ." ". $campaign->userCustomer->last_name:"") ?? '' }}
                                </td>
                                <td>
                                    {{ $campaign->promo_code ?? '' }}
                                </td>
                                <td>
                                    {{ $campaign->start_date ?? '' }}
                                </td>
                                <td>
                                    {{ $campaign->end_date ?? '' }}
                                </td>
                                <td>
                                    {{ ($campaign->is_code_used == 1?"Yes":"No") ?? '' }}
                                </td>
                                


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
           
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.campaigns.index') }}">
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
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)


        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [
                [1, 'desc']
            ],
            pageLength: 100,
        });
        let table = $('.datatable-Campaign:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    })
</script>
@endsection