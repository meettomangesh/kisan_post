<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Models\UserDetails;
use App\Models\PromoCodes;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helper\DataHelper;
use App\Helper\EmailHelper;
use App\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'first_name' => 'required',
            // 'email_address' => 'unique:users,email',
            'mobile_number' => 'required|unique:users,mobile_number',
            'password' => 'required',
            // 'confirm_password' => 'required|same:password',
            'otp_verified' => 'required',
            'pin_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        $input = $request->all();
        $input['referred_by_user_id'] = 0;
        // Verify and Get referred by user
        if (isset($input['referral_coupon_code']) && !empty($input['referral_coupon_code']) && $input['referral_coupon_code'] != "") {
            $userObj = new User();
            $response = $userObj->verifyAndGetReferredByUser($input['referral_coupon_code']);
            if ($response["status"] == false) {
                return $this->sendError('Invalid referral code.', []);
            }
            $input['referred_by_user_id'] = isset($response["referred_by_user_id"]) ? $response["referred_by_user_id"] : 0;
        }
        $input['email'] = $request->email_address;
        $input['password'] = bcrypt($input['password']);
        $input['password_plain'] = DataHelper::encrypt($input['password']);
        $input['referral_code'] = DataHelper::generateBarcodeString(8);
        if (!empty($request->email_address)) {
            $input['email_verify_key'] = DataHelper::emailVerifyKey();
        }

        $input['created_by'] = 1;
        $input['roles'] = [4];

        $user = User::create($input);
        $user->roles()->sync([4]);
        $tokenResult = $user->createToken(getenv('APP_NAME'));
        $success['token'] =  $tokenResult->accessToken;
        $success['expires_at'] =  $tokenResult->token->expires_at;

        // $success['token'] =  $user->createToken(getenv('APP_NAME'))->accessToken;
        $success['name'] =  $user->first_name . " " . $user->last_name;
        $success['referral_code'] = $user->referral_code;
        $success['id'] = $user->id;
        $success['role'] = $user->load('roles')->roles[0]->id;
        $success['role_name'] = $user->load('roles')->roles[0]->title;
        $emailVerifyUrl = config('services.miscellaneous.EMAIL_VERIFY_URL');
        if (!empty($request->email_address)) {
            EmailHelper::sendEmail(
                'IN_APP_EMAIL_VERIFICATION',
                [
                    'link' => $emailVerifyUrl . 'verify?key=' . $input['email_verify_key'],
                    'email_to' => $request->email_address, //$request->email_address
                    'customerName' => $user->first_name . " " . $user->last_name,
                    'isEmailVerified' => 1
                ],
                [
                    'attachment' => []
                ]
            );
        }

        // Check for referral registration campaign
        if (isset($input['referral_coupon_code']) && !empty($input['referral_coupon_code']) && $input['referral_coupon_code'] != "" && $input['referred_by_user_id'] > 0) {
            /* $inputs['user_id'] = $user->id;
            $inputs['referral_user_type'] = 2;
            $inputs['campaign_master_id'] = 8;
            $params['category_id'] = 0;
            $params['sub_category_id'] = 0;
            $params['ordered_date'] = date('Y-m-d');
            $promoCodes = new PromoCodes();

            // For referee
            $promoCodes->referralCampaign($inputs); 
            
            // For referrer
            $inputs['user_id'] = $input['referred_by_user_id'];
            $inputs['referral_user_type'] = 1;
            $inputs['campaign_master_id'] = 7;
            $promoCodes->referralCampaign($inputs); */
        }
        return $this->sendResponse($success, 'User register successfully.');
    }

    public function updateCustomer(Request $request)
    {
        if (!isset($request->role_id)) {
            return $this->sendError("Please provide valid role details.", []);
        }

        if ($request->role_id == 3) { // For delivery boys only
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email_address' => 'required',
                'id' => 'required',
                'role_id' => 'required',
                'platform' => 'required',
                'aadhar_number' => 'required',
                'pan_number' => 'required',
                'license_number' => 'required',
                'vehicle_type' => 'required',
                'vehicle_number' => 'required',
                'user_photo' => 'required',
                'aadhar_card_photo' => 'required',
                'pan_card_photo' => 'required',
                'license_card_photo' => 'required',
                'rc_book_photo' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'ifsc_code' => 'required',
                'gender' => 'required',
                'date_of_birth' => 'required',
                'marital_status' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'date_of_birth' => 'required',
                'marital_status' => 'required',
                'email_address' => 'required',
                'id' => 'required',
                'role_id' => 'required',
                'platform' => 'required'
            ]);
        }


        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        $input = $request->all();
        $customer = User::where('id', $request->id)->first();
        $input['email'] = $request->email_address;
        $input['date_of_birth'] = ($input['date_of_birth']) ? date("Y-m-d", strtotime(str_replace("'", "", $input['date_of_birth']))) : "";
        //  $input['password'] = bcrypt($input['password']);
        //$input['referral_code'] = DataHelper::generateBarcodeString(9);
        //$input['email_verify_key'] = DataHelper::emailVerifyKey();

        $isVerifyEmailRequired = 0;
        if (isset($request->email_address) && !empty($request->email_address)) {
            $baseCustomerEmail = $customer->checkEmailVerified($request->id);

            if ($baseCustomerEmail['email'] == $request->email_address) {
                $emailVerifyKey = $baseCustomerEmail['email_verify_key'];
                $input['email_verify_key'] = $emailVerifyKey;
                $input['email_verified'] = $baseCustomerEmail['email_verified'];
                if ($baseCustomerEmail['email_verified'] == 0) {
                    $isVerifyEmailRequired = 1;
                }
            } else {
                $dataHelper = new DataHelper();
                $emailVerifyKey = $dataHelper->emailVerifyKey();
                $input['email_verify_key'] = DataHelper::emailVerifyKey();
                $isVerifyEmailRequired = 1;
            }
        }


        if (!$customer) {
            return $this->sendError("Please try with valid details.", []);
        }

        $input['updated_by'] = 1;

        $customer->update($input);
        // print_r($input);
        if ($request->role_id == 3) {
            $input['status'] = 1;
            $userDetails =  UserDetails::updateOrCreate(
                ['user_id' => $request->id, 'role_id' => $request->role_id],
                $input
            );
        }

        //$customer = User::create($input);
        // $success['token'] =  $customer->createToken(getenv('APP_NAME'))->accessToken;
        $success['name'] =  $customer->first_name . " " . $customer->last_name;
        $success['id'] = $customer->id;
        $success['role'] = $customer->load('roles')->roles[0]->id;
        $success['role_name'] = $customer->load('roles')->roles[0]->title;
        $message = 'User updated successfully.';
        switch ($request->role_id) {
            case 3:
                $message = 'Delivery boy data updated successfully.';
                break;
            case 4:
                $message = 'Customer data updated successfully.';
                break;
            default:
                $message = 'User updated successfully.';
                break;
        }

        if ($isVerifyEmailRequired) {
            $emailVerifyUrl = config('services.miscellaneous.EMAIL_VERIFY_URL');
            EmailHelper::sendEmail(
                'IN_APP_EMAIL_CHANGE_VERIFICATION',
                [
                    'link' => $emailVerifyUrl . 'verify?key=' . $emailVerifyKey,
                    'email_to' => $request->email_address, //$request->email_address
                    'customerName' => $customer->first_name . " " . $customer->last_name,
                    'isEmailVerified' => 1
                ],
                [
                    'attachment' => []
                ]
            );
        }

        return $this->sendResponse($success, $message);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // if (Auth::guard('api')->attempt(['mobile_number' => $request->mobile_number, 'password' => $request->password])) {
        if (Auth::attempt(['mobile_number' => $request->mobile_number, 'password' => $request->password])) {
            // $credentials = request(['email', 'password']);
            // if(!Auth::attempt($credentials))
            //     return response()->json([
            //         'message' => 'Unauthorized'
            //     ], 401);

            $user = Auth::user();
            $tokenResult = $user->createToken(getenv('APP_NAME'));
            $success['token'] =  $tokenResult->accessToken;
            $success['expires_at'] =  $tokenResult->token->expires_at;

            $success['name'] =  $user->first_name . " " . $user->last_name;
            $success['dob'] =  $user->date_of_birth;
            $success['marital_status'] =  $user->marital_status;
            $success['gender'] =  $user->gender;
            $success['email'] =  $user->email;
            $success['mobile_number'] =  $user->mobile_number;

            $success['referral_code'] = $user->referral_code;
            $success['id'] = $user->id;
            $success['role'] = (!empty($user->load('roles')->roles->toArray())) ? $user->load('roles')->roles[0]->id : 0;
            $success['role_name'] = (!empty($user->load('roles')->roles->toArray())) ? $user->load('roles')->roles[0]->title : "";
            $loginLogs = User::saveUserLoginLogs(array('user_id' => $user->id, 'is_login' => 1, 'platform' => ($request->platform ? $request->platform : 1)));
            $userDetails = $user->details;
            unset($userDetails->id);
            unset($userDetails->user_id);
            unset($userDetails->role_id);
            $success['details'] = ($user->details) ? $user->details : (object)[];
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
    public function getUserDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return $this->sendError('User details are not available.', []);
        }
        $success['name'] =  $user->first_name . " " . $user->last_name;
        $success['dob'] =  $user->date_of_birth;
        $success['marital_status'] =  $user->marital_status;
        $success['gender'] =  $user->gender;
        $success['email'] =  $user->email;
        $success['mobile_number'] =  $user->mobile_number;

        $success['referral_code'] = $user->referral_code;
        $success['id'] = $user->id;
        $success['role'] = (!empty($user->load('roles')->roles->toArray())) ? $user->load('roles')->roles[0]->id : 0;
        $success['role_name'] = (!empty($user->load('roles')->roles->toArray())) ? $user->load('roles')->roles[0]->title : "";

        $userDetails = $user->details;
        unset($userDetails->id);
        unset($userDetails->user_id);
        unset($userDetails->role_id);
        $success['details'] = ($user->details) ? $user->details : (object)[];
        if ($user) {
            return $this->sendResponse($success, 'User details fetched successfully.');
        } else {
            return $this->sendError('User details are not available.', []);
        }
    }
    public function getPinCodeList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            // 'pin_code' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'pin_code' => $request->pin_code,

            ];
            //$params = json_encode($params);

            //Create product object to call functions
            $customer = new User();
            // Function call to get product list
            $responseDetails = $customer->getPinCodeDetails($params);
            $message = 'Pincode list.';
            if (sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    public function storeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'user_role_id' => 'required|integer',
            'device_id' => 'required',
            'device_token' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'device_type' => $request->platform,
                'user_id' => $request->user_id,
                'user_role_id' => $request->user_role_id,
                'device_id' => $request->device_id,
                'device_token' => $request->device_token,
            ];

            //Create user object to call functions
            $user = new User();
            // Function call to store device token
            $responseDetails = $user->storeDeviceToken($params);
            $message = 'Failed to store device token.';
            if ($responseDetails) {
                $message = 'Device token stored successfully';
            }
            $response = $this->sendResponse([], $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $token = $request->user()->token();
            $loginLogs = User::saveUserLoginLogs(array('user_id' => $request->user()->id, 'is_login' => 2, 'platform' => $request->platform));
            $token->revoke();
            $response = $this->sendResponse("", "You have been successfully logged out!");
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->new_password != $request->confirm_password) {
            return $this->sendError('New and confirm password are not matching.', []);
        }

        if ($request->new_password == $request->old_password) {
            return $this->sendError('Old and new password should be different.', []);
        }

        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return $this->sendError("Please try with valid user.", []);
        }
        if (Hash::check($request->old_password, $user->password) && !(Hash::check($request->new_password, $user->password))) {
            $input['password'] = bcrypt($request->new_password);
            $input['password_plain'] = DataHelper::encrypt($request->new_password);
            $input['updated_by'] = 1;
            $user->update($input);

            // Revoke token
            $token = $request->user()->token();
            $token->revoke();
            return $this->sendResponse("", "Password changed successfully.");
        } else {
            return $this->sendError("Please try with valid old password.", []);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'mobile_number' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->new_password != $request->confirm_password) {
            return $this->sendError('Unauthorised.', ['error' => 'New and confirm password are not matching']);
        }

        $user = User::where('mobile_number', $request->mobile_number)->first();
        if (!$user) {
            return $this->sendError("Please try with valid mobile number.", []);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return $this->sendError("Please try with another password.", []);
        }
        $input['password'] = bcrypt($request->new_password);
        $input['password_plain'] = DataHelper::encrypt($request->new_password);
        $input['updated_by'] = 1;
        $user->update($input);
        return $this->sendResponse("", 'Password changed successfully.');
    }
}
