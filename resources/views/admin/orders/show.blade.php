@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.order.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label><b>{{ trans('cruds.order.fields.order_status') }}:</b>
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
                    </label>

                    <h4>{{ trans('cruds.order.fields.customer_details') }}:</h4>
                    <label><b>{{ $customerOrder->userCustomer->first_name }}</b> <b>{{ $customerOrder->userCustomer->last_name ?? '' }}</b></label><br>
                    <label>{{ $customerOrder->userCustomer->mobile_number }}</label><br>
                    <label><b>{{ trans('cruds.order.fields.address') }}:</b></label><br>
                    <label>{{ $customerOrder->customerShippingAddress->address ?? '' }}</label><br>
                    <label>{{ $customerOrder->customerShippingAddress->landmark ?? '' }}</label><br>
                    <label>{{ $customerOrder->customerShippingAddress->pin_code ?? '' }}, {{ $customerOrder->customerShippingAddress->area ?? '' }}</label><br>
                    <label>{{ $customerOrder->customerShippingAddress->mobile_number ?? '' }}</label><br>
                    <label>{{ $customerOrder->customerShippingAddress->city->name ?? '' }}, {{ $customerOrder->customerShippingAddress->state->name ?? '' }}</label>
                </div>
                <div class="col-md-6">
                    <label><b>{{ trans('cruds.order.fields.payment_type') }}:</b> {{ $customerOrder->payment_type }}</label>
                    <h4>{{ trans('cruds.order.fields.delivery_boy_details') }}:</h4>
                    <label><b>{{ $customerOrder->userDeliveryBoy->first_name ?? '' }}</b> <b>{{ $customerOrder->userDeliveryBoy->last_name ?? '' }}</b></label><br>
                    <label>{{ $customerOrder->userDeliveryBoy->mobile_number ?? '' }}</label>
                </div>
            </div>

            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('cruds.order.fields.products') }}</th>
                        <th>{{ trans('cruds.order.fields.mrp') }}</th>
                        <th>{{ trans('cruds.order.fields.price') }}</th>
                        <th>{{ trans('cruds.order.fields.total_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerOrderDetails as $key => $value)
                        <tr data-entry-id="{{ $value->id }}">
                            <td>{{ $value->product->product_name ?? '' }}<br>
                                @if($value->is_basket == 0)
                                    {{ $value->productUnit->unit->unit }}
                                @endif</td>
                            <td>{{ round($value->selling_price, 2) ?? '' }}</td>
                            <td>@if($value->special_price > 0)
                                    {{ round($value->special_price, 2) }} x {{ $value->item_quantity }}
                                @elseif($value->selling_price > 0)
                                    {{ round($value->selling_price, 2) }} x {{ $value->item_quantity }}
                                @endif</td>
                            <td>@if($value->special_price > 0)
                                    {{ round($value->special_price, 2) * $value->item_quantity }}
                                @elseif($value->selling_price > 0)
                                    {{ round($value->selling_price, 2) * $value->item_quantity }}
                                @endif</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{ trans('cruds.order.fields.gross_amount') }}: </b></td>
                        <td>{{ round($customerOrder->gross_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{ trans('cruds.order.fields.discounted_amount') }}: </b></td>
                        <td>{{ round($customerOrder->discounted_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{ trans('cruds.order.fields.delivery_charge') }}: </b></td>
                        <td>{{ round($customerOrder->delivery_charge, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{ trans('cruds.order.fields.net_amount') }}: </b></td>
                        <td>{{ round($customerOrder->net_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection