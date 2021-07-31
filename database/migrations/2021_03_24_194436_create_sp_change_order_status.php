<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpChangeOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS changeOrderStatus;
        CREATE PROCEDURE changeOrderStatus(IN inputData JSON)
        changeOrderStatus:BEGIN
            DECLARE orderId,codId,notFound INTEGER(10) DEFAULT 0;
            DECLARE orderStatus TINYINT(1) DEFAULT 0;
            DECLARE orderNote VARCHAR(255) DEFAULT NULL;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE changeOrderStatus;
            END IF;
            SET orderId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id'));
            SET orderStatus = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_status'));
            SET orderNote = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_note'));
        
            IF orderId = 0 AND orderStatus = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE changeOrderStatus;
            END IF;
        
            block1:BEGIN
                DECLARE orderCursor CURSOR FOR
                SELECT id FROM customer_order_details WHERE order_id = orderId;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
                OPEN orderCursor;
                orderLoop: LOOP
                    FETCH orderCursor INTO codId;
                    IF(notFound = 1) THEN
                        LEAVE orderLoop;
                    END IF;
        
                    UPDATE customer_order_details SET order_status = orderStatus WHERE id = codId;
                    INSERT INTO customer_order_status_track (order_details_id,order_status,created_by)
                    VALUES (codId,orderStatus,1);
        
                END LOOP orderLoop;
                CLOSE orderCursor;
            END block1;
        
            UPDATE customer_orders SET order_status = orderStatus, reject_cancel_reason = orderNote WHERE id = orderId;
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Order status changed successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
            LEAVE changeOrderStatus;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS changeOrderStatus');
    }
}
