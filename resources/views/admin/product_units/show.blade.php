@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.product_unit.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product_units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.id') }}</th>
                        <td>{{ $productUnit->id ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.category') }}</th>
                        <td><b>{{ ($productUnit->product->category) ? $productUnit->product->category->cat_name : "" }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.product_name') }}</th>
                        <td><b>{{ ($productUnit->product) ? $productUnit->product->product_name : "" }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.unit') }}</th>
                        <td><b>{{ ($productUnit->unit) ? $productUnit->unit->unit : "" }}</b></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.selling_price') }}</th>
                        <td>{{ round($productUnit->selling_price, 2) ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.special_price') }}</th>
                        <td>{{ round($productUnit->special_price, 2) ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.special_price_start_date') }}</th>
                        <td>{{ $productUnit->special_price_start_date ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.special_price_end_date') }}</th>
                        <td>{{ $productUnit->special_price_end_date ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.min_quantity') }}</th>
                        <td>{{ $productUnit->min_quantity ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.max_quantity') }}</th>
                        <td>{{ $productUnit->max_quantity ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.opening_quantity') }}</th>
                        <td>{{ $productUnit->opening_quantity ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.current_quantity') }}</th>
                        <td>{{ ($productUnit->id) ? App\Models\ProductLocationInventory::getProductUnitCurrentQuantity($productUnit->id) : "" }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.product_unit.fields.status') }}</th>
                        <td><b>
                            @if($productUnit->status == 1)
                                {{ trans('cruds.product_unit.fields.active') }}
                            @else
                                {{ trans('cruds.product_unit.fields.inactive') }}
                            @endif</b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.products.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection