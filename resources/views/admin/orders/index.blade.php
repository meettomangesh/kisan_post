@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.order.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Order">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('cruds.order.fields.id') }}</th>
                        <th>{{ trans('cruds.order.fields.customer_name') }} <br> {{ trans('cruds.order.fields.mobile_number') }}</th>
                        <th>{{ trans('cruds.order.fields.delivery_boy_name') }} <br> {{ trans('cruds.order.fields.mobile_number') }}</th>
                        <th>{{ trans('cruds.order.fields.net_amount') }}</th>
                        <th>{{ trans('cruds.order.fields.gross_amount') }}</th>
                        <th>{{ trans('cruds.order.fields.discounted_amount') }}</th>
                        <th>{{ trans('cruds.order.fields.delivery_charge') }}</th>
                        <th>{{ trans('cruds.order.fields.payment_type') }}</th>
                        <th>{{ trans('cruds.order.fields.delivery_date') }}</th>
                        <th>{{ trans('cruds.order.fields.status') }}</th>
                        <th>Needs Attention</th>
                        <th  width="30%">{{ trans('cruds.order.fields.actions') }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th></th>
                        <th><input type="text" placeholder="Search" /></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerOrders as $key => $customerOrder)
                    <tr data-entry-id="{{ $customerOrder->id }}">
                        <td width="10%"></td>
                        <td>{{ $customerOrder->id ?? '' }}</td>
                        <td><b>{{ $customerOrder->userCustomer->first_name }}</b><br>{{ $customerOrder->userCustomer->mobile_number }}</td>
                        <td><b>{{ $customerOrder->userDeliveryBoy->first_name ?? '-' }}</b><br>{{ $customerOrder->userDeliveryBoy->mobile_number ?? '' }}</td>
                        <td>{{ round($customerOrder->net_amount, 2) ?? '' }}</td>
                        <td>{{ round($customerOrder->gross_amount, 2) ?? '' }}</td>
                        <td>{{ round($customerOrder->discounted_amount, 2) ?? '' }}</td>
                        <td>{{ round($customerOrder->delivery_charge, 2) ?? '' }}</td>
                        <td>{{ $customerOrder->payment_type ?? '' }}</td>
                        <td>
                        @if($customerOrder->needAttention == 1)
                        <p style="color:red">{{ $customerOrder->delivery_date ?? '' }}</p>
                        @else
                        {{ $customerOrder->delivery_date ?? '' }}
                        @endif
                            
                        </td>
                        <td>
                            @if($customerOrder->order_status == 1)
                            {{ trans('cruds.order.fields.placed') }}
                            @elseif($customerOrder->order_status == 2)
                            {{ trans('cruds.order.fields.picked') }}
                            @elseif($customerOrder->order_status == 3)
                            {{ trans('cruds.order.fields.out_for_delivery') }}
                            @elseif($customerOrder->order_status == 4)
                            {{ trans('cruds.order.fields.delivered') }}
                            @elseif($customerOrder->order_status == 5)
                            {{ trans('cruds.order.fields.cancelled') }}
                            @endif
                        </td>
                        <td>{{ $customerOrder->needAttention ?? 0 }}</td>
                        <td>
                            @can('order_show')
                            <a class="" data-toggle="tooltip" data-placement="top" title="{{ trans('global.view') }}" href="{{ route('admin.orders.show', $customerOrder->id) }}">
                                <!-- {{ trans('global.view') }} -->
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            @endcan
                            @can('order_cancel')
                            @if($customerOrder->order_status == 1 || $customerOrder->order_status == 2 || $customerOrder->needAttention == 1)
                            <!-- <button class=" cancel_order"   data-toggle="tooltip" data-placement="top" title="{{ trans('cruds.order.fields.cancel_order') }}" data-id="{{ $customerOrder->id }}"> -->
                            <a class="cancel_order" data-toggle="tooltip" data-placement="top" title="{{ trans('cruds.order.fields.cancel_order') }}" data-id="{{ $customerOrder->id }}" href="javascript:void(0);">
                                <!-- {{ trans('cruds.order.fields.cancel_order') }} -->
                                <i class="fa fa-times" aria-hidden="true"></i>
                            <!-- </button> -->
                            </a>
                            @endif
                            @endcan  
                            @can('assign_order_delivery_boy')         
                            @if($customerOrder->needAttention == 1)
                            <a class="" data-toggle="tooltip" data-placement="top" title="{{ trans('cruds.order.fields.re_assign_delivery_boy') }}" href="{{ route('admin.orders.reAssign', $customerOrder->id) }}">
                                <!-- {{ trans('cruds.order.fields.re_assign_delivery_boy') }} -->
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            @endif
                            @endcan
                            @if($customerOrder->customer_invoice_url)
                            <a class=""  target="_blank" data-toggle="tooltip" data-placement="top" title="{{ trans('cruds.order.fields.customer_invoice_url') }}" href="{{ $customerOrder->customer_invoice_url }}">
                                <!-- {{ trans('cruds.order.fields.customer_invoice_url') }} -->
                                <i class="fa fa-money" aria-hidden="true"></i>
                                
                            </a>
                            @endif
                            @if($customerOrder->delivery_boy_invoice_url)
                            <a class=""  target="_blank" data-toggle="tooltip" data-placement="top" title="{{ trans('cruds.order.fields.delivery_boy_invoice_url') }}" href="{{ $customerOrder->delivery_boy_invoice_url }}">
                                <!-- {{ trans('cruds.order.fields.delivery_boy_invoice_url') }} -->
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                            </a>
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
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [
                [9, 'desc'],[7,'asc']
            ],
            pageLength: 50,
        });
        $('.datatable-Order:not(.ajaxTable) thead tr:eq(1) th').each(function(i) {
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });


        let table = $('.datatable-Order:not(.ajaxTable)').DataTable({
            buttons: dtButtons,
            "columnDefs": [
                {
                "targets": [ 9 ],
                "visible": false
            }
            ]
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

        $('.cancel_order').on('click', function() {
            var r = confirm("Are you sure you want to cancel this order?");
            if (r == true) {
                var orderId = $(this).attr("data-id");
                var url = '{{ route("admin.orders.cancelOrder", "") }}';
                $(this).text('Cancelling...');
                url = url + '/' + orderId;
                if (orderId) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $(this).text('Cancel Order');
                            if (data.status == "Success") {
                                alert('Order cancelled successfully.');
                                location.reload();
                            } else {
                                alert('Order not cancelled.');
                            }
                        }
                    });
                }
            }
        });
    })
</script>
@endsection