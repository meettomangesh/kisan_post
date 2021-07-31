DELIMITER $$
DROP PROCEDURE IF EXISTS getOrderDetails$$
CREATE PROCEDURE getOrderDetails(IN inputData JSON)
getOrderDetails:BEGIN
    DECLARE orderId,customerId INTEGER(10) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE getOrderDetails;
    ELSE
        SET orderId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id'));
        SET customerId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
        IF orderId IS NULL OR orderId = 0 THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE getOrderDetails;
        END IF;
    END IF;

    SELECT cod.id, cod.customer_id, cod.products_id, cod.product_units_id, cod.item_quantity, cod.expiry_date, TRUNCATE(cod.selling_price, 2) AS selling_price, TRUNCATE(cod.special_price, 2) AS special_price, cod.order_status,
    p.product_name, p.short_description,
    IF(cod.is_basket = 0 AND cod.product_units_id > 0, (SELECT unit FROM unit_master WHERE id = pu.unit_id), NULL) AS unit,
    (SELECT image_name FROM product_images WHERE products_id = p.id AND status = 1 AND deleted_at IS NULL ORDER BY id ASC LIMIT 1) AS product_image
    FROM customer_order_details AS cod
    JOIN products AS p ON p.id = cod.products_id
    LEFT JOIN product_units AS pu ON pu.id = cod.product_units_id
    WHERE order_id = orderId;

END$$
DELIMITER ;