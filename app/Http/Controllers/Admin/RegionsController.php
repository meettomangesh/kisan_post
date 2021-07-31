<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\Region;
use App\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRegionRequest;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\PinCode;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;


class RegionsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pin_code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $regions = Region::all();

        return view('admin.regions.index', compact('regions'));
    }

    public function create()
    {
        abort_if(Gate::denies('pin_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pin_codes = PinCode::all()->pluck('pin_code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.regions.create', compact('pin_codes'));
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->all());
        $region->pin_codes()->sync($request->input('pin_codes', []));

        return redirect()->route('admin.regions.index');
    }

    public function edit(Region $region)
    {
        abort_if(Gate::denies('region_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pin_codes = PinCode::all()->pluck('pin_code', 'id');

        $region->load('pin_codes');

        return view('admin.regions.edit', compact('pin_codes', 'region'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $region->update($request->all());
        $region->pin_codes()->sync($request->input('pin_codes', []));

        return redirect()->route('admin.regions.index');
    }

    public function show(Region $region)
    {
        abort_if(Gate::denies('region_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $region->load('pin_codes');

        return view('admin.regions.show', compact('region'));
    }

    public function destroy(Region $region)
    {
        abort_if(Gate::denies('region_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //$region->pin_codes()->delete();
        $region->delete();

        return back();
    }

    public function massDestroy(MassDestroyRegionRequest $request)
    {   
        //Region::whereIn('id', request('ids'))->pin_codes()->delete();
        Region::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
