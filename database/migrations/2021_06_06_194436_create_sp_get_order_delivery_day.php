<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpGetOrderDeliveryDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getOrderDeliveryDay;
        CREATE PROCEDURE getOrderDeliveryDay()
        getOrderDeliveryDay:BEGIN
            
            SELECT co.id AS order_id, u.id AS user_id, u.first_name, u.mobile_number, u.mobile_verified, u.email, u.email_verified
            FROM customer_orders AS co
            JOIN users AS u ON u.id = co.customer_id
            WHERE co.delivery_date = CURDATE() AND co.order_status NOT IN (4,5) AND co.delivery_boy_id > 0;
        
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getOrderDeliveryDay');
    }
}
