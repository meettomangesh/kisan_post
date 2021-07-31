<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Role;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CustomerLoyalty;
use App\Region;
use App\Helper\NotificationHelper;

class CustomersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('customers_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $customers = CustomerLoyalty::all();

        // return view('admin.customers.index', compact('customers'));

        // $user = new User();
        // $notifyHelper = new NotificationHelper();
        
        // $notifyHelper->setParameters(["name"=>"Mangesh Navale","email" =>"meettomangesh@gmail.com"],'Title','Body');
        // echo "Inside User controller-29";
        // //app(FcmChannel)
        // print_r($user->notify($notifyHelper));
        // exit;
        $customers = User::whereHas(
            'roles', function($q){
                $q->where('title', 'Customer');
            }
        )->get();
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        return view('admin.customers.index', compact('customers','regions'));

    }

    public function create()
    {


        abort_if(Gate::denies('customers_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->where('title', 'Customer')->pluck('title', 'id');
       // $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');
         
        return view('admin.customers.create', compact('roles'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));
        //$user->regions()->sync($request->input('regions', []));


        return redirect()->route('admin.customers.index');
    }

    public function edit(User $customer)
    {

        
        abort_if(Gate::denies('customers_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->where('title', 'Customer')->pluck('title', 'id');
       // $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        $customer->load('roles');
        $customer->load('regions');

        return view('admin.customers.edit', compact('roles', 'customer'));

    }

    public function update(UpdateCustomerRequest $request, User $customer)
    {
        $customer->update($request->all());
        $customer->roles()->sync($request->input('roles', []));
 
        return redirect()->route('admin.customers.index');
    }

    public function show(User $customer)
    {

        abort_if(Gate::denies('customers_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customer->load('roles');


        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(User $cutomer)
    {
        abort_if(Gate::denies('customers_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cutomer->delete();

        return back();
    }

    public function massDestroy(MassDestroyCustomerRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
