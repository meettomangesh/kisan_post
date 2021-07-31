<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpGetUserTypeRegionData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getUserTypeRegionData;
        CREATE PROCEDURE getUserTypeRegionData(IN inputData JSON)
        getUserTypeRegionData:BEGIN
            DECLARE userType,regionType INTEGER(10) DEFAULT 0;
            DECLARE customRegion TEXT DEFAULT '';
           -- [user_type] => 3 [custom_region] => 4,5 [region_type] => 2
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getUserTypeRegionData;
            ELSE
                SET userType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_type'));
                SET regionType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.region_type'));
                SET customRegion = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.custom_region'));

                IF userType IS NULL OR userType = 0 THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE getUserTypeRegionData;
                END IF;
            END IF;
                -- user type 3=delivery boy ,4=customer
                -- region_type 1=All 2:custom
            IF regionType = 1 THEN 
                    SELECT
                        CONCAT(users.first_name,'',users.last_name) AS name,
                        users.*
                    FROM
                        users
                    WHERE EXISTS
                        (
                        SELECT
                            *
                        FROM
                            roles
                        INNER JOIN role_user ON roles.id = role_user.role_id
                        WHERE
                            users.id = role_user.user_id AND roles.id = userType AND roles.deleted_at IS NULL
                    ) AND users.deleted_at IS NULL
                    AND users.status =1;
            ELSE 
                IF userType = 3 THEN 
                    SELECT
                        CONCAT(users.first_name,'',users.last_name) AS name,
                        users.*
                    FROM
                        users
                    WHERE EXISTS
                        (
                        SELECT
                            *
                        FROM
                            roles
                        INNER JOIN role_user ON roles.id = role_user.role_id
                        WHERE
                            users.id = role_user.user_id AND roles.id = userType AND roles.deleted_at IS NULL
                    ) AND users.deleted_at IS NULL
                        AND users.id IN(
                            SELECT DISTINCT user_id FROM region_user WHERE FIND_IN_SET(region_id,customRegion)
                        )
                        AND users.status =1;
                   
                ELSE 
                    SELECT
                        CONCAT(users.first_name,'',users.last_name) AS name,
                        users.*
                    FROM
                        users
                    WHERE EXISTS
                        (
                        SELECT
                            *
                        FROM
                            roles
                        INNER JOIN role_user ON roles.id = role_user.role_id
                        WHERE
                            users.id = role_user.user_id AND roles.id = userType AND roles.deleted_at IS NULL
                    ) AND users.deleted_at IS NULL
                        AND users.id IN(
                                SELECT DISTINCT t.user_id FROM (SELECT DISTINCT user_address.pin_code,user_address.user_id AS user_id,pin_codes.id AS pin_code_id FROM user_address JOIN pin_codes ON  pin_codes.pin_code= user_address.pin_code
                                ) AS t WHERE t.pin_code_id IN 
                                (select pin_code_id FROM pin_code_region WHERE FIND_IN_SET(region_id,customRegion) )
                        )
                        AND users.status =1;
                END IF;


            END IF;

        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserTypeRegionData');
    }
}
