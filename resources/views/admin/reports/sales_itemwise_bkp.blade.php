
@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.fields.sales_itemwise') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-sales-itemwise">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.sales_itemwise.fields.id') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_id') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.product_name') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.selling_price') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.special_price') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.item_qty') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_date') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_status') }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th width="10%"><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input class="form-control" type="date" name="order_date" max="{{ date('Y-m-d') }}" value="{{ old('order_date', '') }}"></th>
                        <th><input type="text" placeholder="Search" /></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesItemsData as $key => $item)
                        <tr data-entry-id="{{ $item->order_id }}">
                            <td></td>
                            <td>{{ $srNo = $srNo + 1 }}</td>
                            <td>{{ $item->order_id ?? '' }}</td>
                            <td>
                                @if($item->is_basket == 0)
                                    <b>{{ $item->product_name }}</b>{{ " (" }}{{ $item->unit.")" }}
                                @elseif($item->is_basket == 1)
                                    <b>{{ $item->product_name }}</b>{{ " (" }}{{ $item->basket_products.")" }}
                                @endif
                            </td>
                            <td>{{ round($item->selling_price, 2) ?? '' }}</td>
                            <td>{{ round($item->special_price, 2) ?? '' }}</td>
                            <td>{{ $item->item_quantity ?? '' }}</td>
                            <td>{{ $item->order_date ?? '' }}</td>
                            <td>
                                @if($item->order_status == 1)
                                {{ trans('cruds.sales_itemwise.fields.placed') }}
                                @elseif($item->order_status == 2)
                                {{ trans('cruds.sales_itemwise.fields.picked') }}
                                @elseif($item->order_status == 3)
                                {{ trans('cruds.sales_itemwise.fields.out_for_delivery') }}
                                @elseif($item->order_status == 4)
                                {{ trans('cruds.sales_itemwise.fields.delivered') }}
                                @elseif($item->order_status == 5)
                                {{ trans('cruds.sales_itemwise.fields.cancelled') }}
                                @endif
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
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [[ 1, 'desc' ]],
            pageLength: 10,
        });
        $('.datatable-sales-itemwise:not(.ajaxTable) thead tr:eq(1) th').each(function(i) {
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        let table = $('.datatable-sales-itemwise:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });
    });
</script>
@endsection