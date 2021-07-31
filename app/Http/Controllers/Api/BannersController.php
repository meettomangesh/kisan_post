<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Exception;
use Validator;
use Carbon\Carbon;

class BannersController extends BaseController
{

    public function getBannerList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            // 'type' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $type = $request->type;
            //Create banner object to call functions
            $banner = new Banner();
            // Function call to get banner list
            $responseDetails['banners'] = $banner->getBannerList(1);
            $responseDetails['sliders'] = $banner->getBannerList(2);
            $response = $this->sendResponse($responseDetails, 'Banner list.');
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }
}
