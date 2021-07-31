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

use Exception;
use DB;
// use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade as PDF;
use Storage;

class PdfHelper
{

    /**
     * To Generate PDF
     *
     */
    public static function generatePDF($html, $filePath, $fileName, $details = [])
    {
        try {

            $emailText = $html;
            $emailTemplate = $emailText;
            $filepath = $filePath;
            $filename = $fileName;
            $finalFileNameWithPath = $filepath . $filename . '.pdf';
            $path = '';
            PDF::setOptions(['isRemoteEnabled' => true])->loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save($finalFileNameWithPath);
            if (file_exists($finalFileNameWithPath)) {
                //send mail of newly generated quotation pdf.
                $path = self::uploadInvoiceToS3($finalFileNameWithPath, $details);
            } else {
                $path = ''; //not generated successfully  
            }
        } catch (Exception $e) {
            $path = '';
        }
        return $path;
    }

    public static function getUplodedPath($obj)
    {
        if ($obj) {
            return config('services.ses.bucket_url').''.$obj;
           
        } else {
            return "";
        }
    }
    public static function uploadInvoiceToS3($finalFileNameWithPath, $details)
    {

        $s3 = Storage::disk('s3');
        $path = $details['user_id'] . "/";
        $final_url = $path . $details['file_name'] . '.pdf';

        try {

            $s3->put($final_url, file_get_contents($finalFileNameWithPath), 'public');
            unlink($finalFileNameWithPath);
        } catch (Aws\Exception\S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }
        return $path . $details['file_name'] . '.pdf';
    }

    //generate random numbers for unique invoice number
    public static function randomNumber($length)
    {
        $digits = '';
        $numbers = range(0, 9);
        shuffle($numbers);
        for ($i = 0; $i < $length; $i++)
            $digits .= $numbers[$i];
        return $digits;
    }
}
