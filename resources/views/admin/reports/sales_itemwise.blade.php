@extends('layouts.admin')
@section('content')
<style>
    .dataTables_scrollHeadInner {
        width: auto !important;
    }
</style>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.report.fields.sales_itemwise') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-sales-itemwise" id="datatable-sales-itemwise">
                <thead>
                    <tr>
                        <!-- <th></th> -->
                        <th></th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_id') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.product_name') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.selling_price') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.special_price') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.item_qty') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_date') }}</th>
                        <th>{{ trans('cruds.sales_itemwise.fields.order_status') }}</th>
                        <th>Action</th>
                    </tr>
                    <tr role="row" class="filter">
                        <th></th>
                        <th><input class="search" name="order_id" type="text" placeholder="Search" /></th>
                        <th><input class="search" name="product_name" type="text" placeholder="Search" /></th>
                        <th><input class="search" name="selling_price" type="text" placeholder="Search" /></th>
                        <th><input class="search" name="special_price" type="text" placeholder="Search" /></th>
                        <th><input class="search" name="item_quantity" type="text" placeholder="Search" /></th>
                        <th>
                            <input class="search form-control" type="date" name="order_date" id="order_date" max="{{ date('Y-m-d') }}" />
                        </th>
                        <th><input class="search" name="order_status" type="text" placeholder="Search" /></th>
                        <th> <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="{!! trans('admin::messages.search') !!}"><i class="fa fa-search"></i></button>
                            <button class="btn btn-sm red filter-cancel margin-bottom-5" title="{!! trans('admin::messages.reset') !!}"><i class="fa fa-times"></i></button>
                        </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

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
            pageLength: 10,
        });
        // $('.datatable-sales-itemwise thead tr:eq(1) th').each(function(i) {
        //     $('input', this).on('keyup change', function() {
        //         if (table.column(i).search() !== this.value) {
        //             table.column(i).search(this.value).draw();
        //         }
        //     });
        // });
        let table = $('.datatable-sales-itemwise').DataTable({
            processing: true,
            serverSide: true,
            // buttons: dtButtons,
            columns: [{
                    data: null,
                    name: 'rownum',
                    searchable: false
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'selling_price',
                    name: 'selling_price'
                },
                {
                    data: 'special_price',
                    name: 'special_price'
                },
                {
                    data: 'item_quantity',
                    name: 'item_quantity'
                },
                {
                    data: 'order_date',
                    name: 'order_date'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: null,
                    name: 'action',
                    sortable: false
                }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;
                var page = api.page();
                var recNum = null;
                var displayLength = settings._iDisplayLength;
                api.column(0, {
                    page: 'current'
                }).data().each(function(group, i) {
                    // recNum = ((page * displayLength) + i + 1);
                    $(rows).eq(i).children('td:first-child').html(recNum);
                });
                api.column(8, {
                    page: 'current'
                }).data().each(function(data, i) {
                    var orderStatus = "";
                    if (data.order_status == 0) {
                        orderStatus = 'Pending';
                    } else if (data.order_status == 1) {
                        orderStatus = 'Placed';
                    } else if (data.order_status == 2) {
                        orderStatus = 'Picked';
                    } else if (data.order_status == 3) {
                        orderStatus = 'Out for delivery';
                    } else if (data.order_status == 4) {
                        orderStatus = 'Delivered';
                    } else if (data.order_status == 5) {
                        orderStatus = 'Cancelled';
                    }
                    $(rows).eq(i).children('td:nth-child(8)').html(orderStatus);
                });

                api.column(8, {
                    page: 'current'
                }).data().each(function(group, i) {
                    // recNum = ((page * displayLength) + i + 1);
                    $(rows).eq(i).children('td:nth-child(9)').html(null);
                });
            },
            ajax: {
                url: "sales-itemwise/data",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }
        });
        $('.filter-cancel').on('click', function(e) {

            $('.datatable-sales-itemwise thead tr:eq(1) th').each(function(i) {
                $('input', this).val('');

                if (table.column(i).search() !== $('input', this).val()) {
                    table.column(i).search($('input', this).val());
                }
            });
            table.draw();
        });
        $('.search').on('keypress', function(e) {
            if (e.which == 13) {
                $('.filter-submit').click();
            }
        });

        $('.filter-submit').on('click', function(e) {
            $('.datatable-sales-itemwise thead tr:eq(1) th').each(function(i) {
                if ($('input', this).val()) {
                    if (table.column(i).search() !== $('input', this).val()) {
                        table.column(i).search($('input', this).val()).draw();
                    }
                }

            });
        });
    });
</script>
@stop

@endsection