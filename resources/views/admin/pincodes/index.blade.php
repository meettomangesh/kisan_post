@extends('layouts.admin')
@section('content')
@can('city_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.pincodes.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.pin_code.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.pin_code.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-PinCode">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.pin_code.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.pin_code.fields.pin_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.pin_code.fields.country') }}
                        </th>
                        <th>
                            {{ trans('cruds.pin_code.fields.state') }}
                        </th>
                        <th>
                            {{ trans('cruds.pin_code.fields.city') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pincodes as $key => $pincode)
                        <tr data-entry-id="{{ $pincode->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $pincode->id ?? '' }}
                            </td>
                            <td>
                                {{ $pincode->pin_code ?? '' }}
                            </td>
                            <td>
                                {{ $pincode->country->name ?? '' }}
                            </td>
                            <td>
                                {{ $pincode->state->name ?? '' }}
                            </td>
                            <td>
                                {{ $pincode->city->name ?? '' }}
                            </td>
                            <td>
                                @can('pin_code_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.pincodes.show', $pincode->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('pin_code_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.pincodes.edit', $pincode->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('pin_code_delete')
                                    <form action="{{ route('admin.pincodes.destroy', $pincode->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('pin_code_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.pincodes.massDestroy') }}",
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
    pageLength: 100,
  });
  let table = $('.datatable-PinCode:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection