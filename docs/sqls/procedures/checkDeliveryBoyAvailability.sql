DELIMITER $$
DROP PROCEDURE IF EXISTS checkDeliveryBoyAvailability$$
CREATE PROCEDURE checkDeliveryBoyAvailability(IN inputData JSON)
checkDeliveryBoyAvailability:BEGIN
    DECLARE userId,addressId,dbUserId,maxOrderCount,notFound,isDeliveryBoyAvailable INTEGER(10) DEFAULT 0;
    DECLARE deliveryDate DATE DEFAULT NULL;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE checkDeliveryBoyAvailability;
    END IF;
    SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
    SET addressId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.address_id'));
    SET deliveryDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.delivery_date'));

    IF userId = 0 OR addressId = 0 OR deliveryDate IS NULL THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE checkDeliveryBoyAvailability;
    END IF;

    block1:BEGIN
        DECLARE checkAvailabilityOfDeliveryBoyCursor CURSOR FOR
        SELECT ru.user_id,rm.max_order_count
        FROM users AS u
        JOIN user_address AS ua ON u.id = ua.user_id
        JOIN pin_codes AS pc ON pc.pin_code = ua.pin_code
        JOIN pin_code_region AS pcr ON pcr.pin_code_id = pc.id
        JOIN region_user AS ru ON ru.region_id = pcr.region_id
        JOIN region_master AS rm ON rm.id = ru.region_id
        WHERE u.id = userId AND ua.id = addressId AND u.status = 1 AND ua.status = 1 AND pc.status = 1 AND pcr.status = 1 AND ru.status = 1 AND rm.status = 1
        AND IF((SELECT status FROM user_details WHERE user_id = ru.user_id AND role_id = 3) = 2, true, false);

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
        OPEN checkAvailabilityOfDeliveryBoyCursor;
        checkAvailabilityOfDeliveryBoyLoop: LOOP
            FETCH checkAvailabilityOfDeliveryBoyCursor INTO dbUserId,maxOrderCount;
            IF(notFound = 1) THEN
                LEAVE checkAvailabilityOfDeliveryBoyLoop;
            END IF;

            IF dbUserId > 0 AND maxOrderCount > 0 AND (SELECT COUNT(id) FROM customer_orders WHERE delivery_date = deliveryDate AND order_status NOT IN (4,5) AND delivery_boy_id = dbUserId) < maxOrderCount THEN
                SET isDeliveryBoyAvailable = 1;
                SET notFound = 1;
            END IF;

        END LOOP checkAvailabilityOfDeliveryBoyLoop;
        CLOSE checkAvailabilityOfDeliveryBoyCursor;
    END block1;

    IF isDeliveryBoyAvailable = 1 THEN
        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Delivery boy is available.','data',JSON_OBJECT(),'statusCode',200) AS response;
    ELSE
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Delivery boy is not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
    END IF;
    LEAVE checkDeliveryBoyAvailability;
END$$
DELIMITER ;