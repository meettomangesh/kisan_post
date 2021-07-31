<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDeliveryBoyRequest;
use App\Http\Requests\StoreDeliveryBoyRequest;
use App\Http\Requests\UpdateDeliveryBoyRequest;
use App\Role;
use App\DeliveryBoy;
use App\User;
use App\Models\UserDetails;
use App\Region;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\EmailHelper;
use App\Helper\DataHelper;
use App\Helper\NotificationHelper;
use App\Models\UserCommunicationMessages;

class DeliveryBoysController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('deliveryboy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryboys = User::whereHas(
            'roles',
            function ($q) {
                $q->where('title', 'Delivery Boy');
            }
        )->get();

        $temp = [];
        foreach ($deliveryboys as $key => $deliveryboy) {
            $temp[$key] = $deliveryboy;
            $temp[$key]->password_plain = DataHelper::decrypt($deliveryboy->password_plain);
        }
        $deliveryboys = collect($temp);

        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        return view('admin.deliveryboys.index', compact('deliveryboys', 'regions'));
    }

    public function create()
    {
        abort_if(Gate::denies('deliveryboy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->where('title', 'Delivery Boy')->pluck('title', 'id');
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        return view('admin.deliveryboys.create', compact('roles', 'regions'));
    }

    public function store(StoreDeliveryBoyRequest $request)
    {
        //print_r($request->all());exit;
        $input = $request->all();
        $input['password_plain'] = DataHelper::encrypt($input['password']);
        $user = User::create($input);
        $user->roles()->sync($request->input('roles', []));
        $user->regions()->sync($request->input('regions', []));
        $user->details()->updateOrCreate([], ['role_id' => 3]);

        $emailVerifyUrl = config('services.miscellaneous.EMAIL_VERIFY_URL');
        if (!empty($request->email)) {
            $request['email_verify_key'] = DataHelper::emailVerifyKey();
        }
        if (!empty($request->email)) {
            EmailHelper::sendEmail(
                'IN_APP_EMAIL_VERIFICATION',
                [
                    'link' => $emailVerifyUrl . 'verify?key=' . $request['email_verify_key'],
                    'email_to' => $request->email, //$request->email_address
                    'customerName' => $user->first_name . " " . $user->last_name,
                    'isEmailVerified' => 1
                ],
                [
                    'attachment' => []
                ]
            );
        }

        return redirect()->route('admin.deliveryboys.index');
    }

    public function edit(User $deliveryboy)
    {
        abort_if(Gate::denies('deliveryboy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->where('title', 'Delivery Boy')->pluck('title', 'id');
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        $deliveryboy->load('roles');
        $deliveryboy->load('regions');

        return view('admin.deliveryboys.edit', compact('roles', 'deliveryboy', 'regions'));
    }

    public function update(UpdateDeliveryBoyRequest $request, User $deliveryboy)
    {
        if ($request->hasfile('pan_card_photo')) {
            $deliveryboy->details->pan_card_photo =  DataHelper::uploadImage($request->file('pan_card_photo'), '/images/documents/', $deliveryboy->id);
        }
        if ($request->hasfile('rc_book_photo')) {
            $deliveryboy->details->rc_book_photo =  DataHelper::uploadImage($request->file('rc_book_photo'), '/images/documents/', $deliveryboy->id);
        }
        if ($request->hasfile('license_card_photo')) {
            $deliveryboy->details->license_card_photo =  DataHelper::uploadImage($request->file('license_card_photo'), '/images/documents/', $deliveryboy->id);
        }
        if ($request->hasfile('aadhar_card_photo')) {
            $deliveryboy->details->aadhar_card_photo =  DataHelper::uploadImage($request->file('aadhar_card_photo'), '/images/documents/', $deliveryboy->id);
        }
        if ($request->hasfile('user_photo')) {
            $deliveryboy->details->user_photo =  DataHelper::uploadImage($request->file('user_photo'), '/images/documents/', $deliveryboy->id);
        }
        $deliveryboy->details->update();
        $input = $request->all();
        $input['password_plain'] = DataHelper::encrypt($input['password']);

        $deliveryboy->update($input);
        $deliveryboy->roles()->sync($request->input('roles', []));
        $deliveryboy->regions()->sync($request->input('regions', []));
        return redirect()->route('admin.deliveryboys.index');
    }

    public function show(User $deliveryboy)
    {
        abort_if(Gate::denies('deliveryboy_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryboy->load('roles');
        $deliveryboy->load('regions');
        $deliveryboy->load('details');
        return view('admin.deliveryboys.show', compact('deliveryboy'));
    }

    public function destroy(User $deliveryboy)
    {
        abort_if(Gate::denies('deliveryboy_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deliveryboy->delete();

        return back();
    }

    public function massDestroy(MassDestroyDeliveryBoyRequest $request)
    {
        DeliveryBoy::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function changeKYCStatus(Request $request)
    {
        $userDetails = UserDetails::find($request->id)->update(['status' => $request->status]);
        $user =  User::find($request->user_id);
        if ($request->status == 2) {
            $notifyHelper = new NotificationHelper();
            //print_r($user);exit;
            $notificationContent = NotificationHelper::getPushNotificationTemplate('IN_APP_ACCOUNT_ACTIVATED_DELIVERY_BOY', '', [
                'name' => $user->first_name
            ]);

            $notifyHelper->setParameters(["user_id" => array($request->user_id), "deep_link" => $notificationContent['deeplink'], "details" => json_encode(array('orderNo' => 0, 'userId' => $request->user_id, 'type' => 'Activated'))], $notificationContent['title'], $notificationContent['message']);
            $user->notify($notifyHelper);
        }
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
