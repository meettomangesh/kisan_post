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

use Illuminate\Support\Facades\File;
use App\Models\SystemEmail;
use PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class EmailHelper
{


    /**
     * Send an email using specified email template
     * 
     * @return response
     */
    public static function sendEmail($email_teplate_name, $vars, $params = [])
    {
        $replacements = [];
        $patterns = [];
        $vars['baseLogo'] = asset(config('services.miscellaneous.kff_logo_url'));
        $vars['iosLogo'] = asset(config('services.miscellaneous.ios_logo_url'));
        $vars['androidLogo'] = asset(config('services.miscellaneous.android_logo_url'));

        $emailContent =  SystemEmail::whereName($email_teplate_name)->firstOrFail()->toArray();

        if (isset($vars['email_to']) && !empty($vars['email_to'])) {
            $emailContent['email_to'] = $vars['email_to'];
        }
        if (isset($vars['email_cc']) && !empty($vars['email_cc'])) {
            $emailContent['email_cc'] = $vars['email_cc'];
        }

        //email message
        $emailMessage = $emailContent['text1'];
        foreach ($vars as $key => $var) {
            $emailMessage = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailMessage);
        }

        preg_match_all('/{\$\\S+}/i', $emailMessage, $matches);
        foreach ($matches[0] as $key => $match) {
            $matchName = str_replace('{$', '', (str_replace('}', '', $match)));
            $patterns[$key] = '/{\$\\' . $matchName . '}/'; //str_replace(':', '', $match);
            $replacements[$key] = config('settings.' . $matchName);
            if (!$replacements[$key]) {
                $replacements[$key] = str_replace(':', '', $match);
            }
        }
        $emailMessageNew = preg_replace($patterns, $replacements, $emailMessage);


        //subject
        $emailSubject = $emailContent['subject'];
        foreach ($vars as $key => $var) {
            $emailSubject = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailSubject);
        }
        preg_match_all('/{\$\\S+}/i', $emailSubject, $matches);
        foreach ($matches[0] as $key => $match) {
            $matchName = str_replace('{$', '', (str_replace('}', '', $match)));
            $patterns[$key] = '/{\$\\' . $matchName . '}/'; //str_replace(':', '', $match);
            $replacements[$key] = config('settings.' . $matchName);
            if (!$replacements[$key]) {
                $replacements[$key] = str_replace(':', '', $match);
            }
        }
        $emailSubjectNew = preg_replace($patterns, $replacements, $emailSubject);

        $emailContent['text1'] = $emailMessageNew;
        $emailContent['subject'] = $emailSubjectNew;

        if (str_contains($emailContent['email_from'], '<')) {
            $str = explode('<', $emailContent['email_from']);
            $email = preg_replace('/\>/', '', $str[1]);
            $name = $str[0];
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            $emailContent['email_from'] = [$email => $name];
        }

        $tags = [];
        if (!empty($emailContent['tags'])) {
            $tags = explode(',', $emailContent['tags']);
        }

        $toAddress = [];
        if (!empty($emailContent['email_to'])) {
            $allTo = explode(',', $emailContent['email_to']);
            foreach ($allTo as $to) {
                $toAddress[] = [
                    "name" => "",
                    "email" => $to,
                ];
            }
        }

        return self::send([
            'subject' => $emailSubjectNew,
            'message' => $emailMessageNew,
            'to' => $toAddress,
            'attachment' => $params['attachment'],
            'isEmailVerified' => (($vars['isEmailVerified']) && $vars['isEmailVerified'] == 1) ? 1 : 0,
        ]);
    }

    public static function send($emailData)
    {
        try {
            $subject = !empty($emailData['subject']) ? $emailData['subject'] : '';
            $message = !empty($emailData['message']) ? $emailData['message'] : '';
            $fromName = !empty($emailData['from']['name']) ? $emailData['from']['name'] : config('services.ses.fromname');
            $isEmailVerified = (($emailData['isEmailVerified']) && $emailData['isEmailVerified'] == 1) ? 1 : 0;
            $mail = new PHPMailer\PHPMailer(); // create a n
            $mail->isSMTP();
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = config('services.ses.host');
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = config('services.ses.username');
            $mail->Password = config('services.ses.password');
            $mail->SetFrom(config('services.ses.email'), $fromName, 0);
            $mail->Subject = $subject;
            $mail->Body = $message;
            if (!empty($emailData['to'])) {
                foreach ($emailData['to'] as $key => $toEmailInfo) {
                    $mail->AddAddress($toEmailInfo['email']);
                }
            }

            if (!empty($emailData['attachment'])) {
                foreach ($emailData['attachment'] as $key => $value) {
                    $mail->addAttachment($value['attachment']);
                }
            }

            if ($isEmailVerified == 1) {
                if ($mail->Send()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            //throw new Exception($e->getMessage());
            return false;
        }
    }

    // public static function sendBulk($emailData)
    // {

    //     try {
    //         $subject = !empty($emailData['subject']) ? $emailData['subject'] : '';
    //         $message = !empty($emailData['message']) ? $emailData['message'] : '';
    //         $fromName = !empty($emailData['from']['name']) ? $emailData['from']['name'] : config('services.ses.fromname') ;

    //         $mail = new PHPMailer\PHPMailer(); // create a n
    //         $mail->isSMTP();
    //         $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    //         $mail->SMTPAuth = true; // authentication enabled
    //         $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    //         $mail->Host = config('services.ses.host');
    //         $mail->Port = 465; // or 587
    //         $mail->IsHTML(true);
    //         $mail->Username = config('services.ses.username');
    //         $mail->Password = config('services.ses.password');
    //         $mail->SetFrom(config('services.ses.email'), $fromName, 0);
    //         $mail->Subject = $subject;
    //         $mail->Body = $message;
    //         if (!empty($emailData['to'])) {
    //             foreach ($emailData['to'] as $key => $toEmailInfo) {
    //                 $mail->AddAddress($toEmailInfo['email']);
    //             }
    //         }

    //         if ($mail->Send()) {
    //             return true;
    //         } else {
    //             return false;
    //         }

    //     } catch (Exception $e) {
    //         //throw new Exception($e->getMessage());
    //         return false;
    //     }

    // }


    // public static function sendMessage($subject, $message, $toEmail, $toName = null, $fromEmail = null, $fromName = null, $isHtml = true, $deliveryTime = '', $tags = [], $campaignId = '')
    // {
    //     try {
    //         $fromName = !empty($fromName) ? $fromName : config('services.ses.fromname') ;

    //         $mail = new PHPMailer\PHPMailer(); // create a n
    //         $mail->isSMTP();
    //         $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    //         $mail->SMTPAuth = true; // authentication enabled
    //         $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    //         $mail->Host = config('services.ses.host');
    //         $mail->Port = 465; // or 587
    //         $mail->IsHTML(true);
    //         $mail->Username = config('services.ses.username');
    //         $mail->Password = config('services.ses.password');
    //         $mail->SetFrom(config('services.ses.email'), $fromName, 0);
    //         $mail->Subject = $subject;
    //         $mail->Body = $message;

    //         $mail->AddAddress($toEmail);
    //         if ($mail->Send()) {
    //             return true;
    //         } else {
    //             return false;
    //         }

    //     } catch (Exception $e) {
    //         //throw new Exception($e->getMessage());
    //         return false;
    //     }

    // }

    // public static function sendBulkMessage($toList, $subject, $message, $fromEmail = null, $fromName = null, $isHtml = true, $deliveryTime = '', $tags = [], $campaignId = '')
    // {
    //     try {
    //         $fromName = !empty($fromName) ? $fromName : config('services.ses.fromname') ;

    //         $mail = new PHPMailer\PHPMailer(); // create a n
    //         $mail->isSMTP();
    //         $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    //         $mail->SMTPAuth = true; // authentication enabled
    //         $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    //         $mail->Host = config('services.ses.host');
    //         $mail->Port = 465; // or 587
    //         $mail->IsHTML(true);
    //         $mail->Username = config('services.ses.username');
    //         $mail->Password = config('services.ses.password');
    //         $mail->SetFrom(config('services.ses.email'), $fromName, 0);
    //         $mail->Subject = $subject;
    //         $mail->Body = $message;


    //         foreach ($toList as $key => $toEmailInfo) {
    //             $mail->AddAddress($toEmailInfo['email']);
    //         }
    //         if ($mail->Send()) {
    //             return true;
    //         } else {
    //             return false;
    //         }

    //     } catch (Exception $e) {
    //         //throw new Exception($e->getMessage());
    //         return false;
    //     }

    // }

    /**
     * get Email body in Email Template format
     * @param string      $merchantId
     * @param string      $emailBody
     *
     * @return Response
     */
    public static function getCustomerEmailTemplate($email_teplate_name, $emailBody, $vars = [])
    {
        $inloyalLogo1 = config('services.miscellaneous.kff_logo_url');

        $vars['emailBody'] = isset($emailBody) ? $emailBody : '';

        $vars['baseLogo'] = asset(config('services.miscellaneous.kff_logo_url'));
        $vars['iosLogo'] = asset(config('services.miscellaneous.ios_logo_url'));
        $vars['androidLogo'] = asset(config('services.miscellaneous.android_logo_url'));

        //$email_teplate_name = 'IN_USER_COMMUNICATION_MESSAGES';
        $emailContent = SystemEmail::whereName($email_teplate_name)->firstOrFail()->toArray();

        $emailMessage = $emailContent['text1'];
        foreach ($vars as $key => $var) {
            $emailMessage = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailMessage);
        }
        return html_entity_decode($emailMessage);
    }



    public function map_explode($email)
    {
        return ['email' => $email];
    }

    public function explodeEmails($emails)
    {
        if (empty($emails)) {
            return '';
        }
        return  array_map(array($this, 'map_explode'),  explode(",", $emails));
    }
}
