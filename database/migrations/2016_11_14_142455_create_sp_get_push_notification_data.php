<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetPushNotificationData extends Migration {

   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS getPushNotificationData;
        CREATE PROCEDURE getPushNotificationData(IN notificationId INT)
                
                BEGIN
                    DECLARE userType,userRole,regionType INTEGER(10) DEFAULT 0;
                    DECLARE customRegion TEXT DEFAULT '';
                    SELECT region_type,user_role,user_type INTO regionType,userRole,userType FROM user_communication_messages  WHERE id=notificationId; 
                        IF userType = 1 THEN 
                            IF regionType = 1 THEN 
                                SELECT
                                    users.id AS user_id,
                                    CONCAT(users.first_name,'',users.last_name) AS name,
                                    users.mobile_number,
                                    cdt.device_token,
                                    cdt.device_type,
                                    cdt.status
                                FROM
                                    users
                                    LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                WHERE EXISTS
                                    (
                                    SELECT
                                        *
                                    FROM
                                        roles
                                    INNER JOIN role_user ON roles.id = role_user.role_id
                                    WHERE
                                        users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                ) AND users.deleted_at IS NULL
                                AND users.status =1
                                AND cdt.status = 1;
                            ELSE 
                                IF userRole = 3 THEN 
                                    SELECT
                                        users.id AS user_id,
                                        CONCAT(users.first_name,'',users.last_name) AS name,
                                        users.mobile_number,
                                        cdt.device_token,
                                        cdt.device_type,
                                        cdt.status
                                    FROM
                                        users
                                        LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                    WHERE EXISTS
                                        (
                                        SELECT
                                            *
                                        FROM
                                            roles
                                        INNER JOIN role_user ON roles.id = role_user.role_id
                                        WHERE
                                            users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                    ) AND users.deleted_at IS NULL
                                        AND users.id IN(
                                            SELECT DISTINCT user_id FROM region_user WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId)
                                        )
                                    AND users.status =1
                                    AND cdt.status = 1;
                                
                                ELSE 
                                    SELECT
                                        users.id AS user_id,
                                        CONCAT(users.first_name,'',users.last_name) AS name,
                                        users.mobile_number,
                                        cdt.device_token,
                                        cdt.device_type,
                                        cdt.status
                                    FROM
                                        users
                                        LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                    WHERE EXISTS
                                        (
                                        SELECT
                                            *
                                        FROM
                                            roles
                                        INNER JOIN role_user ON roles.id = role_user.role_id
                                        WHERE
                                            users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                    ) AND users.deleted_at IS NULL
                                        AND users.id IN(
                                                SELECT DISTINCT t.user_id FROM (SELECT DISTINCT user_address.pin_code,user_address.user_id AS user_id,pin_codes.id AS pin_code_id FROM user_address JOIN pin_codes ON  pin_codes.pin_code= user_address.pin_code
                                                ) AS t WHERE t.pin_code_id IN 
                                                (select pin_code_id FROM pin_code_region WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId) )
                                        )
                                    AND users.status =1
                                    AND cdt.status = 1;
                                END IF;
        
        
                            END IF;
        
                        ELSE 
                            SELECT
                                users.id AS user_id,
                                CONCAT(users.first_name,'',users.last_name) AS name,
                                users.mobile_number,
                                cdt.device_token,
                                cdt.device_type,
                                cdt.status
                            FROM
                                users
                                LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                WHERE users.id IN
                                    (
                                        SELECT user_id FROM user_user_communication_messages WHERE user_communication_messages_id=notificationId
                                ) AND users.deleted_at IS NULL
                                AND users.status =1
                                AND cdt.status = 1;
                        END IF;
                END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPushNotificationData;');
    }

}
