<?php

namespace App\Http\Controllers\Admin;

use App\Models\Unit;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUnitRequest;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Category;

class UnitsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $units = Unit::all();
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        abort_if(Gate::denies('unit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all()->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.units.create', compact('categories'));
    }

    public function store(StoreUnitRequest $request)
    {
        $unit = Unit::create($request->all());
        return redirect()->route('admin.units.index');
    }

    public function edit(Unit $unit)
    {
        abort_if(Gate::denies('unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all()->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $unit->load('category');
        return view('admin.units.edit', compact('unit','categories'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->all());
        return redirect()->route('admin.units.index');
    }

    public function show(Unit $unit)
    {
        abort_if(Gate::denies('unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $unit->load('category');
        return view('admin.units.show', compact('unit'));
    }

    public function destroy(Unit $unit)
    {
        abort_if(Gate::denies('unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $unit->delete();
        return back();
    }

    public function massDestroy(MassDestroyUnitRequest $request)
    {
        Unit::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
