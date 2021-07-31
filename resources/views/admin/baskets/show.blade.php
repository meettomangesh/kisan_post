@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.basket.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.baskets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.id') }}</th>
                        <td>{{ $basket->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.basket_image') }}</th>
                        <!-- <td><img src="{{ asset($basket->images) }}" alt="" width="50" height="50"></td> -->
                        <td><img src="{{ asset(App\Models\ProductImages::getFirstImage($basket->id)) }}" alt="" width="50" height="50"></td>
                    </tr>
                   
                    <tr>
                        <th>{{ trans('cruds.basket.fields.basket_name') }}</th>
                        <td><b>{{ $basket->product_name }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.sku') }}</th>
                        <td><b>{{ $basket->sku }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.short_description') }}</th>
                        <td>{{ $basket->short_description }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.selling_price') }}</th>
                        <td>{{ round($basket->selling_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.special_price') }}</th>
                        <td>{{ round($basket->special_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.special_price_start_date') }}</th>
                        <td>{{ $basket->special_price_start_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.special_price_end_date') }}</th>
                        <td>{{ $basket->special_price_end_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.min_quantity') }}</th>
                        <td>{{ $basket->min_quantity }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.max_quantity') }}</th>
                        <td>{{ $basket->max_quantity }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.opening_quantity') }}</th>
                        <td>{{ $basket->opening_quantity }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.basket.fields.stock_availability') }}</th>
                        <td><b>
                            @if($basket->status == 1)
                                {{ trans('cruds.basket.fields.in_stock') }}
                            @else
                                {{ trans('cruds.basket.fields.out_of_stock') }}
                            @endif</b>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.basket.fields.productUnits') }}
                        </th>
                        <td>
                            @foreach($basket->productUnits as $key => $productUnit)
                                <span class="label label-info"><?php echo $productUnit->product->product_name.' ('.$productUnit->unit->unit.') ';?> </span>
                            @endforeach
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('cruds.basket.fields.status') }}</th>
                        <td><b>
                            @if($basket->status == 1)
                                {{ trans('cruds.basket.fields.active') }}
                            @else
                                {{ trans('cruds.basket.fields.inactive') }}
                            @endif</b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.baskets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection