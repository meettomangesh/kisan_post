@extends('layouts.admin')
@section('content')
@can('product_unit_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.product_units.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.product_unit.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.product_unit.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Product-Unit">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.product_unit.fields.id') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.category') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.product_name') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.unit') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.selling_price') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.special_price') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.opening_quantity') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.current_quantity') }}</th>
                        <th>{{ trans('cruds.product_unit.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productUnits as $key => $productUnit)
                        <tr data-entry-id="{{ $productUnit->id }}">
                            <td></td>
                            <td>{{ $productUnit->id ?? '' }}</td>
                            <td><b>{{ ($productUnit->product && $productUnit->product->category) ? $productUnit->product->category->cat_name : "" }}</b></td>
                            <td><b>{{ ($productUnit->product) ? $productUnit->product->product_name : "" }}</b></td>
                            <td><b>{{ ($productUnit->unit) ? $productUnit->unit->unit : "" }}</b></td>
                            <td>{{ round($productUnit->selling_price, 2) ?? '' }}</td>
                            <td>{{ round($productUnit->special_price, 2) ?? '' }}</td>
                            <td>{{ $productUnit->opening_quantity ?? '' }}</td>
                            <td>{{ App\Models\ProductLocationInventory::getProductUnitCurrentQuantity($productUnit->id) }}</td>
                            <td>
                                @can('product_unit_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.product_units.show', $productUnit->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('product_unit_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.product_units.edit', $productUnit->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('product_unit_delete')
                                    <form action="{{ route('admin.product_units.destroy', $productUnit->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                                @can('product_unit_add_or_remove_inventory')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.product_units.addOrRemoveInventory', $productUnit->id) }}">
                                        {{ trans('cruds.product_unit.fields.add_or_remove_inventory') }}
                                    </a>
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('product_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.product_units.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 10,
  });
  let table = $('.datatable-Product-Unit:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection