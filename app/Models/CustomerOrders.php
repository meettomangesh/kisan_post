<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\User;
use App\Models\CustomerOrderDetails;
use App\Models\CustomerOrderStatusTrack;
use App\Models\ProductLocationInventory;
use App\Models\UserAddress;
use App\Models\PromoCodes;
use App\Models\CustomerDeviceTokens;
use App\Models\ConfigSettings;
use DB;
use PDO;
use App\Helper\PdfHelper;
use App\Helper\DataHelper;
use App\Helper\EmailHelper;
use App\Helper\NotificationHelper;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class CustomerOrders extends Model
{
    use SoftDeletes, Notifiable;

    public $table = 'customer_orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['customer_id', 'delivery_boy_id', 'shipping_address_id', 'billing_address_id', 'delivery_date', 'net_amount', 'gross_amount', 'discounted_amount', 'delivery_charge', 'payment_type', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'total_items', 'total_items_quantity', 'reject_cancel_reason', 'purchased_from', 'is_coupon_applied', 'promo_code', 'is_basket_in_order', 'order_status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function userCustomer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function userDeliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }

    public function customerShippingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    public function customerBillingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(CustomerOrderDetails::class, 'order_id');
    }

    protected function cancelOrder($orderId, $type = "", $reason = "")
    {
        $cancelData = array('order_id' => $orderId, 'type' => $type, 'reason' => $reason);
        $inputData = json_encode($cancelData);
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare("CALL cancelOrder(?)");
        $stmt->execute([$inputData]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt->closeCursor();
        $reponse = json_decode($result['response']);
        if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
            return false;
        }
        $this->sendOrderTransactionNotification($cancelData);
        return true;
        /* $statusCancelled = 5;
        $statusIds = explode(',', '1');        
        $codData = CustomerOrderDetails::select('id','product_units_id','item_quantity','is_basket')->where('order_id', $orderId)->whereIn('order_status', $statusIds)->get()->toArray();
        if(sizeof($codData) > 0) {
            foreach($codData as $key => $value) {
                if($type == 1) {
                    CustomerOrderDetails::where('id', $value['id'])->forceDelete();
                    CustomerOrderStatusTrack::where('order_details_id', $value['id'])->forceDelete();
                } else {
                    $cod = CustomerOrderDetails::find($value['id']);
                    $cod->order_status = $statusCancelled;
                    $cod->save();
                    CustomerOrderStatusTrack::create(array(
                        'order_details_id' => $value['id'],
                        'order_status' => $statusCancelled,
                        'created_by' => 1
                    ));
                }
                $qty = ProductLocationInventory::select('id','current_quantity')->where('product_units_id', $value['product_units_id'])->get()->toArray();
                $inventory = ProductLocationInventory::find($qty[0]['id']);
                $inventory->current_quantity = $qty[0]['current_quantity'] + $value['item_quantity'];
                $inventory->save();
            }
            if($type == 1) {
                $co = CustomerOrders::find($orderId);
                $co->forceDelete();
            } else {
                $co = CustomerOrders::find($orderId);
                $co->order_status = $statusCancelled;
                $co->save();
            }
            return true;
        }
        return false; */
    }

    public function cancelOrderAPI($params)
    {
        return $this->cancelOrder($params['order_id'], 2, '');
    }

    public function placeOrder($params)
    {
        // validate customer
        $usersData = User::select('id')->where('id', $params['user_id'])->where('status', 1)->get()->toArray();
        $userAddressData = UserAddress::select('id')->where('id', $params['delivery_details']['address']['id'])->where('user_id', $params['user_id'])->where('status', 1)->get()->toArray();
        $minOrderAmountRes = ConfigSettings::where('name', 'minOrderAmount')->first();
        $deliveryChargeRes = ConfigSettings::where('name', 'deliveryCharge')->first();

        if (sizeof($usersData) == 0) {
            return array("status" => false, "message" => "Invalid user", "order_id" => 0, "razorpay_order_id" => 0);
        }
        if (sizeof($params['products']) == 0) {
            return array("status" => false, "message" => "Product(s) not present", "order_id" => 0, "razorpay_order_id" => 0);
        }
        if (sizeof($userAddressData) == 0) {
            return array("status" => false, "message" => "Invalid user address", "order_id" => 0, "razorpay_order_id" => 0);
        }
        // validate delivery date
        if ($params['delivery_details']['date'] <= date('Y-m-d')) {
            return array("status" => false, "message" => "Invalid delivery date, must be greater than current date", "order_id" => 0, "razorpay_order_id" => 0);
        }

        if(($params['payment_details']['net_amount'] < $minOrderAmountRes->value && $params['payment_details']['delivery_charge'] == 0) || ($params['payment_details']['delivery_charge'] > 0 && $params['payment_details']['delivery_charge'] != $deliveryChargeRes->value)) {
            return array("status" => false, "message" => "Delivery charge is not applied or incorrect.", "order_id" => 0, "razorpay_order_id" => 0);
        }

        if (isset($params['payment_details']['promo_code']) && !empty($params['payment_details']['promo_code']) && $params['payment_details']['promo_code'] != '') {
            $vpcData = array("promo_code" => $params['payment_details']['promo_code'], "user_id" => $params['user_id']);
            $vpcData = json_encode($vpcData);
            $promoCodes = new PromoCodes();
            $vpcResponse = $promoCodes->validatePromoCode($vpcData);
            if (!$vpcResponse) {
                return array("status" => false, "message" => "Promo code is incorrect", "order_id" => 0, "razorpay_order_id" => 0);
            }
        }

        $orderAmount = $totalItemQty = $isBasketInOrder = 0;
        // validate product
        foreach ($params['products'] as $key => $value) {
            $orderAmount = $orderAmount + ((($value['special_price'] > 0) ? $value['special_price'] : $value['selling_price']) * $value['quantity']);
            $totalItemQty = $totalItemQty + $value['quantity'];
            $inputData = json_encode($value);

            // $result = DB::select('call validateProduct(?)', [$inputData]);
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL validateProduct(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);
            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                return array("status" => false, "message" => "Product validation is failed", "order_id" => 0, "razorpay_order_id" => 0);
            }
            if ($value['is_basket'] == 1) {
                $isBasketInOrder = 1;
            }
        }

        if (!isset($params['payment_details']['promo_code']) && empty($params['payment_details']['promo_code']) && $params['payment_details']['delivery_charge'] == 0 && $orderAmount != $params['payment_details']['net_amount']) {
            return array("status" => false, "message" => "Amount is not matching", "order_id" => 0, "razorpay_order_id" => 0);
        }

        $customerOrdersResponse = CustomerOrders::create(array(
            'customer_id' => $params['user_id'],
            'net_amount' => $params['payment_details']['net_amount'],
            'gross_amount' => $params['payment_details']['gross_amount'],
            'discounted_amount' => $params['payment_details']['discounted_amount'],
            'delivery_charge' => $params['payment_details']['delivery_charge'],
            'payment_type' => $params['payment_details']['type'],
            'total_items' => sizeof($params['products']),
            'total_items_quantity' => $totalItemQty,
            'shipping_address_id' => $params['delivery_details']['address']['id'],
            'billing_address_id' => $params['delivery_details']['address']['id'],
            'delivery_date' => $params['delivery_details']['date'],
            'is_basket_in_order' => $isBasketInOrder,
            'order_status' => ($params['payment_details']['type'] == 'cod') ? 1 : 0,
            'is_coupon_applied' => !empty($params['payment_details']['promo_code']) ? 1 : 0,
            'promo_code' => $params['payment_details']['promo_code'],
            'created_by' => 1
        ));
        $orderId = $customerOrdersResponse->id;

        foreach ($params['products'] as $key => $value) {
            $value['order_id'] = $orderId;
            $value['customer_id'] = $params['user_id'];
            $value['order_status'] = ($params['payment_details']['type'] == 'cod') ? 1 : 0;
            $inputData = json_encode($value);
            /* $result = DB::select('call placeOrderDetails(?)', [$inputData]);
            $reponse = json_decode($result[0]->response); */
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL placeOrderDetails(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);

            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                $this->cancelOrder($orderId, 1);
                return array("status" => false, "message" => "Failed to create order detail", "order_id" => 0, "razorpay_order_id" => 0);
            }
        }


        // Assign delivery boy
        $assignData['order_id'] = $orderId;
        $assignData['delivery_date'] = $params['delivery_details']['date'];
        $inputData = json_encode($assignData);
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare("CALL assignDeliveryBoyToOrder(?)");
        $stmt->execute([$inputData]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt->closeCursor();
        // return true;
        $razorPayOrderId = 0;
        if ($params['payment_details']['type'] == 'online') {
            $createOrderIdParams = array("order_amount" => $orderAmount, "order_id" => $orderId);
            $razorPayOrderId = $this->createOrderAtRazorpay($createOrderIdParams);
            if (isset($razorPayOrderId) && !empty($razorPayOrderId)) {
                $updateOrder = CustomerOrders::find($orderId);
                $updateOrder->razorpay_order_id = $razorPayOrderId;
                $updateOrder->save();
            }
        }

        if (isset($params['payment_details']['promo_code']) && !empty($params['payment_details']['promo_code']) && $params['payment_details']['promo_code'] != '') {
            $vpcData = array("promo_code" => $params['payment_details']['promo_code'], "user_id" => $params['user_id']);
            $promoCodes = new PromoCodes();
            $promoCodes->markPromoCodeAsUtilized($vpcData);
        }
        $params['order_id'] = $orderId;
        $params['razorpay_order_id'] = $razorPayOrderId;
        $params['invoice_template'] = 'IN_APP_INVOICE_AFTER_ORDER';

        $invoiceGeneratedPath =  $this->generateInvoice($params);

        $deliveryBoyInvoice = $this->generateInvoice(
            array(
                'invoice_template' => 'IN_APP_DELIVERY_BOY_INVOICE_AFTER_ORDER',
                'order_id' => $orderId,
                'file_name' => $orderId . '_delivery'
            )
        );
        if ($invoiceGeneratedPath) {
            $orderObj = CustomerOrders::find($orderId);
            $orderObj->customer_invoice_url = $invoiceGeneratedPath;
            $orderObj->delivery_boy_invoice_url = $deliveryBoyInvoice;
            $orderObj->update();
        }
        $params['order_email_template'] = 'IN_APP_ORDER_PLACED_NOTIFICATION';
        if ($params['payment_details']['type'] != 'online') {
            $emailResult = $this->sendOrderTransactionEmail($params);
            $notificationResult = $this->sendOrderTransactionNotification($params);
        }
        $invoiceGenerated = 0;
        return array("status" => true, "order_id" => $orderId, "razorpay_order_id" => $razorPayOrderId, "invoice_generated " => $invoiceGenerated);
    }

    public function sendOrderTransactionNotification($params)
    {
        $notifyHelper = new NotificationHelper();
        $orderDetails = CustomerOrders::find($params['order_id']);

        $templateName = NotificationHelper::getNotitificationTemplateName($orderDetails->order_status, 0);

        $notificationContent = NotificationHelper::getPushNotificationTemplate($templateName, '', [
            'name' => $orderDetails->userCustomer->first_name
        ]);
        $orderId = $params['order_id'];
        $notifyHelper->setParameters(["user_id" => $orderDetails->customer_id, "deep_link" => $notificationContent['deeplink'], "details" => json_encode(array('orderNo' => $orderId, 'userId' => $orderDetails->customer_id))], $notificationContent['title'], $notificationContent['message']);

        $orderDetails->notify($notifyHelper);
        if ($orderDetails->delivery_boy_id != 0) {
            $templateName = NotificationHelper::getNotitificationTemplateName($orderDetails->order_status, 1);

            $notificationContent = NotificationHelper::getPushNotificationTemplate($templateName, '', [
                'name' => $orderDetails->userDeliveryBoy->first_name
            ]);
            $type = '';
            if ($orderDetails->order_status == 1) {
                $type = 'Assigned';
            } elseif ($orderDetails->order_status == 5) {
                $type = 'Rejected';
            }
            $notifyHelper->setParameters(["user_id" => $orderDetails->delivery_boy_id, "deep_link" => $notificationContent['deeplink'], "details" => json_encode(array('orderNo' => $orderId, 'userId' => $orderDetails->userDeliveryBoy, 'type' => $type))], $notificationContent['title'], $notificationContent['message']);

            $orderDetails->notify($notifyHelper);
        }
        return 1;
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm($notification)
    {

        $data = $notification->data;
        unset($notification->data['user_id']);

        if (is_array($data)) {
            //return CustomerDeviceTokens::select('device_token')->where('user_id', $data['user_id'])->first()->device_token;

            return CustomerDeviceTokens::select('device_token')->where('user_id', $data['user_id'])->get()->pluck('device_token')->toArray();
        } else {
            return [];
        }
    }

    public function sendOrderTransactionEmail($params)
    {
        //$user_id = $params['user_id'];
        $order_id = $params['order_id'];
        $orderDetails = CustomerOrders::find($order_id);
        $inputData = array('order_id' => $order_id, 'user_id' => $orderDetails->customer_id);
        $inputData = json_encode($inputData);
        EmailHelper::sendEmail(
            $params['order_email_template'],
            [
                'email_to' => $orderDetails->userCustomer->email,
                'orderId' => $params['order_id'],
                'orderDate' => date("Y-m-d", strtotime($orderDetails->created_at)),
                'customerName' => $orderDetails->userCustomer->first_name . ' ' . $orderDetails->userCustomer->last_name,
                'paymentMethod' => $orderDetails->payment_type,
                'paymentReference' => ($orderDetails->razorpay_payment_id) ? $orderDetails->razorpay_payment_id : '-',
                'totalAmt' => $orderDetails->net_amount,
                'deliveryCharge' => $orderDetails->delivery_charge,
                'deliveryDate' => $orderDetails->delivery_date,
                'isEmailVerified' => $orderDetails->userCustomer->email_verified

            ],
            [
                'attachment' => isset($params['attachment']) ? $params['attachment'] : []
            ]

        );
        return 1;
    }

    public function generateInvoice($params)
    {
        //$user_id = $params['user_id'];
        $order_id = $params['order_id'];
        // $fileName = date('Y') . date('m') . PdfHelper::randomNumber(4);
        $invoiceDate = date('d M Y'); //current date
        $orderDetails = CustomerOrders::find($order_id);
        $inputData = array('order_id' => $order_id, 'user_id' => $orderDetails->customer_id);
        $inputData = json_encode($inputData);
        $productDetails = DB::select('call getOrderDetails(?)', [$inputData]);
        $productStr = '';
        foreach ($productDetails as $product) {
            $productStr .= '
            <tr class="item">
                <td style="text-align: center;padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                ' . $product->product_name . '(' . $product->unit . ')
                </td>
                <td style="text-align: center;padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                ' .  $product->item_quantity . '
                </td>
                <td style="text-align: center;padding: 5px;vertical-align: border-bottom: 1px solid #eee;">
                ' . (($product->special_price != 0) ? $product->special_price : $product->selling_price) . '
                </td>
            </tr>
            ';
        }

        $invoiceTemplate = EmailHelper::getCustomerEmailTemplate($params['invoice_template'], '', [
            'orderId' => $params['order_id'],
            'orderDate' => date("Y-m-d", strtotime($orderDetails->created_at)),
            'address' => $orderDetails->customerShippingAddress->address,
            'landmark' => $orderDetails->customerShippingAddress->landmark,
            'area' => $orderDetails->customerShippingAddress->area,
            'city' => $orderDetails->customerShippingAddress->city->name,
            'state' => $orderDetails->customerShippingAddress->state->name,
            'pinCode' => $orderDetails->customerShippingAddress->pin_code,
            'name' => $orderDetails->customerShippingAddress->name,
            'mobileNumber' => $orderDetails->customerShippingAddress->mobile_number,
            'email' => '',
            'paymentMethod' => $orderDetails->payment_type,
            'paymentReference' => ($orderDetails->razorpay_payment_id) ? $orderDetails->razorpay_payment_id : '-',
            'productList' => $productStr,
            'grossAmount' => $orderDetails->gross_amount,
            'discount' => $orderDetails->discounted_amount,
            'deliveryCharge' => $orderDetails->delivery_charge,
            'total' => $orderDetails->net_amount,
            'deliveryDate' => $orderDetails->delivery_date
        ]);

        $filePath = public_path() . '/invoices/' . $orderDetails->customer_id . '/';  //  '/var/www/html/kisan_farm_fresh/public/invoices/';
        $fileName = (isset($params['file_name'])) ? $params['file_name'] : $order_id;
        DataHelper::checkDirectory($filePath);
        //Generating Invoice PDF and storing on local server

        $invoiceGeneratedPath = PdfHelper::generatePDF($invoiceTemplate, $filePath, $fileName, array('order_id' => $order_id, 'user_id' => $orderDetails->customer_id, 'file_name' => $fileName));
        return $invoiceGeneratedPath;
    }

    public function getOrderList($params)
    {
        $queryResult = DB::select('call getOrderList(?)', [$params]);
        // $result = collect($queryResult);
        $orderList = [];
        if (sizeof($queryResult) > 0) {
            foreach ($queryResult as $key => $val) {
                $orders["order_id"] = $val->id;
                $orders["delivery_details"] = array(
                    "date" => $val->delivery_date,
                    "slot" => "",
                    "order_status" => $val->order_status,
                    "address" => array(
                        "name" => $val->ua_user_name,
                        "address" => $val->address,
                        "landmark" => $val->landmark,
                        "pin_code" => $val->pin_code,
                        "area" => $val->area,
                        "city_name" => $val->city_name,
                        "state_name" => $val->state_name,
                        "is_primary" => $val->is_primary,
                        "mobile_number" => $val->mobile_number
                    ),
                    "delivery_boy_name" => $val->delivery_boy_name
                );
                $orders["payment_details"] = array(
                    "type" => $val->payment_type,
                    "net_amount" => round($val->net_amount, 2),
                    "gross_amount" => round($val->gross_amount, 2),
                    "discounted_amount" => round($val->discounted_amount, 2),
                    "delivery_charge" => round($val->delivery_charge, 2),
                    "order_id" => "",
                    "bill_no" => "",
                    "total_items" => $val->total_items,
                );
                $orderList[$key] = $orders;
                $inputData = array('order_id' => $val->id, 'user_id' => $val->customer_id);
                $inputData = json_encode($inputData);
                $orderDetails = DB::select('call getOrderDetails(?)', [$inputData]);
                if (sizeof($orderDetails) > 0) {
                    $orderList[$key]["products"] = $orderDetails;
                } else {
                    unset($orderList[$key]);
                }
            }
            $orderList = array_merge($orderList);
        }
        return $orderList;
    }

    public function getOrderListForDeliveryBoy($params)
    {
        $queryResult = DB::select('call getOrderListForDeliveryBoy(?)', [$params]);
        // $result = collect($queryResult);
        $orderList = [];
        if (sizeof($queryResult) > 0) {
            foreach ($queryResult as $key => $val) {
                $orders["order_id"] = $val->id;
                $orders["delivery_details"] = array(
                    "date" => $val->delivery_date,
                    "slot" => "",
                    "order_status" => $val->order_status,
                    "address" => array(
                        "name" => $val->ua_user_name,
                        "address" => $val->address,
                        "landmark" => $val->landmark,
                        "pin_code" => $val->pin_code,
                        "area" => $val->area,
                        "city_name" => $val->city_name,
                        "state_name" => $val->state_name,
                        "is_primary" => $val->is_primary,
                        "mobile_number" => $val->mobile_number
                    ),
                    "delivery_boy_name" => $val->delivery_boy_name
                );
                $orders["payment_details"] = array(
                    "type" => $val->payment_type,
                    "net_amount" => round($val->net_amount, 2),
                    "gross_amount" => round($val->gross_amount, 2),
                    "discounted_amount" => round($val->discounted_amount, 2),
                    "delivery_charge" => round($val->delivery_charge, 2),
                    "order_id" => "",
                    "bill_no" => "",
                    "total_items" => $val->total_items,
                );
                $orderList[$key] = $orders;
                $inputData = array('order_id' => $val->id, 'user_id' => $val->customer_id);
                $inputData = json_encode($inputData);
                $orderDetails = DB::select('call getOrderDetails(?)', [$inputData]);
                if (sizeof($orderDetails) > 0) {
                    $orderList[$key]["products"] = $orderDetails;
                } else {
                    unset($orderList[$key]);
                }
            }
            $orderList = array_merge($orderList);
        }
        return $orderList;
    }

    public function changeOrderStatus($params)
    {
        if ($params['order_status'] == 5) {
            return $this->cancelOrder($params['order_id'], 2, $params['order_note']);
        }
        $inputData = json_encode($params);
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare("CALL changeOrderStatus(?)");
        $stmt->execute([$inputData]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt->closeCursor();
        $reponse = json_decode($result['response']);
        if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
            return false;
        }
        $this->sendOrderTransactionNotification($params);
        if ($params['order_status'] == 4) {
            //$params['order_id'] = $params['order_id'];

            $orderDetails = CustomerOrders::find($params['order_id']);
            $params['invoice_template'] = 'IN_APP_INVOICE_AFTER_ORDER';
            $invoiceGeneratedPath =  $this->generateInvoice($params);
            $params['order_email_template'] = 'IN_APP_ORDER_DELIVERED_NOTIFICATION';
            if ($invoiceGeneratedPath) {
                $params['attachment'] = array(array('attachment' => 'invoices/' . $orderDetails->customer_id . '/' . $params['order_id'] . '.pdf'));
            }
            $this->sendOrderTransactionEmail($params);
        }
        return true;
    }

    public function getOrderStatus($params)
    {
        $orderStatus = CustomerOrders::select('order_status')->where('customer_id', $params['user_id'])->where('id', $params['order_id'])->get()->toArray();
        if (sizeof($orderStatus) > 0) {
            return array("status" => true, "order_status" => $orderStatus[0]['order_status']);
        }
        return array("status" => false, "order_status" => "");
    }

    public function paymentCallbackUrl($params)
    {
        //Log::info('inside model paymentCallbackUrl.', ['razorpay_order_id' => $params['razorpay_order_id'], 'razorpay_payment_id' => $params['razorpay_payment_id'], 'razorpay_signature' => $params['razorpay_signature']]);
        if (!empty($params['razorpay_payment_id']) && !empty($params['razorpay_order_id']) && !empty($params['razorpay_signature'])) {
            $order = CustomerOrders::select('id')->where('razorpay_order_id', $params['razorpay_order_id'])->get()->toArray();

            if (sizeof($order) > 0) {
                $updateOrder = CustomerOrders::where('razorpay_order_id', $params['razorpay_order_id'])->first();
                $updateOrder->order_status = 1;
                $updateOrder->razorpay_payment_id = $params['razorpay_payment_id'];
                $updateOrder->razorpay_signature = $params['razorpay_signature'];
                $updateOrder->update();
                $params['order_id'] = $updateOrder->id;
                $params['order_email_template'] = 'IN_APP_ORDER_PLACED_NOTIFICATION';
                
                $emailResult = $this->sendOrderTransactionEmail($params);
                $notificationResult = $this->sendOrderTransactionNotification($params);
               

                return true;
            }
            return false;
        }
        return false;
    }

    public function createOrderAtRazorpay($params)
    {
        try {
            // $inputData = array("amount" => number_format($params["order_amount"], 2, ".", ""), "currency" => "INR", "receipt" => "rcptid_" . $params["order_id"]);
            $inputData = array("amount" => $params["order_amount"], "currency" => "INR", "receipt" => "rcptid_" . $params["order_id"]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                // CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($inputData),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic cnpwX3Rlc3RfVmFjZVN1U1ZGaURXcTQ6WGdLY1dDb3BTRjlKZDhEUUQ0T3I2bXRr',
                    'Content-Type: application/json'
                ),
            ));

            $response = json_decode(curl_exec($curl));

            $err = curl_error($curl);
            curl_close($curl);
            if ($err || isset($response->error)) {
                return 0;
            }
            return $response->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function checkDeliveryBoyAvailability($params)
    {
        // validate customer
        $is_admin = (isset($params['is_admin']) && $params['is_admin'] == 1) ? 1 : 0;
        $usersData = User::select('id')->where('id', $params['user_id'])->where('status', 1)->get()->toArray();
        if (sizeof($usersData) == 0) {
            return array("status" => false, "message" => "Invalid user");
        }

        // validate customer address
        $userAddressData = UserAddress::select('id')->where('id', $params['address_id'])->where('user_id', $params['user_id'])->where('status', 1)->get()->toArray();
        if (sizeof($userAddressData) == 0) {
            return array("status" => false, "message" => "Invalid user address");
        }

        if (($params['delivery_date'] <= date('Y-m-d')) && $is_admin == 0) {
            return array("status" => false, "message" => "Invalid delivery date, must be greater than current date");
        }

        $inputData = json_encode($params);
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare("CALL checkDeliveryBoyAvailability(?)");
        $stmt->execute([$inputData]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt->closeCursor();
        $reponse = json_decode($result['response']);
        if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
            return array("status" => false, "message" => "Delivery boy is not available");
        }
        return array("status" => true, "message" => "Delivery boy is available");
    }

    public function assignDeliveryBoyToOrder($params)
    {

        $assignData['order_id'] = $params['order_id'];
        $assignData['delivery_date'] = $params['delivery_details']['date'];
        // $assignData['order_status'] = 1;
        $inputData = json_encode($assignData);
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $stmt = $pdo->prepare("CALL assignDeliveryBoyToOrder(?)");
        $stmt->execute([$inputData]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt->closeCursor();
        $reponse = json_decode($result['response']);
        if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
            return array("status" => false, "message" => "Delivery boy assigned successfully!");
        }
        return array("status" => true, "message" => "Delivery boy is available");
    }
}
