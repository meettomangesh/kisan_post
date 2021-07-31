<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpAssignDeliveryBoyToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS assignDeliveryBoyToOrder;
        CREATE PROCEDURE assignDeliveryBoyToOrder(IN inputData JSON)
        assignDeliveryBoyToOrder:BEGIN
            DECLARE orderId,userId,maxOrderCount,notFound,isDeliveryBoyAssigned INTEGER(10) DEFAULT 0;
            DECLARE deliveryDate DATE DEFAULT NULL;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE assignDeliveryBoyToOrder;
            END IF;
            SET orderId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id'));
            SET deliveryDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.delivery_date'));
        
            IF orderId = 0 OR deliveryDate IS NULL THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE assignDeliveryBoyToOrder;
            END IF;
        
            block1:BEGIN
                DECLARE assignDeliveryBoyCursor CURSOR FOR
                SELECT ru.user_id,rm.max_order_count
                FROM customer_orders AS co
                JOIN user_address AS ua ON ua.id = co.shipping_address_id
                JOIN pin_codes AS pc ON pc.pin_code = ua.pin_code
                JOIN pin_code_region AS pcr ON pcr.pin_code_id = pc.id
                JOIN region_user AS ru ON ru.region_id = pcr.region_id
                JOIN region_master AS rm ON rm.id = ru.region_id
                WHERE co.id = orderId AND co.order_status NOT IN (4,5) AND ua.status = 1 AND pc.status = 1 AND pcr.status = 1 AND ru.status = 1 AND rm.status = 1
                AND IF((SELECT status FROM users WHERE id = ru.user_id) = 1, true, false)
                AND IF((SELECT status FROM user_details WHERE user_id = ru.user_id AND role_id = 3) = 2, true, false);
        
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
                OPEN assignDeliveryBoyCursor;
                assignDeliveryBoyLoop: LOOP
                    FETCH assignDeliveryBoyCursor INTO userId,maxOrderCount;
                    IF(notFound = 1) THEN
                        LEAVE assignDeliveryBoyLoop;
                    END IF;
        
                    IF userId > 0 AND maxOrderCount > 0 AND (SELECT COUNT(id) FROM customer_orders WHERE delivery_date = deliveryDate AND order_status NOT IN (4,5) AND delivery_boy_id = userId) < maxOrderCount THEN
                        UPDATE customer_orders SET delivery_boy_id = userId WHERE id = orderId;
                        SET isDeliveryBoyAssigned = 1;
                        SET notFound = 1;
                    END IF;
        
                END LOOP assignDeliveryBoyLoop;
                CLOSE assignDeliveryBoyCursor;
            END block1;
        
            IF isDeliveryBoyAssigned = 1 THEN
                SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Delivery boy assigned successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
            ELSE
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Failed to assign delivery boy.','data',JSON_OBJECT(),'statusCode',520) AS response;
            END IF;
            LEAVE assignDeliveryBoyToOrder;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS assignDeliveryBoyToOrder');
    }
}
