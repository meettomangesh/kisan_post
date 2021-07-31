<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSpUpdateDeviceTokens extends Migration {

    /**
      /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS insertOrUpdateDeviceToken; 
           
CREATE  PROCEDURE insertOrUpdateDeviceToken(IN mobileNumber VARCHAR(25), IN token VARCHAR(255), IN deviceId VARCHAR(500), IN platform INT)
BEGIN  
        DECLARE response INT DEFAULT 0;
        IF NOT EXISTS (SELECT * FROM user_device_tokens WHERE mobile_number = mobileNumber AND device_type = platform) THEN
            INSERT INTO user_device_tokens (mobile_number, device_token, device_id, device_type, created_at) VALUES (mobileNumber, token, deviceId, platform, CURRENT_TIMESTAMP);
            SET response = 1;
        ELSE
            UPDATE user_device_tokens SET device_token = token, status = 1, device_id = deviceId,updated_at = CURRENT_TIMESTAMP WHERE mobile_number = mobileNumber AND device_type = platform;
            SET response = 2;
        END IF;  
        SELECT response;
    END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS insertOrUpdateDeviceToken');
    }
}
