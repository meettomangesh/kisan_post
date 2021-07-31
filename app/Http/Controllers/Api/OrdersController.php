<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerOrders;
use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrdersController extends BaseController
{

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            // 'user_id' => 'exists:users.id',
            'user_id' => 'required|integer',
            'delivery_details' => 'required',
            'delivery_details.date' => 'required',
            'delivery_details.address' => 'required',
            'delivery_details.address.id' => 'required',
            'payment_details' => 'required',
            'payment_details.type' => 'in:cod,online|required',
            'payment_details.net_amount' => 'required|integer|gt:0',
            'payment_details.gross_amount' => 'required|integer|gt:0',
            'payment_details.discounted_amount' => 'required',
            'payment_details.delivery_charge' => 'required',
            // 'payment_details.promo_code'=>'required_if:payment_details.discounted_amount,gt:0',
            // 'payment_details.order_id'=>'required_if:payment_details.type,online',
            // 'payment_details.transaction_id'=>'required_if:payment_details.type,online',
            // 'payment_details.method'=>'required_if:payment_details.type,online',
            // 'payment_details.bank'=>'required_if:payment_details.type,online',
            'products' => 'required',
            'products.*.is_basket' => 'in:1,0|required|integer',
            'products.*.id' => 'required|integer|gt:0',
            'products.*.product_unit_id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|gt:0',
            'products.*.selling_price' => 'required|gt:0',
            'products.*.special_price' => 'required',
            'products.*.special_price_start_date' => 'required_if:products.special_price,gt:0',
            'products.*.special_price_end_date' => 'required_if:products.special_price,gt:0',
            // 'products.*.expiry_date' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = $request->all();
            //Create customer order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to get place order
            $responseDetails = $customerOrders->placeOrder($params);
            
            if ($responseDetails["status"] == true) {
                $message = 'Order placed successully!.';
            } else {
                return $this->sendError('Failed to place order.', $responseDetails, 422);
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    public function getOrderList(Request $request)
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
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to get order list
            $responseDetails = $customerOrders->getOrderList($params);
            $message = 'Order list.';
            if (sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'order_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'order_id' => $request->order_id
            ];
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to cancel order
            $responseDetails = $customerOrders->cancelOrderAPI($params);
            $message = 'Failed to order cancel.';
            if ($responseDetails) {
                $message = 'Order cancelled successfully';
            }
            $response = $this->sendResponse([], $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function getOrderListForDeliveryBoy(Request $request)
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
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to get order list
            $responseDetails = $customerOrders->getOrderListForDeliveryBoy($params);
            $message = 'Order list.';
            if (sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function changeOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'order_id' => 'required|integer',
            'order_status' => 'in:2,3,4,5|required',
            'order_note' => 'required_if:order_status,==,5|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'order_id' => $request->order_id,
                'order_status' => $request->order_status,
                'order_note' => $request->order_note,
            ];
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to cancel order
            $responseDetails = $customerOrders->changeOrderStatus($params);
            $message = 'Failed to change order status.';
            if ($responseDetails) {
                $message = 'Order status changed successfully';
            }
            $response = $this->sendResponse([], $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function getOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'order_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'order_id' => $request->order_id
            ];
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to get order status
            $responseDetails = $customerOrders->getOrderStatus($params);
            $response = $this->sendResponse($responseDetails, "Order status");
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function paymentCallbackUrl(Request $request)
    {
        try {
            $input=$request->all();
           // print_r($input['payload']['payment']['entity']['order_id']); 

            //Log::info('inside controller paymentCallbackUrl.', $input);
            $razorpay_order_id = $input['payload']['payment']['entity']['order_id'];
            $razorpay_payment_id = $input['payload']['payment']['entity']['id'];
            $razorpay_signature = $input['payload']['payment']['entity']['status'];
            // echo $razorpay_order_id.'-'.$razorpay_payment_id.'-'.$razorpay_signature; exit;
            //Log::info('inside controller paymentCallbackUrl.', ['razorpay_order_id'=>$razorpay_order_id,'razorpay_payment_id'=>$razorpay_payment_id,'razorpay_signature'=>$razorpay_signature]);
            if (!isset($razorpay_payment_id) || !isset($razorpay_order_id) || !isset($razorpay_signature)) {
                return false;
            }
            $params = [
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_signature' => $razorpay_signature
            ];
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to update order status using payment response
            $responseDetails = $customerOrders->paymentCallbackUrl($params);
            
            $message = 'Success.';
            if ($responseDetails == false) {
                $message = 'Failure.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function checkDeliveryBoyAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'address_id' => 'required|integer',
            'delivery_date' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'address_id' => $request->address_id,
                'delivery_date' => $request->delivery_date,
            ];
            //Create order object to call functions
            $customerOrders = new CustomerOrders();
            // Function call to check delivery boy availability by delivery date
            $responseDetails = $customerOrders->checkDeliveryBoyAvailability($params);
            if ($responseDetails["status"] == true) {
                return $this->sendResponse($responseDetails, $responseDetails["message"]);
            } else {
                return $this->sendError($responseDetails["message"], $responseDetails, 422);
            }
        } catch (Exception $e) {
            return $this->sendResponse(array(), $e->getMessage());
        }
    }
}
