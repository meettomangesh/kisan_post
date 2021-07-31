DELIMITER $$
DROP PROCEDURE IF EXISTS placeOrderDetails$$
CREATE PROCEDURE placeOrderDetails(IN inputData JSON)
placeOrderDetails:BEGIN
    DECLARE productsId,productUnitId,quantity,orderId,lastInsertId,lastInsertIdOrderDetails,customerId,productUnitsId,notFound,productsIdNew INTEGER(10) DEFAULT 0;
    DECLARE sellingPrice,specialPrice DECIMAL(14,4) DEFAULT 0.00;
    DECLARE specialPriceStartDate,specialPriceEndDate,expiryDate DATE DEFAULT NULL;
    DECLARE isBasket,orderStatus TINYINT(1) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE placeOrderDetails;
    END IF;
    SET productsId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.id'));
    SET productUnitId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.product_unit_id'));
    SET quantity = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.quantity'));
    SET sellingPrice = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.selling_price'));
    SET specialPrice = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price'));
    SET orderId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id'));
    SET customerId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.customer_id'));
    SET isBasket = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.is_basket'));
    SET orderStatus = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_status'));

    IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date')) != 'null' THEN
        SET specialPriceStartDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date'));
    END IF;
    IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date')) != 'null' THEN
        SET specialPriceEndDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date'));
    END IF;
    IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date')) != 'null' THEN
        SET expiryDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date'));
    END IF;
  
    INSERT INTO customer_order_details (customer_id,order_id,products_id,product_units_id,item_quantity,expiry_date,selling_price,special_price,special_price_start_date,special_price_end_date,is_basket,order_status,created_by)
    VALUES (customerId,orderId,productsId,productUnitId,quantity,expiryDate,sellingPrice,specialPrice,specialPriceStartDate,specialPriceEndDate,isBasket,orderStatus,1);

    IF LAST_INSERT_ID() > 0 THEN
        SET lastInsertIdOrderDetails = LAST_INSERT_ID();
        INSERT INTO customer_order_status_track (order_details_id,order_status,created_by)
        VALUES (lastInsertIdOrderDetails,orderStatus,1);

        IF isBasket = 0 THEN
            UPDATE product_location_inventory SET current_quantity = current_quantity - quantity WHERE product_units_id = productUnitId;
            IF (SELECT current_quantity FROM product_location_inventory WHERE product_units_id = productUnitId) = 0 THEN
                UPDATE product_units SET status = 0, updated_by = 1 WHERE id = productUnitId;
            END IF;
        END IF;
    ELSE
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Failed to add.','data',JSON_OBJECT(),'statusCode',101) AS response;
        LEAVE placeOrderDetails;
    END IF;
    IF isBasket = 1 THEN
        block1:BEGIN
            DECLARE basketProductUnitsCursor CURSOR FOR
            SELECT bpu.product_units_id, pu.products_id, pu.selling_price, pu.special_price, pu.special_price_start_date, pu.special_price_end_date,p.expiry_date
            FROM basket_product_units AS bpu
            JOIN product_units AS pu ON bpu.product_units_id = pu.id
            JOIN products AS p ON pu.products_id = p.id
            WHERE bpu.basket_id = productsId;

            DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
            OPEN basketProductUnitsCursor;
            basketProductUnitsLoop: LOOP
                FETCH basketProductUnitsCursor INTO productUnitsId,productsIdNew,sellingPrice,specialPrice,specialPriceStartDate,specialPriceEndDate,expiryDate;
                IF(notFound = 1) THEN
                    LEAVE basketProductUnitsLoop;
                END IF;

                INSERT INTO customer_order_details_basket (customer_id,order_id,order_details_id,products_id,product_units_id,item_quantity,expiry_date,selling_price,special_price,special_price_start_date,special_price_end_date,created_by)
                VALUES (customerId,orderId,lastInsertIdOrderDetails,productsIdNew,productUnitsId,quantity,expiryDate,sellingPrice,specialPrice,specialPriceStartDate,specialPriceEndDate,1);

                IF LAST_INSERT_ID() > 0 THEN
                    SET lastInsertId = LAST_INSERT_ID();
                    UPDATE product_location_inventory SET current_quantity = current_quantity - quantity WHERE product_units_id = productUnitsId;
                    IF (SELECT current_quantity FROM product_location_inventory WHERE product_units_id = productUnitsId) = 0 THEN
                        UPDATE product_units SET status = 0, updated_by = 1 WHERE id = productUnitsId;
                    END IF;
                ELSE
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Failed to add.','data',JSON_OBJECT(),'statusCode',101) AS response;
                    LEAVE placeOrderDetails;
                END IF;

            END LOOP basketProductUnitsLoop;
            CLOSE basketProductUnitsCursor;
        END block1;
    END IF;

    SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Order details added successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
    LEAVE placeOrderDetails;
END$$
DELIMITER ;