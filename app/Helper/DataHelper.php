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
use App\Models\CustomerLoyalty;
use App\PushNotificationsTemplates;

class DataHelper
{

    /**
     * Email verification key
     * @return string
     */
    public static function emailVerifyKey()
    {
        $length = 30;
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /**
     * To generate random alphabetical string
     * @param int $length
     * @return string
     */
    public static function randomAlphabeticStringGenerator($length)
    {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public static function generateBarcodeString($length)
    {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        // call the same function if the barcode exists already
        if (self::barcodeStringExists($key)) {
            return self::generateBarcodeString($length);
        }

        // otherwise, it's valid and can be used
        return $key;
    }

    public static function barcodeStringExists($str)
    {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return CustomerLoyalty::whereReferralCode($str)->exists();
    }

    /**
     * To generate URL param using package data
     * @param array $packageUrlData
     * @return array
     */
    public static function generateURLparam($packageUrlData)
    {
        if (!empty($packageUrlData) && count($packageUrlData) > 0) {
            $packageUrlData = $packageUrlData[0];

            foreach ($packageUrlData as $key => $packageUrl) {
                $packageUrlData->$key = strtolower(preg_replace('/\s+/', '-', trim($packageUrl)));
            }
            return $packageUrlData;
        } else {
            return [];
        }
    }

    /**
     * To generate the random promo code depends on rule for quantity,prefix,format type as well as length.
     * @param type $promocode_length
     * @param type $promocode_format_type
     * @param type $promocode_prefix
     * @return type
     */
    public static function randomPrmocodeGeneratorByLengthFormatPrefix($promocode_length, $promocode_format_type = 1, $promocode_prefix = '', $promocode_suffix = '')
    {
        $key = '';
        $promocode = $promocode_prefix;
        $alphaKeys = range('A', 'Z');
        $numKeys = range(0, 9);
        switch ($promocode_format_type) {
            case 1:
                $keys = array_merge($numKeys, $alphaKeys);
                break;
            case 2:
                $keys = array_merge($numKeys);
                break;
            case 3:
                $keys = array_merge($alphaKeys);
                break;
            default:
                $keys = array_merge($numKeys, $alphaKeys);
                break;
        }
        /*for ($i = 0; ($i < $promocode_length && strlen($promocode) < $promocode_length); $i++) {
            $promocode .= $keys[array_rand($keys)];
        }*/
        $promocode_temp_length = $promocode_length - (strlen($promocode_prefix) + strlen($promocode_suffix));

        for ($i = 0; $i < $promocode_temp_length; $i++) {
            $promocode .= $keys[array_rand($keys)];
        }
        $promocode .= $promocode_suffix;
        return substr($promocode, 0, $promocode_length);
    }

    public static function uploadImage($fileObj, $path, $id = 0)
    {
        // $path = '/images/categories/';
        if ($id != 0) {
            $path .= $id;
            if (!File::exists(public_path() . $path)) {
                File::makeDirectory(public_path() . $path, 0775, true);
            }
            $path .= '/';
        }
        $var = date_create();
        $time = date_format($var, 'YmdHis');
        $imageName = $time . '-' . $fileObj->getClientOriginalName();
        $fileObj->move(base_path() . '/public' . $path, $imageName);
        return $path . $imageName;
    }

    /**
     * upload brand image
     * @return String
     */
    public static function uploadInvoicePdf($fileObj, $path, $id = 0)
    {
        // $path = '/images/categories/';
        if ($id != 0) {
            $path .= $id;
            if (!File::exists(public_path() . $path)) {
                File::makeDirectory(public_path() . $path, 0644, true);
            }
            $path .= '/';
        }
        $var = date_create();
        $time = date_format($var, 'YmdHis');
        $imageName = $time . '-' . $fileObj->getClientOriginalName();
        $fileObj->move(base_path() . '/public' . $path, $imageName);
        return $path . $imageName;
    }

    /**
     * check and create directory 
     */
    public static function checkDirectory($dirPath = '')
    {
        if (!File::isDirectory($dirPath)) {

            File::makeDirectory($dirPath, 0777, true, true);
        }
    }

    public static function getDeeplinkData()
    {
        return PushNotificationsTemplates::all()->pluck('deeplink', 'deeplink')->toArray();

        // return([
        //     "ORDERS" => "ORDERS",
        //     "OFFERS" => "OFFERS"
        // ]);
    }

    public static function encrypt($ipString)
    {
        $ciphering = config('services.miscellaneous.CIPHERING');
        $encryption_iv = config('services.miscellaneous.ENCRYPTION_IV');
        $hash_key = config('services.miscellaneous.HASH_KEY');
        return openssl_encrypt(
            $ipString,
            $ciphering,
            $hash_key,
            0,
            $encryption_iv
        );
    }

    public static function decrypt($ipString)
    {
        $ciphering = config('services.miscellaneous.CIPHERING');
        $encryption_iv = config('services.miscellaneous.ENCRYPTION_IV');
        $hash_key = config('services.miscellaneous.HASH_KEY');
        return openssl_decrypt(
            $ipString,
            $ciphering,
            $hash_key,
            0,
            $encryption_iv
        );
    }
}
