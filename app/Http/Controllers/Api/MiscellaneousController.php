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
use App\Helper\DataHelper;

class MiscellaneousController extends BaseController
{
    function uploadImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'platform' => 'required',
                'role_id' => 'required',
                'image' => 'required|mimes:jpeg,jpg,pdf,png,bmp|max:2048',
            ]);
            if ($validator->fails()) {
                return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
            }
            $image_path = '';
            if ($files = $request->file('image')) {
                $image_path = DataHelper::uploadImage($request->file('image'), '/images/documents/', $request->user_id);
            }
            $responseDetails = array("user_id" => $request->user_id, 'role_id' => $request->role_id, 'image_path' => $image_path);
            $response = $this->sendResponse(
                $responseDetails,
                "Image uploaded successfully."
            );
        } catch (Exception $e) {
            $responseDetails = array("user_id" => isset($requestedParams["user_id"]) ? $requestedParams["user_id"] : '');
            $response = $this->sendResponse(
                $responseDetails,
                $e->getMessage()
            );
        }

        return $response;
    }
}
