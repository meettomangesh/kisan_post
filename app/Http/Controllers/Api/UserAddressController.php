<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Models\UserAddress;
use App\Models\CustomerOrders;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helper\DataHelper;
use Exception;
use DB;

class UserAddressController extends BaseController
{
    public function getAllAddressByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            //  'no_of_records' => 'required',
            //  'page_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $userAddress = new UserAddress();
            $user = User::where('id', '=', $request->user_id)->first();
            if ($user === null) {
                return $this->sendError('Please provide valid customer details.', []);
            }

            $responseDetails = DB::table('user_address')
            ->leftJoin('states', 'user_address.state_id', '=', 'states.id')
            ->leftJoin('cities', 'user_address.city_id', '=', 'cities.id')
            ->select('user_address.*', 'states.name AS state_name', 'cities.name AS city_name')
            ->where('user_address.user_id', $request->user_id)->orderByDesc('id')->get();

             $message = 'Address list.';
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

    public function saveAddressByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            // 'landmark',
            'pin_code' => 'required',
            'area' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'mobile_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {

            $user = User::where('id', '=', $request->user_id)->first();
            if ($user === null) {
                return $this->sendError('Please provide valid customer details.', []);
            }
            // Function call to get product list
            $userAddress = UserAddress::create($request->all());
            $success['user_address_id'] = $userAddress->id;

            return $this->sendResponse($success, 'User address added successfully.');
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    public function updateAddressByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'user_address_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            // 'landmark',
            'pin_code' => 'required',
            'area' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'mobile_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            
            $user = User::where('id', '=', $request->user_id)->first();
            if ($user === null) {
                return $this->sendError('Please provide valid customer details.', []);
            }
            $userAddress =  UserAddress::where('id', $request->user_address_id)->first();
            if ($userAddress === null) {
                return $this->sendError('Please provide valid address details.', []);
            }
            $customerOrders = CustomerOrders::where('shipping_address_id', $request->user_address_id)->whereIn('order_status', [0,1,2,3])->get()->toArray();
            if (sizeof($customerOrders) > 0) {
                return $this->sendError('You have some order(s) which are in transition, so we can not update your address.', []);
            }
            $userAddress->name = $request->name;
            $userAddress->address = $request->address;
            $userAddress->pin_code = $request->pin_code;
            $userAddress->area = $request->area;
            $userAddress->landmark = $request->landmark;
            $userAddress->city_id = $request->city_id;
            $userAddress->state_id = $request->state_id;
            $userAddress->mobile_number = $request->mobile_number;
            $userAddress->update();
            $success['user_address_id'] = $userAddress->id;

            return $this->sendResponse($success, 'User address updated successfully.');
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    public function deleteAddressByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            'user_address_id' => 'required',
            //  'no_of_records' => 'required',
            //  'page_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            
            $user = User::where('id', '=', $request->user_id)->first();
            if ($user === null) {
                return $this->sendError('Please provide valid customer details.', []);
            }
            $userAddress =  UserAddress::where('id', $request->user_address_id)->first();
            if ($userAddress === null) {
                return $this->sendError('Please provide valid address details.', []);
            }
            $customerOrders = CustomerOrders::where('shipping_address_id', $request->user_address_id)->whereIn('order_status', [0,1,2,3])->get()->toArray();
            if (sizeof($customerOrders) > 0) {
                return $this->sendError('You have some order(s) which are in transition, so we can not delete your address.', []);
            }
            $userAddress->delete();
            $success['user_address_id'] = $request->user_address_id;

            return $this->sendResponse($success, 'User address deleted successfully.');
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }
}
