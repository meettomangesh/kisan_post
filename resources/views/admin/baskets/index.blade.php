@extends('layouts.admin')
@section('content')
@can('basket_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.baskets.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.basket.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.basket.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Basket">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.basket.fields.id') }}</th>
                        <th>{{ trans('cruds.basket.fields.basket_image') }}</th>
                        <th>{{ trans('cruds.basket.fields.basket_name') }}</th>
                        <th>{{ trans('cruds.basket.fields.sku') }}</th>
                        <th>{{ trans('cruds.basket.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($baskets as $key => $basket)
                        <tr data-entry-id="{{ $basket->id }}">
                            <td></td>
                            <td>{{ $basket->id ?? '' }}</td>
                            <!-- <td><img src="{{ asset($basket->images)  }}" alt="" width="60" height="60"></td> -->
                            <td><img src="{{ asset(App\Models\ProductImages::getFirstImage($basket->id))  }}" alt="" width="60" height="60"></td>
                            <td>{{ $basket->product_name ?? '' }}</td>
                            <td>{{ $basket->sku ?? '' }}</td>
                            <td>
                                @can('basket_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.baskets.show', $basket->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('basket_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.baskets.edit', $basket->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('basket_delete')
                                    <form action="{{ route('admin.baskets.destroy', $basket->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('basket_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.baskets.massDestroy') }}",
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
  let table = $('.datatable-Basket:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection