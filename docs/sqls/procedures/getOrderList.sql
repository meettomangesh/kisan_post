DELIMITER $$
DROP PROCEDURE IF EXISTS getOrderList$$
CREATE PROCEDURE getOrderList(IN inputData JSON)
getOrderList:BEGIN
    DECLARE customerId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE getOrderList;
    ELSE
        SET customerId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
        SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
        SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
        IF customerId IS NULL OR customerId = 0 THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE getOrderList;
        END IF;
    END IF;

    IF pageNumber > 0 THEN
        SET pageNumber = pageNumber * noOfRecords;
    END IF;

    SELECT co.*, ua.name AS ua_user_name, ua.address, ua.landmark, ua.pin_code, ua.area, ua.is_primary, ua.mobile_number, (SELECT name FROM cities WHERE id = ua.city_id) AS city_name, (SELECT name FROM states WHERE id = ua.state_id) AS state_name,(SELECT CONCAT(first_name,' ',last_name) FROM users WHERE id = co.delivery_boy_id) AS delivery_boy_name
    FROM customer_orders AS co
    LEFT JOIN user_address AS ua ON ua.id = (SELECT id FROM user_address WHERE user_id = co.customer_id AND id = co.shipping_address_id AND status = 1 ORDER BY id ASC LIMIT 1)
    WHERE co.customer_id = customerId
    ORDER BY co.id DESC
    LIMIT noOfRecords
    OFFSET pageNumber;

END$$
DELIMITER ;
