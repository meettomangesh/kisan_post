<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpValidateOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS validateOtp; 
        CREATE PROCEDURE `validateOtp`(IN otpNumber MEDIUMINT(9), IN otpId INT(11), IN platformGeneratedOn TINYINT(4), IN mobileNumber VARCHAR(15), IN smsValidityTime INT(11))
        validateOtp:BEGIN 
        DECLARE currentOtpId INT(11) DEFAULT 0;
        DECLARE response VARCHAR(50);
        SELECT id INTO currentOtpId FROM customer_otp WHERE otp = otpNumber AND id = otpId AND platform_generated_on = platformGeneratedOn AND mobile_number = mobileNumber AND created_at > DATE_SUB(NOW(),INTERVAL smsValidityTime MINUTE) AND otp_used <> 1;

        IF (currentOtpId > 0) THEN
            UPDATE customer_otp SET otp_used = 1 WHERE otp = otpNumber AND id = otpId;

            UPDATE customer_loyalty SET mobile_verified = 1 WHERE mobile_number = mobileNumber;
            
            IF platformGeneratedOn = 1 OR platformGeneratedOn = 2 THEN
            
                 UPDATE customer_loyalty SET is_app_installed = 1, app_installed_date = CURDATE() WHERE mobile_number = mobileNumber AND is_app_installed = 0;
                 IF ROW_COUNT() > 0 THEN
                    SET response = 'isAppInstalled';
                 ELSE
                    SET response = 'appNotInstalled'; 
                 END IF;
                 
            END IF;
        END IF;
        SELECT currentOtpId, response;
    END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS validateOtp');
    }
}
