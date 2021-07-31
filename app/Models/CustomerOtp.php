<?php

namespace App\Models;
use PDO;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use DB;

class CustomerOtp extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_number', 'otp', 'mobile_number', 'platform_generated_on', 'otp_generated_for'
    ];


    public function generateOtp()
    {
        $digit = 6;
        $otpNumber = rand(pow(10, $digit - 1), pow(10, $digit) - 1);
        return $otpNumber;
    }

    // validate OTP from table based on input params
    public function validateOtp($otpNumber, $mobileNumber, $id, $platform, $ismobilePresent, $smsValidityTime)
    {

        try {
            if (!$ismobilePresent) {
                $mobileNumber = 0;
            }   

            $stmt = DB::connection()->getPdo()->prepare("CALL validateOtp(?, ?, ?, ?, ?)");
            $stmt->execute(array($otpNumber, $id, $platform, $mobileNumber, $smsValidityTime));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['currentOtpId'] > 0) {

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
          //  throw new Exception($e->getMessage());
            return $this->sendError('Error.', $$e->getMessage());
        }
    }
}
