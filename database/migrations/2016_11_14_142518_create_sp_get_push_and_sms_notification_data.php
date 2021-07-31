<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetPushAndSmsNotificationData extends Migration {

   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS getPushAndSmsNotificationData;
                        CREATE PROCEDURE getPushAndSmsNotificationData(IN conferenceId INT)
                        BEGIN
                            IF conferenceId = 0 THEN
                                SELECT p.id AS customer_id, p.mobile_no,cdt.device_token,cdt.device_type,cdt.status FROM participants AS p 
                                LEFT JOIN user_device_tokens AS cdt ON cdt.mobile_number = p.mobile_no
                                WHERE p.status = 1 
                                ORDER BY p.id DESC;
                            ELSE
                                SELECT p.id AS customer_id, p.mobile_no,cdt.device_token,cdt.device_type,cdt.status FROM participants AS p 
                                LEFT JOIN user_device_tokens AS cdt ON cdt.mobile_number = p.mobile_no
                                WHERE p.status = 1  
                                ORDER BY p.id DESC;
                            END IF;
                        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPushAndSmsNotificationData;');
    }


}
