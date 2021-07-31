<?php

/**
 * The helper library class for Data related functionality
 *
 *
 * @author 
 * @package Admin
 * @since 1.0
 */

namespace App\Helper;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use App\PushNotificationsTemplates;

class NotificationHelper extends Notification
{
    public $data;
    public $title;
    public $body;
    public $image;

    public function setParameters($data, $title, $body, $image = "")
    {
        $this->data = $data;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
    }
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData($this->data)
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->title)
                ->setBody($this->body)
                ->setImage($this->image))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
            );
    }

    /**
     * get Email body in Email Template format
     * @param string      $merchantId
     * @param string      $emailBody
     *
     * @return Response
     */
    public static function getPushNotificationTemplate($template_name, $body, $vars = [])
    {

        $vars['body'] = isset($body) ? $body : '';

        $vars['baseLogo'] = asset(config('services.miscellaneous.kff_logo_url'));
        $vars['iosLogo'] = asset(config('services.miscellaneous.ios_logo_url'));
        $vars['androidLogo'] = asset(config('services.miscellaneous.android_logo_url'));

        //$email_teplate_name = 'IN_USER_COMMUNICATION_MESSAGES';
        $pushContent = PushNotificationsTemplates::whereName($template_name)->firstOrFail()->toArray();

        $pushMessage = $pushContent['message'];
        $pushTitle = $pushContent['title'];
        foreach ($vars as $key => $var) {
            $pushMessage = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $pushMessage);
            $pushTitle = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $pushTitle);
        }
        return array(
            'message' =>  html_entity_decode($pushMessage),
            'title' =>  html_entity_decode($pushTitle),
            'deeplink' =>   $pushContent['deeplink']
        );
    }

    public static function getNotitificationTemplateName($orderStatus, $isDelivery = 0)
    {
        //0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled
        if ($isDelivery == 0) {
            switch ($orderStatus) {
                case 1:
                    return 'IN_APP_ORDER_PLACED';
                    break;
                case 2:
                    return 'IN_APP_ORDER_PICKED';
                    break;
                case 3:
                    return 'IN_APP_ORDER_OUT_FOR_DELIVERY';
                    break;
                case 4:
                    return 'IN_APP_ORDER_DELIVERED';
                    break;
                case 5:
                    return 'IN_APP_ORDER_CANCELLED';
                    break;
                default:
                    return 'IN_APP_ORDER_PLACED';
                    break;
            }
        } else {
            switch ($orderStatus) {
                case 1:
                    return 'IN_APP_ORDER_ASSIGNED_DELIVERY_BOY';
                    break;
                case 2:
                    return '';
                    break;
                case 3:
                    return '';
                    break;
                case 4:
                    return '';
                    break;
                case 5:
                    return 'IN_APP_ORDER_CANCELLED_DELIVERY_BOY';
                    break;
                default:
                    return '';
                    break;
            }
        }
    }


    // optional method when using kreait/laravel-firebase:^3.0, this method can be omitted, defaults to the default project
    public function fcmProject($notifiable, $message)
    {
        // $message is what is returned by `toFcm`
        return 'app'; // name of the firebase project to use
    }
}
