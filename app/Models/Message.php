<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use PDO;
use Exception;

class Message extends ApiModel
{
    /**
     * Send Message to user mobile
     * return success response or error response in json
     */
    public function sendMessage($to, $textMessage, $merchantId, $channelType, $params = [])
    {
        try {
            // $smsGateway = [];
            // $smsGateway = $this->getMerchantSmsCredentials($merchantId, $channelType);
            // $smsGateWayUrl = getenv('SMS_GATEWAY_API_BASE_URL');
            // $username = getenv('SMS_GATEWAY_USERNAME');
            // $password = getenv('SMS_GATEWAY_PASSWORD');
            // $smsGateWayUrl = $smsGateWayUrl . '?feedid=' . $smsGateway['feed_id'];
            // $smsGateWayUrl = $smsGateWayUrl . '&username=' . $username;
            // $smsGateWayUrl = $smsGateWayUrl . '&password=' . $password;
            // $smsGateWayUrl = $smsGateWayUrl . '&to=' . $to;
            // $smsGateWayUrl = $smsGateWayUrl . '&text=' . str_replace(' ', '+', $textMessage);
            // $smsGateWayUrl = $smsGateWayUrl . '&time=';
            // $smsGateWayUrl = $smsGateWayUrl . '&senderid=' . $smsGateway['sender_id'];
            // $objURL = curl_init($smsGateWayUrl);
            // curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1);
            // $retVal = trim(curl_exec($objURL));
            // curl_close($objURL);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Send OTP to user mobile
     * return success response or error response in json
     */
    public function sendOtp($to, $textMessage, $params = [])
    {
        try {


            $toMob = '91' . $to;
            $otp = $params['otp'];
            $SMS_VALIDITY_TIME_MINUTES = $params['SMS_VALIDITY_TIME_MINUTES'];

            $smsGateWayUrl = config('services.miscellaneous.SMS_GATEWAY_API_BASE_URL');
            //$smsGateWayUrl .= 'sendotp.php';

            $authkey = config('services.miscellaneous.SMS_GATEWAY_API_AUTH_KEY');
            $sender = config('services.miscellaneous.SMS_GATEWAY_API_SENDER');
            $templateId = '5fb9ffe4f4b56659f70ec1b7';

            $smsGateWayUrl = $smsGateWayUrl . '?authkey=' . $authkey;
            $smsGateWayUrl = $smsGateWayUrl . '&template_id=' . $templateId;
            $smsGateWayUrl = $smsGateWayUrl . '&mobile=' . $toMob;
            $smsGateWayUrl = $smsGateWayUrl . '&otp=' . $otp;
            $smsGateWayUrl = $smsGateWayUrl . '&otp_expiry=' . $SMS_VALIDITY_TIME_MINUTES;
            $smsGateWayUrl = $smsGateWayUrl . '&message=' . urlencode($textMessage);


            $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $smsGateWayUrl,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "GET",
            // ));
            curl_setopt_array($curl, array(
                CURLOPT_URL => $smsGateWayUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{\"Value1\":\"Param1\",\"Value2\":\"Param2\",\"Value3\":\"Param3\"}",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));

            $response = json_decode(curl_exec($curl));
            $err = curl_error($curl);
            curl_close($curl);
            if ($err || $response->type == 'error') {
                return 0;
            } else {
                return 1;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * Send OTP to user mobile
     * return success response or error response in json
     */
    public function verifyOtp($to, $otp)
    {
        try {
            $toMob = '91' . $to;
            $smsGateWayUrl = config('services.miscellaneous.SMS_GATEWAY_API_BASE_URL');
            //$smsGateWayUrl .= 'verifyRequestOTP.php';
            $smsGateWayUrl .= '/verify';
            $authkey = config('services.miscellaneous.SMS_GATEWAY_API_AUTH_KEY');

            $smsGateWayUrl = $smsGateWayUrl . '?authkey=' . $authkey;
            $smsGateWayUrl = $smsGateWayUrl . '&mobile=' . $toMob;
            $smsGateWayUrl = $smsGateWayUrl . '&otp=' . $otp;


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $smsGateWayUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));


            $response = json_decode(curl_exec($curl));
            $err = curl_error($curl);
            curl_close($curl);

            if ($err || $response->type == 'error') {
                return 0;
            } else {
                return 1;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
