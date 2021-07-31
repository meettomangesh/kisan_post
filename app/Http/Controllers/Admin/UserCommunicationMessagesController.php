<?php

namespace App\Http\Controllers\Admin;

use App\Helper\DataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserCommunicationMessagesRequest;
use App\Role;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserCommunicationMessages;
use App\Helper\EmailHelper;
use Carbon\Carbon as Carbon;
use App\Region;
use DB;
use Auth;

class UserCommunicationMessagesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('communication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $result = EmailHelper::send(array(
        //     'subject' => "Get well soon subject",
        //     'message' => '<table style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#000000;line-height:22px;width:600px" cellspacing="0" cellpadding="0" align="center">
        //     <tbody>
        //         <tr>
        //             <td style="border-top:3px solid #a1c13a;height:3px" valign="top" align="center">&nbsp;</td>
        //         </tr>
        //         <tr>
        //             <td style="padding: 10px 0;" align="center" valign="middle" width="90">
        //                 <a href="http://www.Frendzi.com" target="_blank">
        //                     <img class="CToWUd" src="http://13.234.133.94/admin/assets/images/brand-logo.png" alt="Frendzi" height="80" />
        //                 </a>
        //             </td>
        //         </tr>
        //         <tr>
        //             <td style="border-bottom:1px solid #ececec;height:1px" valign="top" align="center">&nbsp;</td>
        //         </tr>
        //         <tr>
        //             <td style="background-color:#f6f6f6" valign="top" align="center">
        //                 <table style="width:94%" cellspacing="12" cellpadding="0">
        //                     <tbody>
        //                         <tr>
        //                             <td style="font-size:14px;color:#454545;font-weight:bold" valign="middle" height="30" align="left">Dear Admin,</td>
        //                         </tr>
        //                         <tr>
        //                             <td style="font-size:14px;color:#454545;line-height:24px;padding-bottom:10px" valign="top" align="left">Refund needs to process for    Here are the details :</td>
        //                         </tr>
        //                         <tr>
        //                             <td style="font-size:14px;color:#454545;line-height:24px;padding-bottom:10px" valign="top" align="left">
        //                                 <label>
        //                                     <span>Sponsor Name:</span> 

        //                                 </lable>
        //                                 <br>
        //                                     <label>
        //                                         <span>Sponsor Code:</span> 

        //                                     </lable>
        //                                     <br>
        //                                         <label>
        //                                             <span>Amount:</span> 

        //                                         </lable>
        //                                         <br>
        //                                             <label>
        //                                                 <span>Bank Name:</span> 

        //                                             </lable>
        //                                             <br>
        //                                                 <label>
        //                                                     <span>Account Number:</span> 

        //                                                 </lable>
        //                                                 <br>
        //                                                     <label>
        //                                                         <span>Swift Code:</span> 

        //                                                     </lable>
        //                                                 </td>
        //                                             </tr>
        //                                             <tr>
        //                                                 <td style="font-size:14px;color:#454545;line-height:24px;padding-bottom:10px" valign="top" align="left">
        //                 Regards,

        //                                                     <br>
        //                                                         <span style="color:#8cb53f;text-transform:uppercase;font-weight:bold">Team Frendzi</span>
        //                                                     </td>
        //                                                 </tr>
        //                                                 <tr>
        //                                                     <td style="font-size:3px;color:#454545;line-height:4px;" valign="top" align="left">
        //                                                         <hr>
        //                                                         </td>
        //                                                     </tr>
        //                                                     <tr>
        //                                                         <td style="font-size:11px;color:#454545;line-height:16px;padding-bottom:10px" valign="top" align="left">P.S. We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td>
        //                                                     </tr>
        //                                                 </tbody>
        //                                             </table>
        //                                         </td>
        //                                     </tr>
        //                                     <tr>
        //                                         <td style="background-color:#8cb53f;padding:20px 0;text-transform:uppercase;font-weight:bold" valign="top" align="center">
        //                                             <strong style="color:#3c4d1b">Terms & conditions  | Privacy Policy  | Help</strong>
        //                                         </td>
        //                                     </tr>
        //                                 </tbody>
        //                             </table>',
        //     'to' => array(array('email' => 'meettomangesh@gmail.com'), array('email' => 'aky.nagare003@gmail.com')),
        //     'attachment' => array(
        //         array('attachment' => 'images/logo.png'),
        //         array('attachment' => 'images/sample.pdf')
        //     )
        // ));
        // echo 'email is sent' . $result;
        // exit;
        $userCommunicationMessages = UserCommunicationMessages::all();
        foreach ($userCommunicationMessages as $key => $userCommunicationMessage) {
            $notifyType = '';
            $notify = $userCommunicationMessage->notify_users_by;
            $email = trans('cruds.communication.fields.email');
            $push_notification = trans('cruds.communication.fields.push-notification');
            $sms = trans('cruds.communication.fields.sms');
            $sms_notification = trans('cruds.communication.fields.sms-notification');

            $notifyStr = '';
            if ($notify == '1000') {
                $notifyStr = $email;
            } else if ($notify == '0100') {
                $notifyStr = $push_notification;
            } else if ($notify == '0010') {
                $notifyStr = $sms;
            } else if ($notify == '1100') {
                $notifyStr = $email . ', ' . $push_notification;
            } else if ($notify == '1010') {
                $notifyStr = $email . ', ' . $sms;
            } else if ($notify == '1110') {
                $notifyStr = $email . ', ' . $push_notification . ', ' . $sms;
            } else if ($notify == '1001') {
                $notifyStr = $email . ', ' . $sms_notification;
            } else if ($notify == '1011') {
                $notifyStr = $email . ', ' . $sms . ', ' . $sms_notification;
            } else if ($notify == '1101') {
                $notifyStr = $email . ', ' . $push_notification . ', ' . $sms_notification;
            } else if ($notify == '1111') {
                $notifyStr = $email . ', ' . $push_notification . ', ' . $sms . ', ' . $sms_notification;
            } else if ($notify == '0110') {
                $notifyStr = $push_notification . ', ' . $sms;
            } else if ($notify == '0001') {
                $notifyStr = $sms_notification;
            } else if ($notify == '0011') {
                $notifyStr = $sms . ', ' . $sms_notification;
            } else if ($notify == '0101') {
                $notifyStr = $push_notification . ', ' . $sms_notification;
            } else if ($notify == '0111') {
                $notifyStr = $push_notification . ', ' . $sms . ', ' . $sms_notification;
            }
            $userCommunicationMessages[$key]->notifyStr = $notifyStr;  
            
    }
        // print_r($userCommunicationMessages); exit;
        return view('admin.communications.index', compact('userCommunicationMessages'));
    }

    public function create()
    {
        abort_if(Gate::denies('communication_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $loyaltyTierIdsNames = [];
        $merchantListData = [];
        $deepLinkScreeningData = [];
        $deepLinkScreeningDataGolbal = [];
        $deepLinkScreeningDataGolbalList = DataHelper::getDeeplinkData();
        //$roles = Role::all()->pluck('title', 'id');
        $roles = Role::all()->whereNotIn('title', ['Delivery Boy', 'Customer'])->pluck('title', 'id');
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        return view('admin.communications.create', compact('roles', 'loyaltyTierIdsNames', 'merchantListData', 'deepLinkScreeningData', 'deepLinkScreeningDataGolbal', 'deepLinkScreeningDataGolbalList', 'regions'));
    }

    public function store(Request $request)
    {

        $inputs = $request->all();
        $inputs['email'] = isset($inputs['email']) ? $inputs['email'] : 0;
        $inputs['push_notification'] = isset($inputs['push_notification']) ? $inputs['push_notification'] : 0;
        $inputs['sms'] = isset($inputs['sms']) ? $inputs['sms'] : 0;
        $inputs['sms_notification'] = isset($inputs['sms_notification']) ? $inputs['sms_notification'] : 0;
        $inputs['min_points_filter'] = isset($inputs['min_points_filter']) ? $inputs['min_points_filter'] : 0;
        $inputs['max_points_filter'] = isset($inputs['max_points_filter']) ? $inputs['max_points_filter'] : 0;
        $inputs['sms_text'] = isset($inputs['sms_text']) ? $inputs['sms_text'] : '';
        $inputs['uploaded_data'] = isset($inputs['uploaded_data']) ? $inputs['uploaded_data'] : '';
        $inputs['test_email_address'] = isset($inputs['test_email_address']) ? $inputs['test_email_address'] : '';
        $inputs['test_mobile_number'] = isset($inputs['test_mobile_number']) ? $inputs['test_mobile_number'] : '';
        $inputs['created_by'] = Auth::id();
        $inputs['updated_by'] = Auth::id();
        $inputs['notify_users_by'] = $inputs['email'] . $inputs['push_notification'] . $inputs['sms'] . $inputs['sms_notification'];
        $inputs['reference_id'] = 0;
        if ($inputs['message_type'] == 1) {
            $inputs['reference_id'] = isset($inputs['offer_id']) ? $inputs['offer_id'] : 0;
        } else if ($inputs['message_type'] == 3) {
            $inputs['reference_id'] = isset($inputs['product_id']) ? $inputs['product_id'] : 0;
        }

        if ($inputs['push_notification'] > 0 || $inputs['sms_notification'] > 0) {
            $inputs['push_text'] = isset($inputs['push_text']) ? $inputs['push_text'] : '';

            $inputs['deep_link_screen'] = isset($inputs['deep_link_screen']) ? $inputs['deep_link_screen'] : '';
        } else {
            $inputs['push_text'] = '';
            $inputs['deep_link_screen'] = '';
        }
        
       // $inputs['message_send_time'] = '';
        $send_today = isset($inputs['send_today']) ? $inputs['send_today'] : 0;
        if ($send_today > 0) {
            $today_time = $inputs['today_time'];
            $inputs['message_send_time'] = Carbon::now()->toDateString() . ' ' . date('H:i:s', strtotime($today_time));
        } else {
            $originalDate = $inputs['message_send_time'];
            $today_time = $inputs['today_time'];
            $newDate = date("Y-m-d", strtotime($originalDate));
            $newDateTime = date('H:i:s', strtotime($today_time));
            $inputs['message_send_time'] = $newDate . ' ' . $newDateTime;
        }
        $userCommunication = UserCommunicationMessages::create($inputs);
        // if ($send_today > 0) {
        //     $res = \Illuminate\Support\Facades\Artisan::call('send-notifications', ['--notification_id' => $userCommunication->id]);
        // }

        $userCommunication->regions()->sync($request->input('regions', []));
        $userCommunication->users()->sync($request->input('users', []));

        return redirect()->route('admin.communications.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('communication_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
       // print_r($userCommunicationMessages);exit;

        $userCommunicationMessages = UserCommunicationMessages::find($id);

        $notify = $userCommunicationMessages->notify_users_by;
        $email = 0;
        $push_notification = 0;
        $sms = 0;
        $sms_notification = 0;

        if ($notify == '1000') {
            $email = 1;
        } else if ($notify == '0100') {
            $push_notification = 1;
        } else if ($notify == '0010') {
            $sms = 1;
        } else if ($notify == '1100') {
            $email = 1;
            $push_notification = 1;
        } else if ($notify == '1010') {
            $email = 1;
            $sms = 1;
        } else if ($notify == '1110') {
            $email = 1;
            $push_notification = 1;
            $sms = 1;
        } else if ($notify == '1001') {
            $email = 1;
            $sms_notification = 1;
        } else if ($notify == '1011') {
            $email = 1;
            $sms = 1;
            $sms_notification = 1;
        } else if ($notify == '1101') {
            $email = 1;
            $push_notification = 1;
            $sms_notification = 1;
        } else if ($notify == '1111') {
            $email = 1;
            $push_notification = 1;
            $sms = 1;
            $sms_notification = 1;
        } else if ($notify == '0110') {
            $push_notification = 1;
            $sms = 1;
        } else if ($notify == '0001') {
            $sms_notification = 1;
        } else if ($notify == '0011') {
            $sms = 1;
            $sms_notification = 1;
        } else if ($notify == '0101') {
            $push_notification = 1;
            $sms_notification = 1;
        } else if ($notify == '0111') {
            $push_notification = 1;
            $sms = 1;
            $sms_notification = 1;
        }

        $userCommunicationMessages->email = $email;
        $userCommunicationMessages->push_notification = $push_notification;
        $userCommunicationMessages->sms = $sms;
        $userCommunicationMessages->sms_notification = $sms_notification;
        $userCommunicationMessages->message_send_date = date('Y-m-d', strtotime($userCommunicationMessages->message_send_time));
        $userCommunicationMessages->message_send_time = date('g:h A', strtotime($userCommunicationMessages->message_send_time));
        $deepLinkScreeningDataGolbalList = DataHelper::getDeeplinkData();

        $roles = Role::all()->where('title', 'Delivery Boy')->pluck('title', 'id');
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        $inputData = array('user_type' => $userCommunicationMessages->user_role, 'custom_region' => implode(",",$userCommunicationMessages->regions()->get()->pluck('id')->toArray()), 'region_type' => $userCommunicationMessages->region_type);
        $inputData = json_encode($inputData);
        $users = collect(DB::select('call getUserTypeRegionData(?)', [$inputData]))->pluck('name','id');

        return view('admin.communications.edit', compact('userCommunicationMessages','regions', 'users','deepLinkScreeningDataGolbalList'));
    }

    public function update(Request $request, UserCommunicationMessages $userCommunicationMessages)
    {
        // $user->update($request->all());
        // $user->roles()->sync($request->input('roles', []));
        
        
        $inputs = $request->all();
        //  echo '<pre>'; print_r($inputs);
        //  exit;
        $userCommunicationMessages = UserCommunicationMessages::find($inputs['id']);
        $inputs['email'] = isset($inputs['email']) ? $inputs['email'] : 0;
        $inputs['push_notification'] = isset($inputs['push_notification']) ? $inputs['push_notification'] : 0;
        $inputs['sms'] = isset($inputs['sms']) ? $inputs['sms'] : 0;
        $inputs['sms_notification'] = isset($inputs['sms_notification']) ? $inputs['sms_notification'] : 0;
        $inputs['min_points_filter'] = isset($inputs['min_points_filter']) ? $inputs['min_points_filter'] : 0;
        $inputs['max_points_filter'] = isset($inputs['max_points_filter']) ? $inputs['max_points_filter'] : 0;
        $inputs['sms_text'] = isset($inputs['sms_text']) ? $inputs['sms_text'] : '';
        $inputs['uploaded_data'] = isset($inputs['uploaded_data']) ? $inputs['uploaded_data'] : '';
        $inputs['test_email_address'] = isset($inputs['test_email_address']) ? $inputs['test_email_address'] : '';
        $inputs['test_mobile_number'] = isset($inputs['test_mobile_number']) ? $inputs['test_mobile_number'] : '';
        $inputs['created_by'] = Auth::id();
        $inputs['updated_by'] = Auth::id();
        $inputs['notify_users_by'] = $inputs['email'] . $inputs['push_notification'] . $inputs['sms'] . $inputs['sms_notification'];
        $inputs['reference_id'] = 0;
        if ($inputs['message_type'] == 1) {
            $inputs['reference_id'] = isset($inputs['offer_id']) ? $inputs['offer_id'] : 0;
        } else if ($inputs['message_type'] == 3) {
            $inputs['reference_id'] = isset($inputs['product_id']) ? $inputs['product_id'] : 0;
        }

        if ($inputs['push_notification'] > 0 || $inputs['sms_notification'] > 0) {
            $inputs['push_text'] = isset($inputs['push_text']) ? $inputs['push_text'] : '';

            $inputs['deep_link_screen'] = isset($inputs['deep_link_screen']) ? $inputs['deep_link_screen'] : '';
        } else {
            $inputs['push_text'] = '';
            $inputs['deep_link_screen'] = '';
        }
        
       // $inputs['message_send_time'] = '';
        $send_today = isset($inputs['send_today']) ? $inputs['send_today'] : 0;
        if ($send_today > 0) {
            $today_time = $inputs['today_time'];
            $inputs['message_send_time'] = Carbon::now()->toDateString() . ' ' . date('H:i:s', strtotime($today_time));
        } else {
            $originalDate = $inputs['message_send_time'];
            $today_time = $inputs['today_time'];
            $newDate = date("Y-m-d", strtotime($originalDate));
            $newDateTime = date('H:i:s', strtotime($today_time));
            $inputs['message_send_time'] = $newDate . ' ' . $newDateTime;
        }   
        //echo '<pre>'; print_r($userCommunicationMessages); exit;
        $userCommunicationMessages->update($inputs);
        $userCommunicationMessages->regions()->sync($request->input('regions', []));
        $userCommunicationMessages->users()->sync($request->input('users', []));

        return redirect()->route('admin.communications.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('communication_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userCommunicationMessages = UserCommunicationMessages::find($id);

        $notify = $userCommunicationMessages->notify_users_by;
        $notifyType = '';
        
        $email = trans('cruds.communication.fields.email');
        $push_notification = trans('cruds.communication.fields.push-notification');
        $sms = trans('cruds.communication.fields.sms');
        $sms_notification = trans('cruds.communication.fields.sms-notification');

        $notifyStr = '';
        if ($notify == '1000') {
            $notifyStr = $email;
        } else if ($notify == '0100') {
            $notifyStr = $push_notification;
        } else if ($notify == '0010') {
            $notifyStr = $sms;
        } else if ($notify == '1100') {
            $notifyStr = $email . ', ' . $push_notification;
        } else if ($notify == '1010') {
            $notifyStr = $email . ', ' . $sms;
        } else if ($notify == '1110') {
            $notifyStr = $email . ', ' . $push_notification . ', ' . $sms;
        } else if ($notify == '1001') {
            $notifyStr = $email . ', ' . $sms_notification;
        } else if ($notify == '1011') {
            $notifyStr = $email . ', ' . $sms . ', ' . $sms_notification;
        } else if ($notify == '1101') {
            $notifyStr = $email . ', ' . $push_notification . ', ' . $sms_notification;
        } else if ($notify == '1111') {
            $notifyStr = $email . ', ' . $push_notification . ', ' . $sms . ', ' . $sms_notification;
        } else if ($notify == '0110') {
            $notifyStr = $push_notification . ', ' . $sms;
        } else if ($notify == '0001') {
            $notifyStr = $sms_notification;
        } else if ($notify == '0011') {
            $notifyStr = $sms . ', ' . $sms_notification;
        } else if ($notify == '0101') {
            $notifyStr = $push_notification . ', ' . $sms_notification;
        } else if ($notify == '0111') {
            $notifyStr = $push_notification . ', ' . $sms . ', ' . $sms_notification;
        }
        $userCommunicationMessages->notifyStr = $notifyStr;  

        $userCommunicationMessages->message_send_date = date('Y-m-d', strtotime($userCommunicationMessages->message_send_time));
        $userCommunicationMessages->message_send_time = date('g:h A', strtotime($userCommunicationMessages->message_send_time));


        $roles = Role::all()->where('title', 'Delivery Boy')->pluck('title', 'id');
        $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');

        $inputData = array('user_type' => $userCommunicationMessages->user_role, 'custom_region' => implode(",",$userCommunicationMessages->regions()->get()->pluck('id')->toArray()), 'region_type' => $userCommunicationMessages->region_type);
        $inputData = json_encode($inputData);
        $users = collect(DB::select('call getUserTypeRegionData(?)', [$inputData]));
       
        return view('admin.communications.show', compact('userCommunicationMessages','regions', 'users'));

    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('communication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function checkPastTime($todayTime)
    {
        $now = Carbon::now()->toDateTimeString();
        $messageSendTime = Carbon::now()->toDateString() . ' ' . date('H:i:s', strtotime($todayTime));
        $response = [];
        $response['status'] = '';
        $response['message'] = '';
        if (strtotime($now) > strtotime($messageSendTime)) { //today's time but past time
            $response['status'] = 'error';
            $response['message'] = 'Send Notification Time should be greater than current time.';
        }
        return $response;
    }

    public function getUserTypeData(Request $request)
    {
        $input = $request->all();

        // formData.append('user_type', id);
        // formData.append('custom_region', customRegion);
        // formData.append('region_type', regionTypeFlag);
        $inputData = array('user_type' => $input['user_type'], 'custom_region' => $input['custom_region'], 'region_type' => $input['region_type']);
        $inputData = json_encode($inputData);
        $users = collect(DB::select('call getUserTypeRegionData(?)', [$inputData]));


        //Array ( [user_type] => 3 [custom_region] => 4,5 [region_type] => 2 )
        // if ($input['user_type'] == 3) {
        //     $users = User::select(DB::raw('CONCAT(users.first_name, " ", users.last_name) AS name'), 'users.id')
        //         ->whereHas(
        //             'roles',
        //             function ($q) {
        //                 $q->where('title', 'Delivery Boy');
        //             }
        //         )->get();
        // } else if ($input['user_type'] == 4) {
        //     $users = User::select(DB::raw('CONCAT(users.first_name, " ", users.last_name) AS name'), 'users.id')
        //         ->whereHas(
        //             'roles',
        //             function ($q) {
        //                 $q->where('title', 'Customer');
        //             }
        //         )->get();
        // }


        $response['user_details'] = $users->pluck('name', 'id');

        $response['status'] = '';
        $response['message'] = '';

        return response()->json($response);
    }



    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductCreateRequest $request, Modules\Admin\Models\CustomerCommunicationMessages $customerCommunicationMessages
     * @return json encoded Response
     */
    public function sendTestSms(Request $request)
    {
        $inputs = $request->all();

        if (array_key_exists($inputs['merchant_id'], $this->fromArray)) {
            $from = $this->fromArray[$inputs['merchant_id']];
        }

        $response = SmsHelper::send($inputs['test_mobile_numbers'], $inputs['sms_text'], $from);

        $result = array();
        if ($response == true) {
            $result['status'] = 'success';
            $result['message'] = 'Test SMS Send.';
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Test SMS Sending Problem.';
        }
        return response()->json($result);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductCreateRequest $request, Modules\Admin\Models\CustomerCommunicationMessages $customerCommunicationMessages
     * @return json encoded Response
     */
    public function sendTestEmail(Request $request)
    {
        $inputs = $request->all();

        $responseMail = EmailHelper::getCustomerEmailTemplate('IN_USER_COMMUNICATION_MESSAGES', $inputs['email_body']);

        $toEmails = (new EmailHelper())->explodeEmails($inputs['test_email_addresses']);
        $response = EmailHelper::send(array(
            'subject' => isset($inputs['email_subject']) ? $inputs['email_subject'] : '',
            'message' => $responseMail,
            'from' => [
                "name" => isset($inputs['email_from_name']) ? $inputs['email_from_name'] : '',
                "email" => isset($inputs['email_from_email']) ? $inputs['email_from_email'] : '',
               
            ],
            'to' => $toEmails,
            "isEmailVerified" => 1,
        ));


        $result = array();
        if ($response == true) {
            $result['status'] = 'success';
            $result['message'] = 'Test Email Send.';
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Test Email Sending Problem.';
        }
        return response()->json($result);
    }
}
