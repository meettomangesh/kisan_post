DELIMITER $$
DROP PROCEDURE IF EXISTS cancelOrder$$
CREATE PROCEDURE cancelOrder(IN inputData JSON)
cancelOrder:BEGIN
    DECLARE orderId,codId,codbId,productUnitsId,productUnitsIdOne,itemQuantity,itemQuantityOne,notFound,notFoundBasket INTEGER(10) DEFAULT 0;
    DECLARE actionType,orderStatusCancelled,isBasket TINYINT(1) DEFAULT 0;
    DECLARE reason VARCHAR(255) DEFAULT NULL;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE cancelOrder;
    END IF;
    SET orderId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id'));
    SET actionType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.type'));
    SET reason = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.reason'));
    SET orderStatusCancelled = 5;

    IF orderId = 0 AND actionType = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE cancelOrder;
    END IF;

    block1:BEGIN
        DECLARE orderCursor CURSOR FOR
        SELECT id,product_units_id,item_quantity,is_basket
        FROM customer_order_details
        WHERE order_id = orderId;

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
        OPEN orderCursor;
        orderLoop: LOOP
            FETCH orderCursor INTO codId,productUnitsId,itemQuantity,isBasket;
            IF(notFound = 1) THEN
                LEAVE orderLoop;
            END IF;

            IF actionType = 1 THEN
                DELETE FROM customer_order_status_track WHERE order_details_id = codId;
                DELETE FROM customer_order_details WHERE id = codId;
            ELSEIF actionType = 2 THEN
                UPDATE customer_order_details SET order_status = orderStatusCancelled WHERE id = codId;
                INSERT INTO customer_order_status_track (order_details_id,order_status,created_by)
                VALUES (codId,orderStatusCancelled,1);
            END IF;
            IF isBasket = 0 AND productUnitsId > 0 THEN
                UPDATE product_location_inventory SET current_quantity = current_quantity + itemQuantity WHERE product_units_id = productUnitsId;
            ELSE
                SET notFoundBasket = 0;
                block2:BEGIN
                    DECLARE basketCursor CURSOR FOR
                    SELECT id,product_units_id,item_quantity
                    FROM customer_order_details_basket
                    WHERE order_id = orderId AND order_details_id = codId;

                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFoundBasket = 1;
                    OPEN basketCursor;
                    basketLoop: LOOP
                        FETCH basketCursor INTO codbId,productUnitsIdOne,itemQuantityOne;
                        IF(notFoundBasket = 1) THEN
                            LEAVE basketLoop;
                        END IF;

                        IF actionType = 1 THEN
                            DELETE FROM customer_order_details_basket WHERE id = codbId AND order_id = orderId AND order_details_id = codId;
                        END IF;
                        UPDATE product_location_inventory SET current_quantity = current_quantity + itemQuantityOne WHERE product_units_id = productUnitsIdOne;
                        
                    END LOOP basketLoop;
                    CLOSE basketCursor;
                END block2;

            END IF;

        END LOOP orderLoop;
        CLOSE orderCursor;
    END block1;

    IF actionType = 1 THEN
        DELETE FROM customer_orders WHERE id = orderId;
    ELSEIF actionType = 2 THEN
        UPDATE customer_orders SET order_status = orderStatusCancelled, reject_cancel_reason = reason WHERE id = orderId;
    END IF;

    SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Order cancelled successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
    LEAVE cancelOrder;
END$$
DELIMITER ;