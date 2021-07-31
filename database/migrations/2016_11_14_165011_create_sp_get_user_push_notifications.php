<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserPushNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS getUserNotifications;
                        CREATE PROCEDURE getUserNotifications(IN userId INT,IN lastId INT)
                            BEGIN  
                                IF lastId = 0 THEN
                                    SELECT cpn.id,cpn.custom_data,cpn.push_text,cpn.created_at,cpn.deep_link_screen,mo.offer_end_date
                                    FROM user_push_notifications AS cpn
                                    -- LEFT JOIN merchant_offers AS mo ON cpn.custom_data = mo.id
                                    WHERE  1=1 
                                    -- FIND_IN_SET(cpn.merchant_id,merchantIds)
                                    AND cpn.user_id = userId
                                    -- AND FIND_IN_SET(cpn.loyalty_id,loyaltyIds)
                                    ORDER BY cpn.id DESC
                                    LIMIT 10;
                                ELSE
                                    SELECT cpn.id,cpn.custom_data,cpn.push_text,cpn.created_at,cpn.deep_link_screen,mo.offer_end_date
                                    FROM user_push_notifications AS cpn
                                    -- LEFT JOIN merchant_offers AS mo ON cpn.custom_data = mo.id
                                    WHERE 
                                        1=1
                                    -- FIND_IN_SET(cpn.merchant_id,merchantIds)
                                    AND cpn.user_id = userId
                                    -- AND FIND_IN_SET(cpn.loyalty_id,loyaltyIds)
                                    AND cpn.id < lastId
                                    ORDER BY cpn.id DESC
                                    LIMIT 10;            
                                END IF;
                            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerNotifications;');
    }

}
