<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\PinCode;
use App\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPinCodeRequest;
use App\Http\Requests\StorePinCodeRequest;
use App\Http\Requests\UpdatePinCodeRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;


class PinCodesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pin_code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pincodes = PinCode::all();

        return view('admin.pincodes.index', compact('pincodes'));
    }

    public function create()
    {
        abort_if(Gate::denies('pin_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = Country::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $states = State::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.pincodes.create', compact('countries','states','cities'));
    }

    public function store(StorePinCodeRequest $request)
    {
        $pincode = PinCode::create($request->all());

        return redirect()->route('admin.pincodes.index');
    }

    public function edit(PinCode $pincode)
    {
        abort_if(Gate::denies('pin_code_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = Country::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pincode->load('country');

        $states = State::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pincode->load('state');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pincode->load('city');

        return view('admin.pincodes.edit', compact('countries', 'states','cities', 'pincode'));
    }

    public function update(UpdatePinCodeRequest $request, PinCode $pincode)
    {
        $pincode->update($request->all());

        return redirect()->route('admin.pincodes.index');
    }

    public function show(PinCode $pincode)
    {
        abort_if(Gate::denies('pin_code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pincode->load('country');
        $pincode->load('state');
        $pincode->load('city');

        return view('admin.pincodes.show', compact('pincode'));
    }

    public function destroy(PinCode $pincode)
    {
        abort_if(Gate::denies('pin_code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pincode->delete();

        return back();
    }

    public function massDestroy(MassDestroyPinCodeRequest $request)
    {
        PinCode::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getStates($id)
    {
        $states = DB::table("states")->where("country_id", $id)->pluck("name", "id");
        return json_encode($states);
    }
    public function getCities($country_id,$state_id)
    {
        $cities = DB::table("cities")->where("country_id", $country_id)->where("state_id", $state_id)->pluck("name", "id");
        return json_encode($cities);
    }
}
