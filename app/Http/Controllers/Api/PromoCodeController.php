<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoCodes;
use Exception;
use Validator;
use Carbon\Carbon;

class PromoCodeController extends BaseController
{

    public function getPromoCodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'no_of_records' => 'required',
            'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'no_of_records' => $request->no_of_records,
                'page_number' => $request->page_number
            ];
            $params = json_encode($params);
            //Create promo code object to call functions
            $promoCodes = new PromoCodes();
            // Function call to get promo codes
            $responseDetails = $promoCodes->getPromoCodes($params);
            $message = 'Promo Codes.';
            if(sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function validatePromoCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'promo_code' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'promo_code' => $request->promo_code
            ];
            //Create promo code object to call functions
            $promoCodes = new PromoCodes();
            // Function call to validate promo code
            $responseDetails = $promoCodes->validatePromoCode($params);
            $message = 'Promo code is not valid.';
            if($responseDetails) {
                $message = 'Promo code is valid.';
            }
            $response = $this->sendResponse([], $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }
}
