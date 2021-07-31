<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerOtp;
use App\Models\SmsTemplate;
use App\Models\Message;
use Exception;
use Validator;
use Carbon\Carbon;

class SmsController extends BaseController
{

    public function getOtp(Request $request)
    {
        //Create customerOtp object to call functions
        $customerOtp = new CustomerOtp();
        // Function call to generate OTP
        $otpNumber = $customerOtp->generateOtp();
        //Initialize mobile number
        $mobileNumber = 0;
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'platform' => 'required',
            'transactionType' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $smsTemplatesValue = parent::SMS_MSG_TEMPLATES;
            $transActionType = $request->transactionType;
            $smsTemplateName = "";
            foreach ($smsTemplatesValue as $key => $value) {
                if ($transActionType == $key) {
                    $smsTemplateName = $value;
                    break;
                }
            }

            $smsValidityTime = config('services.miscellaneous.SMS_VALIDITY_TIME_MINUTES');
            $smsgTemplates = new SmsTemplate($this->pdo, $this->redis);
            $requestedParams["template_name"] = $smsTemplateName;

            $smsgTemplatesData = $smsgTemplates->getSmsTemplates($requestedParams);

            $textMessage = "";
            $merchantID = "";
            if ($smsgTemplatesData) {
                $textMessage = str_replace('$OTP', $otpNumber, $smsgTemplatesData);
                $textMessage = str_replace('Kisan Farm Fresh', getenv("APP_NAME"), $textMessage);
                $textMessage = str_replace('$SMS_VALIDITY_TIME_MINUTES', $smsValidityTime, $textMessage);
                $textMessage = '<#> ' . $textMessage;
                //LP_REGISTRATION_OTP
            }

            $requestedParams['from_no'] = config('services.miscellaneous.from_no');
            $requestedParams['SMS_VALIDITY_TIME_MINUTES'] = $smsValidityTime;
            $requestedParams['otp'] = $otpNumber;
            $message = new Message($this->pdo, $this->redis);
            //Call function to send message

            $sendMessage = $message->sendOtp($request->mobile_number, $textMessage, $requestedParams);


            if ($sendMessage) {

                $params = $request->all();
                $params["mobile_number"] = $request->mobile_number;
                $params["otp"] = $otpNumber;
                $params["sms_delivered"] = 1;
                $params["error_message"] = "";
                $params["otp_used"] = 0;
                $params["platform_generated_on"] = $request->platform;
                $params["otp_generated_for"] = $request->transactionType;
                $params["created_at"] =  now();
                $responseDetails = CustomerOtp::create($params);
                $responseDetails = array("id" => $responseDetails->id, "Otp" => $responseDetails->otp);
                // $lastInsertId = $customerOtp->save($params);
                $response = $this->sendResponse($responseDetails, 'OTP sent successfully.');
            }
        } catch (Exception $e) {
            if ($request->mobile_number != 0) {
                $params = $request->all();
                $params["mobile_number"] = $request->mobile_number;
                $params["otp"] = $otpNumber;
                $params["sms_delivered"] = 0;
                $params["error_message"] = $e->getMessage();
                $params["otp_used"] = 0;
                $params["platform_generated_on"] = $request->platform;
                $params["otp_generated_for"] = $request->transactionType;
                $params["created_at"] = now();
                $responseDetails = CustomerOtp::create($params);
                $responseDetails = array("id" => $responseDetails->id, "Otp" => $responseDetails->otp);
            }


            $response = $this->sendResponse($responseDetails, $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    function verifyOtp(Request $request)
    {
        try {
            $ismobilePresent = 0;
            $mobileNumber = 0;

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'otp' => 'required',
                'transactionType' => 'required',
                'platform' => 'required',
                'mobile_number' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
            }

            if (isset($request->mobile_number)) {
                $ismobilePresent = 1;
                $mobileNumber = $request->mobile_number;
            }
            $smsValidityTime = getenv('SMS_VALIDITY_TIME_MINUTES');

            //$customerOtp = new CustomerOtp();
            //$result = $customerOtp->validateOtp($request->otp, $mobileNumber, $request->id, $request->platform, $ismobilePresent, $smsValidityTime);
            $message = new Message($this->pdo, $this->redis);
            $result = $message->verifyOtp($request->mobile_number, $request->otp);

            $responseDetails = array("id" => $request->id);
            $response = $this->sendResponse(
                $responseDetails,
                ($result) ? 'OTP verified successfully.' : "OTP is invalid, expired or used."
            );
        } catch (Exception $e) {
            $responseDetails = array("id" => isset($requestedParams["id"]) ? $requestedParams["id"] : '');
            $response = $this->sendResponse(
                $responseDetails,
                $e->getMessage()
            );
        }

        return $response;
    }
}
