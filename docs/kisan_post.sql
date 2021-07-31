-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 01, 2021 at 12:47 AM
-- Server version: 5.7.34-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kisan_post`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addUpdateCampaignOffer` (IN `inputData` JSON)  addUpdateCampaignOffer:BEGIN
    DECLARE campaignCategoryId,campaignMasterId,codeType,rewardTypeXValue,codeLength,isForInsert,lastInsertedId,codeFormat INTEGER(10) DEFAULT 0;
    DECLARE rewardType,campaignUse,campaignUseValue INTEGER(10) DEFAULT 1;
    DECLARE title,campaignStartDate,campaignEndDate VARCHAR(255) DEFAULT '';
    DECLARE codePrefix,codeSuffix VARCHAR(5) DEFAULT '';
    DECLARE campaignDescription,platforms VARCHAR(255) DEFAULT '';
    DECLARE ipStatus,targetCustomer TINYINT(3) DEFAULT 1;
    DECLARE targetCustomerValue JSON ;
    DECLARE targetCustomerValueStr TEXT DEFAULT '';
    DECLARE i,userId,quantity,couponLengthTemp INTEGER(10) DEFAULT 0;
    DECLARE promoCode VARCHAR(20) DEFAULT '';
    

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE addUpdateCampaignOffer;
    END IF;
    SET campaignCategoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.campaign_category_id'));
    SET campaignMasterId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.campaign_master_id'));
    SET title = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.title'));
    SET campaignDescription = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.description'));
    SET codeType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.code_type'));
    SET rewardTypeXValue = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.reward_value'));
    SET campaignStartDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.start_date'));
    SET campaignEndDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.end_date'));
    SET codePrefix = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.code_prefix'));
    SET codeSuffix = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.code_suffix'));
    SET codeLength = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.code_length'));
    SET ipStatus = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.status'));
    SET targetCustomer = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.target_customer'));
    SET targetCustomerValue = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.target_customer_value'));

    IF targetCustomer = 1 THEN 
        SELECT JSON_ARRAYAGG(id) INTO targetCustomerValue FROM users 
            JOIN role_user ON role_user.user_id=users.id
            WHERE role_user.role_id=4;
    END IF;

    IF codePrefix IS NULL OR codePrefix='null' THEN 
        SET codePrefix = '';
    END IF;

    IF codeSuffix IS NULL OR codeSuffix='null' THEN 
        SET codeSuffix = '';
    END IF;
    
    SET targetCustomerValueStr = implode(JSON_OBJECT(
        'paramObjectArr',(JSON_EXTRACT(targetCustomerValue,'$'))
    ));

    SET platforms = '1,2';
    SET rewardType = 3;
    SET campaignUse = 2;
    SET codeFormat = 2;

    IF campaignCategoryId = 0 OR campaignCategoryId IS NULL OR campaignMasterId = 0 OR campaignMasterId IS NULL OR title IS NULL OR title = '' THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE addUpdateCampaignOffer;
    END IF;

    START TRANSACTION;

    IF NOT EXISTS(SELECT id FROM promo_code_master WHERE promo_code_master.title = title AND deleted_at IS NULL) THEN
        INSERT INTO promo_code_master (`campaign_category_id`,`campaign_master_id`,`title`,`description`,`start_date`,`end_date`,`platforms`,`target_customer`,`target_customer_value`,`reward_type`,`reward_type_x_value`,`campaign_use`,`campaign_use_value`,`code_type`,`status`,`created_by`) VALUES
            (campaignCategoryId,campaignMasterId,title,campaignDescription,campaignStartDate,campaignEndDate,platforms,targetCustomer,targetCustomerValueStr,rewardType,rewardTypeXValue,campaignUse,campaignUseValue,codeType,ipStatus,1);
        
            IF LAST_INSERT_ID() = 0 THEN
                ROLLBACK;
                SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'There is problem to add campaign offers.','data',JSON_OBJECT(),'statusCode',500) AS response;
                LEAVE addUpdateCampaignOffer;
            
            ELSE
                SET lastInsertedId = LAST_INSERT_ID();
                SET isForInsert = 1;
                -- IF codeType = 1 THEN                    INSERT INTO promo_code_format_master (`promo_code_master_id`,`code_format`,`code_prefix`,`code_suffix`,`code_length`,`created_by`) VALUES
                        (lastInsertedId,codeFormat,codePrefix,codeSuffix,codeLength,1);
                    IF LAST_INSERT_ID() = 0 THEN
                        ROLLBACK;
                        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'There is problem to add campaign offers code format.','data',JSON_OBJECT(),'statusCode',500) AS response;
                        LEAVE addUpdateCampaignOffer;
                    END IF;
                -- END IF;                
                IF codeType = 1 THEN 
                    -- CODE FOR GENERIC CODE                    SET quantity = 1;
                    do_this:LOOP
                        SET couponLengthTemp = codeLength - IFNULL(LENGTH(codePrefix), 0) - IFNULL(LENGTH(codeSuffix), 0);
                        IF codeFormat = 1 THEN
                            SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(FLOOR(RAND() * 10000000000), couponLengthTemp, '1'), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                        ELSE
                            SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(CONV(FLOOR(RAND()*POW(36,8)), 10, 36), couponLengthTemp, 0), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                        END IF;

                        IF (SELECT COUNT(id) FROM promo_codes WHERE promo_code = promoCode) = 0 THEN
                            SET quantity = quantity - 1;
                            IF quantity = 0 THEN
                                LEAVE do_this;
                            END IF;
                        END IF;
                    END LOOP do_this;

                    WHILE i < JSON_LENGTH(targetCustomerValue) DO
                        SELECT JSON_EXTRACT(targetCustomerValue,CONCAT('$[',i,']')) INTO userId;
                        INSERT INTO promo_codes (`promo_code_master_id`,`user_id`,`promo_code`,`start_date`,`end_date`,`is_code_used`,`created_by`) VALUES
                            (lastInsertedId,userId,promoCode,campaignStartDate,campaignEndDate,0,1);
                        
                        SELECT i + 1 INTO i;
                    END WHILE;
                ELSE
                    -- CODE FOR UNIQUE CODE                    WHILE i < JSON_LENGTH(targetCustomerValue) DO
                        SELECT JSON_EXTRACT(targetCustomerValue,CONCAT('$[',i,']')) INTO userId;
                        -- SELECT userId;
                        SET couponLengthTemp = codeLength - IFNULL(LENGTH(codePrefix), 0) - IFNULL(LENGTH(codeSuffix), 0);
                        SET quantity = 1;
                        do_this:LOOP
                            IF codeFormat = 1 THEN
                                SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(FLOOR(RAND() * 10000000000), couponLengthTemp, '1'), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                            ELSE
                                SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(CONV(FLOOR(RAND()*POW(36,8)), 10, 36), couponLengthTemp, 0), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                            END IF;

                            IF (SELECT COUNT(id) FROM promo_codes WHERE promo_code = promoCode) = 0 THEN
                                SET quantity = quantity - 1;
                                IF quantity = 0 THEN
                                    LEAVE do_this;
                                END IF;
                            END IF;
                        END LOOP do_this;
                        
                        INSERT INTO promo_codes (`promo_code_master_id`,`user_id`,`promo_code`,`start_date`,`end_date`,`is_code_used`,`created_by`) VALUES
                            (lastInsertedId,userId,promoCode,campaignStartDate,campaignEndDate,0,1);
                        
                        SELECT i + 1 INTO i;
                    END WHILE;
                END IF;


            END IF;
            
    ELSE
        SET isForInsert = 0;
            UPDATE promo_code_master SET promo_code_master.status=ipStatus,promo_code_master.description=campaignDescription WHERE promo_code_master.title = title AND deleted_at IS NULL;
    END IF;
    COMMIT;
    IF isForInsert = 1 THEN
        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Campaign/Offer created successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
    ELSE
        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Campaign/Offer updated successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
    END IF;
    LEAVE addUpdateCampaignOffer;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `assignDeliveryBoyToOrder` (IN `inputData` JSON)  assignDeliveryBoyToOrder:BEGIN
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
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancelOrder` (IN `inputData` JSON)  cancelOrder:BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `changeOrderStatus` (IN `inputData` JSON)  changeOrderStatus:BEGIN
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
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkDeliveryBoyAvailability` (IN `inputData` JSON)  checkDeliveryBoyAvailability:BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkEmailVerified` (IN `customerId` INT)  BEGIN  
        SELECT email, email_verified, email_verify_key 
        FROM users 
        WHERE id=customerId;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `editCommunicationMessageSa` (IN `edit_id` INT)  BEGIN
                
                SELECT 
                 c.id, 
                
                c.message_title,
                c.message_type,
                CASE 
                    WHEN 
                        c.message_type = "1" 
                    THEN 
                        "Offer"
                    ELSE
                        "Message"
                    END AS message_type_name,
                c.offer_id,
                c.push_text, 
                c.deep_link_screen, 
                c.sms_text, 
                c.notify_users_by,
                c.email_from_name,
                c.email_from_email, 
                c.email_subject, 
                c.email_body, 
                c.email_tags, 
                c.test_mode, 
                c.test_email_address, 
                c.test_mobile_number, 
                c.message_send_time, 
                c.status, 
                c.created_by, 
                c.updated_by, 
                c.created_at, 
                c.updated_at,
                cm.name
            FROM
                customer_communication_messages as c
            WHERE
                c.id = edit_id;
                
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCommunicationMessageSa` ()  BEGIN
                
                SELECT 
                c.id, 
               
                c.message_title,
                c.message_type,
                CASE 
                    WHEN 
                        c.message_type = "1" 
                    THEN 
                        "Offer"
                    WHEN 
                        c.message_type = "2" 
                    THEN
						"Message"
                    WHEN 
                        c.message_type = "3" 
                    THEN                   
                        "Product"
					ELSE   
						""
                    END AS message_type_name,
                c.reference_id,                
                c.push_text, 
                c.deep_link_screen, 
                c.sms_text, 
                c.notify_users_by,
                c.email_from_name,
                c.email_from_email, 
                c.email_subject, 
                c.email_body, 
                c.email_tags, 
                c.test_mode, 
                c.test_email_address, 
                c.test_mobile_number, 
                c.message_send_time, 
                c.status, 
                c.created_by, 
                c.updated_by, 
                c.created_at, 
                c.updated_at,
                cf.name,
                c.email_count,
                c.sms_count,
                c.push_notification_count,
                c.push_notification_received_count
            FROM
                user_communication_messages as c
            ORDER BY
                c.id DESC;
                
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCustomerForBirthdayWishes` ()  getCustomerForBirthdayWishes:BEGIN
            
        SELECT u.id AS user_id, u.first_name, u.mobile_number, u.mobile_verified, u.email, u.email_verified, u.date_of_birth, ru.role_id
            FROM users AS u
            JOIN role_user AS ru ON ru.user_id = u.id
            WHERE ru.role_id = 4 AND MONTH(date_of_birth) = MONTH(CURDATE());
            -- AND DAY(date_of_birth) = DAY(CURDATE());        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getEmailNotificationData` (IN `notificationId` INT)  BEGIN
            DECLARE userType,userRole,regionType INTEGER(10) DEFAULT 0;
            DECLARE customRegion TEXT DEFAULT '';
            SELECT region_type,user_role,user_type INTO regionType,userRole,userType FROM user_communication_messages  WHERE id=notificationId; 
                IF userType = 1 THEN 
                    IF regionType = 1 THEN 
                        SELECT
                            CONCAT(users.first_name,' ',users.last_name) AS name,
                            users.email
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
                                users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                        ) AND users.deleted_at IS NULL
                        AND users.status = 1 AND users.email_verified = 1 ;
                    ELSE 
                        IF userRole = 3 THEN 
                            SELECT
                                CONCAT(users.first_name,' ',users.last_name) AS name,
                                users.email
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
                                    users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                            ) AND users.deleted_at IS NULL
                                AND users.id IN(
                                    SELECT DISTINCT user_id FROM region_user WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId)
                                )
                                AND users.status =1 AND users.email_verified = 1;
                        
                        ELSE 
                            SELECT
                                CONCAT(users.first_name,' ',users.last_name) AS name,
                                users.email
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
                                    users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                            ) AND users.deleted_at IS NULL
                                AND users.id IN(
                                        SELECT DISTINCT t.user_id FROM (SELECT DISTINCT user_address.pin_code,user_address.user_id AS user_id,pin_codes.id AS pin_code_id FROM user_address JOIN pin_codes ON  pin_codes.pin_code= user_address.pin_code
                                        ) AS t WHERE t.pin_code_id IN 
                                        (select pin_code_id FROM pin_code_region WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId) )
                                )
                                AND users.status =1 AND users.email_verified = 1 ;
                        END IF;


                    END IF;

                ELSE 
                        SELECT
                            CONCAT(users.first_name,' ',users.last_name) AS name,
                            users.email
                        FROM
                            users
                        WHERE users.id IN
                            (
                                SELECT user_id FROM user_user_communication_messages WHERE user_communication_messages_id=notificationId
                        ) AND users.deleted_at IS NULL
                        AND users.status =1 AND users.email_verified = 1 ;
                END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLowQuantityProduct` ()  getLowQuantityProduct:BEGIN
        
            SELECT pu.id, p.product_name, um.unit, pli.current_quantity
            FROM product_units AS pu
            JOIN products AS p ON p.id = pu.products_id
            JOIN unit_master AS um ON um.id = pu.unit_id
            JOIN product_location_inventory AS pli ON pli.product_units_id = pu.id
            WHERE pli.current_quantity < 10;
            -- pli.current_quantity < p.notify_for_qty_below;        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getNotificationData` (IN `notificationId` INT)  BEGIN      
                        SELECT id, message_type, offer_id, push_text, deep_link_screen, sms_text, notify_users_by, 
                               email_tags, email_from_name, email_from_email, email_subject, email_body, message_send_time, status
                        FROM user_communication_messages 
                        WHERE IF(notificationId != 0, id = notificationId,(message_send_time <= date_add(now(),interval 2 minute)) AND DATE_FORMAT(message_send_time,'%y-%m-%d') = CURDATE()) AND processed = 0 AND status=1;       
                    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrderDeliveryDay` ()  getOrderDeliveryDay:BEGIN
            
            SELECT co.id AS order_id, u.id AS user_id, u.first_name, u.mobile_number, u.mobile_verified, u.email, u.email_verified
            FROM customer_orders AS co
            JOIN users AS u ON u.id = co.customer_id
            WHERE co.delivery_date = CURDATE() AND co.order_status NOT IN (4,5) AND co.delivery_boy_id > 0;
        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrderDetails` (IN `inputData` JSON)  getOrderDetails:BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrderList` (IN `inputData` JSON)  getOrderList:BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getOrderListForDeliveryBoy` (IN `inputData` JSON)  getOrderListForDeliveryBoy:BEGIN
            DECLARE deliveryBoyId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getOrderListForDeliveryBoy;
            ELSE
                SET deliveryBoyId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
                SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
                SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
                IF deliveryBoyId IS NULL OR deliveryBoyId = 0 THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE getOrderListForDeliveryBoy;
                END IF;
            END IF;
        
            IF pageNumber > 0 THEN
                SET pageNumber = pageNumber * noOfRecords;
            END IF;
        
            SELECT co.*, ua.name AS ua_user_name, ua.address, ua.landmark, ua.pin_code, ua.area, ua.is_primary, ua.mobile_number, (SELECT name FROM cities WHERE id = ua.city_id) AS city_name, (SELECT name FROM states WHERE id = ua.state_id) AS state_name,(SELECT CONCAT(first_name,' ',last_name) FROM users WHERE id = co.delivery_boy_id) AS delivery_boy_name
            FROM customer_orders AS co
            LEFT JOIN user_address AS ua ON ua.id = (SELECT id FROM user_address WHERE user_id = co.customer_id AND id = co.shipping_address_id AND status = 1 ORDER BY id ASC LIMIT 1)
            WHERE co.delivery_boy_id = deliveryBoyId AND co.order_status NOT IN (4,5)
            ORDER BY co.id DESC
            LIMIT noOfRecords
            OFFSET pageNumber;
        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductList` (IN `inputData` JSON)  getProductList:BEGIN
            DECLARE searchValue,sortType,sortOn,subCategoryIds VARCHAR(100) DEFAULT '';
            DECLARE categoryId,subCategoryId,noOfRecords,pageNumber,basketCategoryId INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getProductList;
            ELSE
                SET categoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.category_id'));
                SET subCategoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sub_category_id'));
                SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
                SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
                SET searchValue = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.search_value'));
                SET sortType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sort_type'));
                SET sortOn = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sort_on'));
                IF noOfRecords IS NULL OR pageNumber IS NULL THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE getProductList;
                END IF;
            END IF;
        
            IF pageNumber > 0 THEN
                SET pageNumber = pageNumber * noOfRecords;
            END IF;
        
            /* SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,IF(p.special_price IS NOT NULL AND p.special_price_start_date <= CURDATE() AND p.special_price_end_date >= CURDATE(), TRUNCATE(p.special_price, 2), 0.00)  AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity,pli.current_quantity
            FROM products AS p
            JOIN product_location_inventory AS pli ON p.id = pli.products_id
            WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND pli.current_quantity > 0 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND IF(categoryId = 0 OR categoryId IS NULL, 1=1, p.category_id = categoryId)
            -- AND (searchValue IS NULL, 1=1, p.product_name LIKE '%searchValue%')
            ORDER BY p.selling_price ASC
            LIMIT noOfRecords
            OFFSET pageNumber; */
        
            SET @whrCategory = ' 1=1 ';
            IF subCategoryId > 0 AND subCategoryId IS NOT NULL THEN
                SET @whrCategory = CONCAT(' p.category_id = ', subCategoryId, ' ');
            ELSEIF categoryId > 0 AND categoryId IS NOT NULL THEN
                SELECT id INTO basketCategoryId FROM categories_master WHERE cat_name = 'Basket';
                IF categoryId = basketCategoryId THEN 
                    SET @whrCategory = CONCAT(' p.is_basket = 1 ');
                ELSE
                    SELECT GROUP_CONCAT(id) INTO subCategoryIds FROM categories_master WHERE status = 1 AND cat_parent_id = categoryId;
                    SET @whrCategory = CONCAT(' p.category_id IN (', subCategoryIds, ') ');
                END IF;
            END IF;
        
            SET @orderBy = ' p.product_name ASC ';
            /* SET @orderBy = ' p.selling_price ASC ';
            IF sortType != '' AND sortOn != '' AND sortType != 'null' AND sortOn != 'null' THEN
                SET @orderBy = CONCAT(' ', sortType, ' ', sortOn, ' ');
            END IF; */
            SET @whrSearch = ' 1=1 ';
            IF searchValue != '' AND searchValue != 'null' THEN
                SET @whrSearch = CONCAT(' p.product_name LIKE '%', searchValue, '%'');
            END IF;
        
            /* SET @sqlStmt = CONCAT('SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,IF(p.special_price IS NOT NULL AND p.special_price_start_date <= CURDATE() AND p.special_price_end_date >= CURDATE(), TRUNCATE(p.special_price, 2), 0.00)  AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity,pli.current_quantity
            FROM products AS p
            JOIN product_location_inventory AS pli ON p.id = pli.products_id
            WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND pli.current_quantity > 0 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND '
            , @whrCategory, ' AND ', @whrSearch, ' ORDER BY ', @orderBy, ' LIMIT ', noOfRecords, ' OFFSET ', pageNumber); */
        
            SET @sqlStmt = CONCAT('SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,TRUNCATE(p.special_price, 2) AS special_price,p.special_price_start_date,p.special_price_end_date,p.is_basket,p.min_quantity,p.max_quantity
            FROM products AS p
            LEFT JOIN categories_master AS c ON c.id = p.category_id AND c.status = 1
            WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND '
            , @whrCategory, ' AND ', @whrSearch, ' ORDER BY ', @orderBy, ' LIMIT ', noOfRecords, ' OFFSET ', pageNumber);
        
            PREPARE stmt from @sqlStmt;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            -- SELECT JSON_OBJECT('status','SUCCESS', 'message','No record found.','data',JSON_OBJECT('statusCode',104),'statusCode',104) AS response;            -- LEAVE getProductList;        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPromoCodes` (IN `inputData` JSON)  getPromoCodes:BEGIN
            DECLARE userId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getPromoCodes;
            ELSE
                SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
                SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
                SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
                IF userId IS NULL OR userId = 0 THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE getPromoCodes;
                END IF;
            END IF;
        
            IF pageNumber > 0 THEN
                SET pageNumber = pageNumber * noOfRecords;
            END IF;
        
            SELECT pc.promo_code,pc.start_date,pc.end_date,pcm.title,
            CASE WHEN pcm.reward_type = 2 THEN CONCAT(pcm.reward_type_x_value,'%')
                 WHEN pcm.reward_type = 3 THEN CONCAT('Rs.',pcm.reward_type_x_value)
            ELSE '' END AS promo_code_value
            FROM promo_codes AS pc
            JOIN promo_code_master AS pcm ON pcm.id = pc.promo_code_master_id
            WHERE pc.user_id = userId AND pc.is_code_used = 0 AND pc.status = 1 AND pc.start_date <= CURDATE() AND pc.end_date >= CURDATE()
            ORDER BY pc.id DESC
            LIMIT noOfRecords
            OFFSET pageNumber;
        
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPushAndSmsNotificationData` (IN `conferenceId` INT)  BEGIN
                            IF conferenceId = 0 THEN
                                SELECT p.id AS customer_id, p.mobile_no,cdt.device_token,cdt.device_type,cdt.status FROM participants AS p 
                                LEFT JOIN user_device_tokens AS cdt ON cdt.mobile_number = p.mobile_no
                                WHERE p.status = 1 
                                ORDER BY p.id DESC;
                            ELSE
                                SELECT p.id AS customer_id, p.mobile_no,cdt.device_token,cdt.device_type,cdt.status FROM participants AS p 
                                LEFT JOIN user_device_tokens AS cdt ON cdt.mobile_number = p.mobile_no
                                WHERE p.status = 1  
                                ORDER BY p.id DESC;
                            END IF;
                        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPushNotificationData` (IN `notificationId` INT)  BEGIN
                    DECLARE userType,userRole,regionType INTEGER(10) DEFAULT 0;
                    DECLARE customRegion TEXT DEFAULT '';
                    SELECT region_type,user_role,user_type INTO regionType,userRole,userType FROM user_communication_messages  WHERE id=notificationId; 
                        IF userType = 1 THEN 
                            IF regionType = 1 THEN 
                                SELECT
                                    users.id AS user_id,
                                    CONCAT(users.first_name,'',users.last_name) AS name,
                                    users.mobile_number,
                                    cdt.device_token,
                                    cdt.device_type,
                                    cdt.status
                                FROM
                                    users
                                    LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                WHERE EXISTS
                                    (
                                    SELECT
                                        *
                                    FROM
                                        roles
                                    INNER JOIN role_user ON roles.id = role_user.role_id
                                    WHERE
                                        users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                ) AND users.deleted_at IS NULL
                                AND users.status =1
                                AND cdt.status = 1;
                            ELSE 
                                IF userRole = 3 THEN 
                                    SELECT
                                        users.id AS user_id,
                                        CONCAT(users.first_name,'',users.last_name) AS name,
                                        users.mobile_number,
                                        cdt.device_token,
                                        cdt.device_type,
                                        cdt.status
                                    FROM
                                        users
                                        LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                    WHERE EXISTS
                                        (
                                        SELECT
                                            *
                                        FROM
                                            roles
                                        INNER JOIN role_user ON roles.id = role_user.role_id
                                        WHERE
                                            users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                    ) AND users.deleted_at IS NULL
                                        AND users.id IN(
                                            SELECT DISTINCT user_id FROM region_user WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId)
                                        )
                                    AND users.status =1
                                    AND cdt.status = 1;
                                
                                ELSE 
                                    SELECT
                                        users.id AS user_id,
                                        CONCAT(users.first_name,'',users.last_name) AS name,
                                        users.mobile_number,
                                        cdt.device_token,
                                        cdt.device_type,
                                        cdt.status
                                    FROM
                                        users
                                        LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                    WHERE EXISTS
                                        (
                                        SELECT
                                            *
                                        FROM
                                            roles
                                        INNER JOIN role_user ON roles.id = role_user.role_id
                                        WHERE
                                            users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                    ) AND users.deleted_at IS NULL
                                        AND users.id IN(
                                                SELECT DISTINCT t.user_id FROM (SELECT DISTINCT user_address.pin_code,user_address.user_id AS user_id,pin_codes.id AS pin_code_id FROM user_address JOIN pin_codes ON  pin_codes.pin_code= user_address.pin_code
                                                ) AS t WHERE t.pin_code_id IN 
                                                (select pin_code_id FROM pin_code_region WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId) )
                                        )
                                    AND users.status =1
                                    AND cdt.status = 1;
                                END IF;
        
        
                            END IF;
        
                        ELSE 
                            SELECT
                                users.id AS user_id,
                                CONCAT(users.first_name,'',users.last_name) AS name,
                                users.mobile_number,
                                cdt.device_token,
                                cdt.device_type,
                                cdt.status
                            FROM
                                users
                                LEFT JOIN customer_device_tokens AS cdt ON cdt.user_id = users.id
                                WHERE users.id IN
                                    (
                                        SELECT user_id FROM user_user_communication_messages WHERE user_communication_messages_id=notificationId
                                ) AND users.deleted_at IS NULL
                                AND users.status =1
                                AND cdt.status = 1;
                        END IF;
                END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSmsNotificationData` (IN `notificationId` INT)  BEGIN
                   DECLARE userType,userRole,regionType INTEGER(10) DEFAULT 0;
                   DECLARE customRegion TEXT DEFAULT '';
                   SELECT region_type,user_role,user_type INTO regionType,userRole,userType FROM user_communication_messages  WHERE id=notificationId; 
                       IF userType = 1 THEN 
                           IF regionType = 1 THEN 
                               SELECT
                                   CONCAT(users.first_name,'',users.last_name) AS name,
                                   users.mobile_number
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
                                       users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                               ) AND users.deleted_at IS NULL
                               AND users.status =1;
                           ELSE 
                               IF userRole = 3 THEN 
                                   SELECT
                                       CONCAT(users.first_name,'',users.last_name) AS name,
                                       users.mobile_number
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
                                           users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                   ) AND users.deleted_at IS NULL
                                       AND users.id IN(
                                           SELECT DISTINCT user_id FROM region_user WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId)
                                       )
                                       AND users.status =1;
                               
                               ELSE 
                                   SELECT
                                       CONCAT(users.first_name,'',users.last_name) AS name,
                                       users.mobile_number
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
                                           users.id = role_user.user_id AND roles.id = userRole AND roles.deleted_at IS NULL
                                   ) AND users.deleted_at IS NULL
                                       AND users.id IN(
                                               SELECT DISTINCT t.user_id FROM (SELECT DISTINCT user_address.pin_code,user_address.user_id AS user_id,pin_codes.id AS pin_code_id FROM user_address JOIN pin_codes ON  pin_codes.pin_code= user_address.pin_code
                                               ) AS t WHERE t.pin_code_id IN 
                                               (select pin_code_id FROM pin_code_region WHERE region_id IN (SELECT region_id FROM region_user_communication_messages WHERE user_communication_messages_id = notificationId) )
                                       )
                                       AND users.status =1;
                               END IF;
       
       
                           END IF;
       
                       ELSE 
                               SELECT
                                   CONCAT(users.first_name,'',users.last_name) AS name,
                                   users.mobile_number
                               FROM
                                   users
                               WHERE users.id IN
                                   (
                                       SELECT user_id FROM user_user_communication_messages WHERE user_communication_messages_id=notificationId
                               ) AND users.deleted_at IS NULL
                               AND users.status =1;
                       END IF;
               END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserNotifications` (IN `userId` INT, IN `lastId` INT)  BEGIN  
                                IF lastId = 0 THEN
                                    SELECT cpn.id,cpn.custom_data,cpn.push_text,cpn.created_at,cpn.deep_link_screen,mo.offer_end_date
                                    FROM user_push_notifications AS cpn
                                    -- LEFT JOIN merchant_offers AS mo ON cpn.custom_data = mo.id                                    WHERE  1=1 
                                    -- FIND_IN_SET(cpn.merchant_id,merchantIds)                                    AND cpn.user_id = userId
                                    -- AND FIND_IN_SET(cpn.loyalty_id,loyaltyIds)                                    ORDER BY cpn.id DESC
                                    LIMIT 10;
                                ELSE
                                    SELECT cpn.id,cpn.custom_data,cpn.push_text,cpn.created_at,cpn.deep_link_screen,mo.offer_end_date
                                    FROM user_push_notifications AS cpn
                                    -- LEFT JOIN merchant_offers AS mo ON cpn.custom_data = mo.id                                    WHERE 
                                        1=1
                                    -- FIND_IN_SET(cpn.merchant_id,merchantIds)                                    AND cpn.user_id = userId
                                    -- AND FIND_IN_SET(cpn.loyalty_id,loyaltyIds)                                    AND cpn.id < lastId
                                    ORDER BY cpn.id DESC
                                    LIMIT 10;            
                                END IF;
                            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserTypeRegionData` (IN `inputData` JSON)  getUserTypeRegionData:BEGIN
            DECLARE userType,regionType INTEGER(10) DEFAULT 0;
            DECLARE customRegion TEXT DEFAULT '';
           -- [user_type] => 3 [custom_region] => 4,5 [region_type] => 2            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
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
                -- user type 3=delivery boy ,4=customer                -- region_type 1=All 2:custom            IF regionType = 1 THEN 
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

        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getWishlist` (IN `inputData` JSON)  getWishlist:BEGIN
            DECLARE userId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getWishlist;
            END IF;
        
            SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
            SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
            SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
            IF noOfRecords IS NULL OR pageNumber IS NULL THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getWishlist;
            END IF;
        
            IF pageNumber > 0 THEN
                SET pageNumber = pageNumber * noOfRecords;
            END IF;
        
            /* SELECT cw.id AS wishlist_id,cw.product_units_id,cw.is_basket,COALESCE(p1.id, p2.id) id,COALESCE(p1.product_name, p2.product_name) product_name,COALESCE(p1.short_description, p2.short_description) short_description,COALESCE(p1.expiry_date, p2.expiry_date) expiry_date,TRUNCATE(COALESCE(p1.selling_price, p2.selling_price), 2) selling_price,TRUNCATE(COALESCE(p1.special_price, p2.special_price), 2) special_price,COALESCE(p1.special_price_start_date, p2.special_price_start_date) special_price_start_date,COALESCE(p1.special_price_end_date, p2.special_price_end_date) special_price_end_date,COALESCE(p1.min_quantity, p2.min_quantity) min_quantity,COALESCE(p1.max_quantity, p2.max_quantity) max_quantity
            -- ,COALESCE(p1.status, p2.status) p_status,COALESCE(p1.deleted_at, p2.deleted_at) p_deleted_at,COALESCE(p1.stock_availability, p2.stock_availability) p_stock_availability
            ,IF(COALESCE(p1.status, p2.status) = 1 AND COALESCE(p1.deleted_at, p2.deleted_at) IS NULL AND COALESCE(p1.stock_availability, p2.stock_availability) = 1 AND IF(COALESCE(p1.expiry_date, p2.expiry_date) IS NOT NULL, COALESCE(p1.expiry_date, p2.expiry_date) >= CURDATE(), 1=1), 1, 0) AS is_active
            FROM customer_wishlist AS cw
            LEFT JOIN products AS p1 ON p1.id = (SELECT products_id FROM product_units WHERE id = cw.product_units_id) AND cw.is_basket = 0
            LEFT JOIN products AS p2 ON p2.id = cw.product_units_id AND cw.is_basket = 1
            LEFT JOIN categories_master AS c ON (c.id = p1.category_id OR c.id = p2.category_id) AND c.status = 1
            WHERE cw.user_id = userId
            -- HAVING p_status = 1 AND p_deleted_at IS NULL AND p_stock_availability = 1 AND IF(expiry_date IS NOT NULL, expiry_date >= CURDATE(), 1=1)
            ORDER BY cw.id DESC LIMIT noOfRecords OFFSET pageNumber; */
        
            SELECT cw.id AS wishlist_id,cw.is_basket,p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,TRUNCATE(p.special_price, 2) AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity
            ,IF(p.status = 1 AND p.deleted_at IS NULL AND p.stock_availability = 1 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND cm.status = 1, 1, 0) AS is_active
            FROM customer_wishlist AS cw
            LEFT JOIN products AS p ON p.id = cw.products_id
            LEFT JOIN categories_master AS cm ON cm.id = p.category_id
            WHERE cw.user_id = userId
            ORDER BY cw.id DESC LIMIT noOfRecords OFFSET pageNumber;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertCommunicationMessageSa` (IN `message_title` VARCHAR(100), IN `message_type` TINYINT, IN `reference_id` INT, IN `push_text` VARCHAR(320), IN `deep_link_screen` VARCHAR(50), IN `sms_text` VARCHAR(320), IN `notify_users_by` VARCHAR(10), IN `email_from_name` VARCHAR(100), IN `email_from_email` VARCHAR(100), IN `email_subject` VARCHAR(200), IN `email_body` VARCHAR(1000), IN `email_tags` VARCHAR(250), IN `test_mode` TINYINT, IN `test_email_address` VARCHAR(200), IN `test_mobile_number` VARCHAR(1000), IN `message_send_time` DATETIME, IN `status` TINYINT, IN `created_by` INT, IN `updated_by` INT, IN `created_at` TIMESTAMP, OUT `last_inserted_id` INT)  BEGIN
                
                INSERT INTO 
                user_communication_messages (
                   
                    message_title, 
                    message_type, 
                    reference_id,
                    push_text, 
                    deep_link_screen, 
                    sms_text, 
                    notify_users_by, 
                    email_from_name, 
                    email_from_email, 
                    email_subject, 
                    email_body, 
                    email_tags, 
                    test_mode, 
                    test_email_address, 
                    test_mobile_number, 
                    message_send_time, 
                    status, 
                    created_by, 
                    updated_by, 
                    created_at
                ) 
                VALUES (
                    
                    message_title, 
                    message_type, 
                    reference_id,
                    push_text, 
                    deep_link_screen, 
                    sms_text, 
                    notify_users_by, 
                    email_from_name, 
                    email_from_email, 
                    email_subject, 
                    email_body, 
                    email_tags, 
                    test_mode, 
                    test_email_address, 
                    test_mobile_number, 
                    message_send_time, 
                    status, 
                    created_by, 
                    updated_by, 
                    created_at
                );

            SET last_inserted_id = LAST_INSERT_ID();
            SELECT last_inserted_id;
                
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertOrUpdateDeviceToken` (IN `mobileNumber` VARCHAR(25), IN `token` VARCHAR(255), IN `deviceId` VARCHAR(500), IN `platform` INT)  BEGIN  
        DECLARE response INT DEFAULT 0;
        IF NOT EXISTS (SELECT * FROM user_device_tokens WHERE mobile_number = mobileNumber AND device_type = platform) THEN
            INSERT INTO user_device_tokens (mobile_number, device_token, device_id, device_type, created_at) VALUES (mobileNumber, token, deviceId, platform, CURRENT_TIMESTAMP);
            SET response = 1;
        ELSE
            UPDATE user_device_tokens SET device_token = token, status = 1, device_id = deviceId,updated_at = CURRENT_TIMESTAMP WHERE mobile_number = mobileNumber AND device_type = platform;
            SET response = 2;
        END IF;  
        SELECT response;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `placeOrderDetails` (IN `inputData` JSON)  placeOrderDetails:BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchPincode` (IN `pinCode` VARCHAR(100))  searchPincode:BEGIN 
            IF pinCode IS NULL OR pinCode = '' THEN 
                SELECT
                pin_codes.id,
                pin_code AS pc,
                pin_codes.country_id,
                pin_codes.state_id,
                pin_codes.city_id,
                cities.name AS city_name,
                states.name AS state_name,
                countries.name AS country_name
                FROM
                    pin_codes
                JOIN cities ON cities.id=pin_codes.city_id
                JOIN states ON states.id=pin_codes.state_id
                JOIN countries ON countries.id=pin_codes.country_id
                WHERE
                    pin_codes.status = 1 
                ORDER BY id;
            ELSE 
                SELECT
                pin_codes.id,
                pin_code AS pc,
                pin_codes.country_id,
                pin_codes.state_id,
                pin_codes.city_id,
                cities.name AS city_name,
                states.name AS state_name,
                countries.name AS country_name
                FROM
                    pin_codes
                JOIN cities ON cities.id=pin_codes.city_id
                JOIN states ON states.id=pin_codes.state_id
                JOIN countries ON countries.id=pin_codes.country_id
                WHERE
                    pin_codes.status = 1        
                AND
                pin_codes.pin_code LIKE CONCAT(pinCode, '%')
                ORDER BY id;
            END IF;

    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `storeDeviceToken` (IN `inputData` JSON)  storeDeviceToken:BEGIN
            DECLARE userId INTEGER(10) DEFAULT 0;
            DECLARE deviceType INTEGER(10) DEFAULT 1;
            DECLARE userRoleId TINYINT(1) DEFAULT 0;
            DECLARE deviceToken,deviceId VARCHAR(255) DEFAULT NULL;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeDeviceToken;
            END IF;
            SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
            SET deviceToken = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.device_token'));

            IF userId = 0 OR (deviceToken = '' OR deviceToken IS NULL) OR (deviceId = '' OR deviceToken IS NULL) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeDeviceToken;
            END IF;
           
            IF EXISTS(SELECT id FROM customer_device_tokens WHERE user_id = userId) THEN
                UPDATE customer_device_tokens SET device_id = deviceId, device_token = deviceToken WHERE user_id = userId AND user_role_id = userRoleId;
            ELSEIF EXISTS(SELECT id FROM users WHERE id = userId) THEN
                INSERT INTO customer_device_tokens (user_id,user_role_id,device_id,device_token)
                VALUES (userId,userRoleId,deviceId,deviceToken);
            ELSE
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Failed to store device token.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeDeviceToken;
            END IF;
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Device token stored successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
            LEAVE storeDeviceToken;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `storeProductInWishlist` (IN `inputData` JSON)  storeProductInWishlist:BEGIN
            DECLARE userId,productsId,wishlistId INTEGER(10) DEFAULT 0;
            DECLARE isBasket TINYINT(1) DEFAULT 0;
            DECLARE EXIT HANDLER FOR 1062
            BEGIN
                ROLLBACK;
                SELECT JSON_OBJECT('status','FAILURE','message','Already present in wishlist.','data',JSON_OBJECT('statusCode',404),'statusCode',404) AS response;
            END;
            
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
            SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
            SET productsId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.products_id'));
            SET isBasket = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.is_basket'));
        
            IF userId = 0 OR productsId = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
           
            IF NOT EXISTS(SELECT id FROM users WHERE id = userId) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Invalid user.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
        
            IF NOT EXISTS(SELECT id FROM products WHERE id = productsId AND IF(isBasket = 1, is_basket = 1, 1=1)) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Invalid product.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            ELSEIF EXISTS(SELECT id FROM customer_wishlist WHERE user_id = userId AND products_id = productsId) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Already present in wishlist.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            ELSE
                INSERT INTO customer_wishlist (user_id,products_id,is_basket,created_by)
                VALUES (userId,productsId,isBasket,userId);
                SET wishlistId = LAST_INSERT_ID();
            END IF;
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Product/Basket stored successfully.','data',JSON_OBJECT('wishlist_id', wishlistId),'statusCode',200) AS response;
            LEAVE storeProductInWishlist;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `storeUserLoginLogs` (IN `inputData` JSON)  storeUserLoginLogs:BEGIN
    
        DECLARE userId,idDeliveryBoy,roleId INTEGER(10) DEFAULT 0;
        DECLARE isLogin,platform TINYINT(3) DEFAULT 1;
        DECLARE token TEXT DEFAULT NULL;
        DECLARE loginTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    
        IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE storeUserLoginLogs;
        END IF;
        
        SET isLogin = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.is_login'));
        SET platform = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.platform'));
        SET userId  = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
    
    
        IF userId IS NULL OR userId = 0 THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'User data is not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE storeUserLoginLogs;
        END IF;
        SELECT role_id INTO roleId FROM role_user WHERE user_id = userId;
        
        IF roleId IS NULL OR roleId = 0 THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'User role data is not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE storeUserLoginLogs;
        END IF;
        IF roleId = 3 THEN 
    
            INSERT INTO user_login_logs (`user_id`,`platform`,`login_time`,`is_login`) VALUES (userId,platform,now(),isLogin);
    
        END IF;
        
        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Campaign/Offer updated successfully.','data',JSON_OBJECT(),'statusCode',200) AS response;
        LEAVE storeUserLoginLogs;
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCommunicationMessageSa` (IN `update_edit_id` INT, IN `update_message_title` VARCHAR(100), IN `update_message_type` TINYINT, IN `update_reference_id` INT, IN `update_push_text` VARCHAR(320), IN `update_deep_link_screen` VARCHAR(50), IN `update_sms_text` VARCHAR(320), IN `update_notify_users_by` VARCHAR(10), IN `update_email_from_name` VARCHAR(100), IN `update_email_from_email` VARCHAR(100), IN `update_email_subject` VARCHAR(200), IN `update_email_body` VARCHAR(1000), IN `update_email_tags` VARCHAR(1000), IN `update_test_mode` TINYINT, IN `update_test_email_address` VARCHAR(200), IN `update_test_mobile_number` VARCHAR(1000), IN `update_message_send_time` DATETIME, IN `update_status` TINYINT, IN `update_updated_by` INT, IN `update_updated_at` TIMESTAMP, OUT `success` INT)  BEGIN
                
                SET success = 0;
                UPDATE 
                    user_communication_messages 
                SET 
                    
                    message_title = update_message_title, 
                    message_type = update_message_type, 
                    offer_id = update_reference_id, 
                    push_text = update_push_text, 
                    deep_link_screen = update_deep_link_screen, 
                    sms_text = update_sms_text, 
                    notify_users_by = update_notify_users_by, 
                    email_from_name = update_email_from_name, 
                    email_from_email = update_email_from_email, 
                    email_subject = update_email_subject, 
                    email_body = update_email_body, 
                    email_tags = update_email_tags, 
                    test_mode = update_test_mode, 
                    test_email_address = update_test_email_address, 
                    test_mobile_number = update_test_mobile_number, 
                    message_send_time = update_message_send_time, 
                    status = update_status, 
                    updated_by = update_updated_by, 
                    updated_at = update_updated_at 
                WHERE 
                    id = update_edit_id;

                IF ROW_COUNT() > 0 THEN
                    SET success = 1;
                    END IF;
                    SELECT success;
                
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validateBasketProducts` (IN `inputData` JSON)  validateBasketProducts:BEGIN
            DECLARE basketId,productsId,productUnitsId,notFound INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateBasketProducts;
            END IF;
            SET basketId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.basket_id'));
            
            IF basketId = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateBasketProducts;
            END IF;
        
            block1:BEGIN
                DECLARE basketProductUnitsCursor CURSOR FOR
                SELECT bpu.product_units_id, pu.products_id
                FROM basket_product_units AS bpu
                JOIN product_units AS pu ON pu.id = bpu.product_units_id
                WHERE bpu.basket_id = basketId;
        
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
                OPEN basketProductUnitsCursor;
                basketProductUnitsLoop: LOOP
                    FETCH basketProductUnitsCursor INTO productUnitsId,productsId;
                    IF(notFound = 1) THEN
                        LEAVE basketProductUnitsLoop;
                    END IF;
        
                    IF NOT EXISTS(SELECT id FROM product_units WHERE id = productUnitsId AND products_id = productsId AND min_quantity <= 1 AND max_quantity >= 1 AND status = 1 AND deleted_at IS NULL) THEN
                        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Product unit data is not valid.','data',JSON_OBJECT(),'statusCode',520) AS response;
                        LEAVE validateBasketProducts;
                    END IF;
        
                    IF NOT EXISTS(SELECT id FROM product_location_inventory WHERE product_units_id = productUnitsId AND current_quantity >= 1) THEN
                        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Quantity not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
                        LEAVE validateBasketProducts;
                    END IF;
        
                END LOOP basketProductUnitsLoop;
                CLOSE basketProductUnitsCursor;
            END block1;        
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Basket data is valid.','data',JSON_OBJECT(),'statusCode',200) AS response;
            LEAVE validateBasketProducts;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validateOtp` (IN `otpNumber` MEDIUMINT(9), IN `otpId` INT(11), IN `platformGeneratedOn` TINYINT(4), IN `mobileNumber` VARCHAR(15), IN `smsValidityTime` INT(11))  validateOtp:BEGIN 
        DECLARE currentOtpId INT(11) DEFAULT 0;
        DECLARE response VARCHAR(50);
        SELECT id INTO currentOtpId FROM customer_otp WHERE otp = otpNumber AND id = otpId AND platform_generated_on = platformGeneratedOn AND mobile_number = mobileNumber AND created_at > DATE_SUB(NOW(),INTERVAL smsValidityTime MINUTE) AND otp_used <> 1;

        IF (currentOtpId > 0) THEN
            UPDATE customer_otp SET otp_used = 1 WHERE otp = otpNumber AND id = otpId;

            UPDATE customer_loyalty SET mobile_verified = 1 WHERE mobile_number = mobileNumber;
            
            IF platformGeneratedOn = 1 OR platformGeneratedOn = 2 THEN
            
                 UPDATE customer_loyalty SET is_app_installed = 1, app_installed_date = CURDATE() WHERE mobile_number = mobileNumber AND is_app_installed = 0;
                 IF ROW_COUNT() > 0 THEN
                    SET response = 'isAppInstalled';
                 ELSE
                    SET response = 'appNotInstalled'; 
                 END IF;
                 
            END IF;
        END IF;
        SELECT currentOtpId, response;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validateProduct` (IN `inputData` JSON)  validateProduct:BEGIN
            DECLARE productsId,productUnitId,quantity,notFound,productUnitsId INTEGER(10) DEFAULT 0;
            DECLARE sellingPrice,specialPrice DECIMAL(14,4) DEFAULT 0.00;
            DECLARE specialPriceStartDate,specialPriceEndDate,expiryDate DATE DEFAULT NULL;
            DECLARE isBasket TINYINT(1) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateProduct;
            END IF;
            SET productsId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.id'));
            SET productUnitId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.product_unit_id'));
            SET quantity = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.quantity'));
            SET sellingPrice = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.selling_price'));
            SET specialPrice = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price'));
            SET isBasket = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.is_basket'));
        
            IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date')) != 'null' THEN
                SET specialPriceStartDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_start_date'));
            END IF;
            IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date')) != 'null' THEN
                SET specialPriceEndDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.special_price_end_date'));
            END IF;
            IF JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date')) != '' AND JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date')) != 'null' THEN
                SET expiryDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.expiry_date'));
            END IF;
            
            IF productsId = 0 OR quantity = 0 OR sellingPrice = 0 OR sellingPrice = 0.00 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateProduct;
            END IF;
        
            IF NOT EXISTS(SELECT id FROM products WHERE id = productsId AND status = 1 AND deleted_at IS NULL
                AND IF(isBasket = 1, min_quantity <= quantity AND max_quantity >= quantity,1=1)
                AND IF(isBasket = 1 AND specialPrice > 0, CURDATE() >= specialPriceStartDate AND CURDATE() <= specialPriceEndDate, 1=1)) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Product id is not valid.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateProduct;
            END IF;
        
            IF isBasket = 0 THEN
                IF NOT EXISTS(SELECT id FROM product_units
                                WHERE id = productUnitId AND products_id = productsId AND min_quantity <= quantity AND max_quantity >= quantity AND status = 1 AND deleted_at IS NULL
                                AND IF(specialPrice > 0, CURDATE() >= specialPriceStartDate AND CURDATE() <= specialPriceEndDate, 1=1)) THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Product unit data is not valid.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE validateProduct;
                END IF;
        
                IF NOT EXISTS(SELECT id FROM product_location_inventory WHERE product_units_id = productUnitId AND current_quantity >= quantity) THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Quantity not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE validateProduct;
                END IF;
            ELSE
                block1:BEGIN
                    DECLARE basketProductUnitsCursor CURSOR FOR
                    SELECT bpu.product_units_id, pu.products_id
                    FROM basket_product_units AS bpu
                    JOIN product_units AS pu ON pu.id = bpu.product_units_id
                    WHERE bpu.basket_id = productsId;
        
                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
                    OPEN basketProductUnitsCursor;
                    basketProductUnitsLoop: LOOP
                        FETCH basketProductUnitsCursor INTO productUnitsId,productsId;
                        IF(notFound = 1) THEN
                            LEAVE basketProductUnitsLoop;
                        END IF;
        
                        IF NOT EXISTS(SELECT id FROM product_units WHERE id = productUnitsId AND products_id = productsId AND min_quantity <= 1 AND max_quantity >= 1 AND status = 1 AND deleted_at IS NULL) THEN
                            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Product unit data is not valid.','data',JSON_OBJECT(),'statusCode',520) AS response;
                            LEAVE validateProduct;
                        END IF;
        
                        IF NOT EXISTS(SELECT id FROM product_location_inventory WHERE product_units_id = productUnitsId AND current_quantity >= 1) THEN
                            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Quantity not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
                            LEAVE validateProduct;
                        END IF;
        
                    END LOOP basketProductUnitsLoop;
                    CLOSE basketProductUnitsCursor;
                END block1;
            END IF;
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Product data is valid.','data',JSON_OBJECT(),'statusCode',200) AS response;
            LEAVE validateProduct;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validatePromoCode` (IN `inputVoucherData` JSON)  validatePromoCode:BEGIN     
            DECLARE promoCode VARCHAR(100) DEFAULT '';
            DECLARE userId INTEGER DEFAULT 0;
            DECLARE endDate DATE DEFAULT NULL;
            DECLARE isCodeUsed, pcStatus TINYINT DEFAULT 0;
            
            IF inputVoucherData IS NOT NULL AND JSON_VALID(inputVoucherData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','statusCode',520) AS response;
                LEAVE validatePromoCode;
            ELSE
                SET promoCode = JSON_UNQUOTE(JSON_EXTRACT(inputVoucherData,'$.promo_code')); 
                SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputVoucherData,'$.user_id'));
        
                IF promoCode IS NULL OR userId = 0 THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input of validatePromoCode.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE validatePromoCode;           
                END IF;  
        
                /* IF EXISTS(SELECT id FROM promo_codes WHERE promo_code = promoCode AND user_id = userId AND promo_codes.status = 1 AND is_code_used = 0) THEN
                    SELECT JSON_OBJECT('status','SUCCESS','message','Promo code is valid.','data',JSON_OBJECT('statusCode',200),'statusCode',200) AS response;
                    LEAVE validatePromoCode;
                ELSE
                    SELECT JSON_OBJECT('status','FAILURE','message','Promo code is not valid.','data',JSON_OBJECT(),'statusCode',103) AS response;
                    LEAVE validatePromoCode;
                END IF; */
        
                IF NOT EXISTS(SELECT id FROM users WHERE users.id = userId AND users.status = 1) THEN
                    SELECT JSON_OBJECT('status','FAILURE','statusCode',101,'message','No record found for this user id.','data',JSON_OBJECT('user_id',userId)) AS response;
                    LEAVE validatePromoCode;
                END IF;
        
                IF EXISTS (SELECT id FROM promo_codes WHERE promo_codes.promo_code = promoCode AND promo_codes.user_id = userId) THEN
                    SELECT pc.end_date, pc.is_code_used, pc.status INTO endDate,isCodeUsed,pcStatus
                    FROM promo_codes AS pc
                    WHERE pc.promo_code = promoCode AND pc.user_id = userId;
        
                    IF isCodeUsed = 1 THEN
                        SELECT JSON_OBJECT('status','FAILURE','statusCode',103,'message','Promo code is already used.','data',JSON_OBJECT()) AS response;
                        LEAVE validatePromoCode;
                    ELSEIF DATE(endDate) < CURDATE() OR pcStatus = 2 THEN
                        SELECT JSON_OBJECT('status','FAILURE','statusCode',104,'message','Promo code is expired.','data',JSON_OBJECT()) AS response;
                        LEAVE validatePromoCode;
                    ELSE
                        SELECT JSON_OBJECT('status','SUCCESS','statusCode',200,'message','Promo code is valid.','data',JSON_OBJECT()) AS response;
                        LEAVE validatePromoCode;
                    END IF;
                ELSEIF EXISTS (SELECT id FROM promo_codes WHERE promo_code = promoCode) THEN
                    SELECT JSON_OBJECT('status','FAILURE','statusCode',105,'message','This Promo Code does not belong to your user id.','data',inputVoucherData) AS response;
                    LEAVE validatePromoCode;
                ELSE
                    SELECT JSON_OBJECT('status','FAILURE','statusCode',105,'message','Promo code is invalid.','data',inputVoucherData) AS response;
                    LEAVE validatePromoCode;
                END IF;
            END IF;                     
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `verifyEmail` (IN `emailVerifyKey` VARCHAR(50))  BEGIN
	DECLARE customerEmailAddress, emailAddress VARCHAR(255) DEFAULT "";
    DECLARE emailVerified TINYINT(4);
    
    SET emailVerified = 0;

	SELECT email INTO customerEmailAddress FROM users WHERE email_verify_key = emailVerifyKey LIMIT 1;
	IF customerEmailAddress != "" THEN
		SET emailVerified = 2;
		UPDATE users
		SET email_verified=1
		WHERE email_verify_key = emailVerifyKey;
		IF ROW_COUNT() > 0 THEN
			SET emailVerified = 1;
			SET emailAddress = customerEmailAddress;	
		END IF;
    END IF;
	
	SELECT emailAddress, emailVerified;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `users_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Male, 0: Female',
  `contact` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact number either phone or mobile',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User profile picture',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `user_type_id` int(10) UNSIGNED NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Banner, 2: Slider Image',
  `url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `basket_product_units`
--

CREATE TABLE `basket_product_units` (
  `id` int(10) UNSIGNED NOT NULL,
  `basket_id` int(10) UNSIGNED NOT NULL,
  `product_units_id` int(10) UNSIGNED NOT NULL COMMENT 'Product unit table ID',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands_master`
--

CREATE TABLE `brands_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `cat_id` int(10) UNSIGNED NOT NULL,
  `brand_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_description` text COLLATE utf8mb4_unicode_ci,
  `brand_logo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_categories_master`
--

CREATE TABLE `campaign_categories_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_master`
--

CREATE TABLE `campaign_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_category_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories_master`
--

CREATE TABLE `categories_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `cat_parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cat_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cat_image_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cat_description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_settings`
--

CREATE TABLE `config_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `short_code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Afghanistan', 'af', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(2, 'Albania', 'al', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(3, 'Algeria', 'dz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(4, 'American Samoa', 'as', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(5, 'Andorra', 'ad', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(6, 'Angola', 'ao', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(7, 'Anguilla', 'ai', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(8, 'Antarctica', 'aq', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(9, 'Antigua and Barbuda', 'ag', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(10, 'Argentina', 'ar', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(11, 'Armenia', 'am', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(12, 'Aruba', 'aw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(13, 'Australia', 'au', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(14, 'Austria', 'at', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(15, 'Azerbaijan', 'az', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(16, 'Bahamas', 'bs', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(17, 'Bahrain', 'bh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(18, 'Bangladesh', 'bd', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(19, 'Barbados', 'bb', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(20, 'Belarus', 'by', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(21, 'Belgium', 'be', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(22, 'Belize', 'bz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(23, 'Benin', 'bj', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(24, 'Bermuda', 'bm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(25, 'Bhutan', 'bt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(26, 'Bolivia', 'bo', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(27, 'Bosnia and Herzegovina', 'ba', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(28, 'Botswana', 'bw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(29, 'Brazil', 'br', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(30, 'British Indian Ocean Territory', 'io', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(31, 'British Virgin Islands', 'vg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(32, 'Brunei', 'bn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(33, 'Bulgaria', 'bg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(34, 'Burkina Faso', 'bf', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(35, 'Burundi', 'bi', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(36, 'Cambodia', 'kh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(37, 'Cameroon', 'cm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(38, 'Canada', 'ca', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(39, 'Cape Verde', 'cv', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(40, 'Cayman Islands', 'ky', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(41, 'Central African Republic', 'cf', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(42, 'Chad', 'td', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(43, 'Chile', 'cl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(44, 'China', 'cn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(45, 'Christmas Island', 'cx', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(46, 'Cocos Islands', 'cc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(47, 'Colombia', 'co', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(48, 'Comoros', 'km', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(49, 'Cook Islands', 'ck', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(50, 'Costa Rica', 'cr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(51, 'Croatia', 'hr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(52, 'Cuba', 'cu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(53, 'Curacao', 'cw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(54, 'Cyprus', 'cy', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(55, 'Czech Republic', 'cz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(56, 'Democratic Republic of the Congo', 'cd', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(57, 'Denmark', 'dk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(58, 'Djibouti', 'dj', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(59, 'Dominica', 'dm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(60, 'Dominican Republic', 'do', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(61, 'East Timor', 'tl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(62, 'Ecuador', 'ec', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(63, 'Egypt', 'eg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(64, 'El Salvador', 'sv', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(65, 'Equatorial Guinea', 'gq', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(66, 'Eritrea', 'er', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(67, 'Estonia', 'ee', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(68, 'Ethiopia', 'et', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(69, 'Falkland Islands', 'fk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(70, 'Faroe Islands', 'fo', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(71, 'Fiji', 'fj', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(72, 'Finland', 'fi', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(73, 'France', 'fr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(74, 'French Polynesia', 'pf', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(75, 'Gabon', 'ga', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(76, 'Gambia', 'gm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(77, 'Georgia', 'ge', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(78, 'Germany', 'de', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(79, 'Ghana', 'gh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(80, 'Gibraltar', 'gi', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(81, 'Greece', 'gr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(82, 'Greenland', 'gl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(83, 'Grenada', 'gd', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(84, 'Guam', 'gu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(85, 'Guatemala', 'gt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(86, 'Guernsey', 'gg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(87, 'Guinea', 'gn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(88, 'Guinea-Bissau', 'gw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(89, 'Guyana', 'gy', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(90, 'Haiti', 'ht', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(91, 'Honduras', 'hn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(92, 'Hong Kong', 'hk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(93, 'Hungary', 'hu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(94, 'Iceland', 'is', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(95, 'India', 'in', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(96, 'Indonesia', 'id', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(97, 'Iran', 'ir', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(98, 'Iraq', 'iq', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(99, 'Ireland', 'ie', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(100, 'Isle of Man', 'im', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(101, 'Israel', 'il', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(102, 'Italy', 'it', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(103, 'Ivory Coast', 'ci', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(104, 'Jamaica', 'jm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(105, 'Japan', 'jp', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(106, 'Jersey', 'je', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(107, 'Jordan', 'jo', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(108, 'Kazakhstan', 'kz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(109, 'Kenya', 'ke', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(110, 'Kiribati', 'ki', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(111, 'Kosovo', 'xk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(112, 'Kuwait', 'kw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(113, 'Kyrgyzstan', 'kg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(114, 'Laos', 'la', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(115, 'Latvia', 'lv', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(116, 'Lebanon', 'lb', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(117, 'Lesotho', 'ls', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(118, 'Liberia', 'lr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(119, 'Libya', 'ly', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(120, 'Liechtenstein', 'li', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(121, 'Lithuania', 'lt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(122, 'Luxembourg', 'lu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(123, 'Macau', 'mo', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(124, 'Macedonia', 'mk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(125, 'Madagascar', 'mg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(126, 'Malawi', 'mw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(127, 'Malaysia', 'my', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(128, 'Maldives', 'mv', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(129, 'Mali', 'ml', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(130, 'Malta', 'mt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(131, 'Marshall Islands', 'mh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(132, 'Mauritania', 'mr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(133, 'Mauritius', 'mu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(134, 'Mayotte', 'yt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(135, 'Mexico', 'mx', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(136, 'Micronesia', 'fm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(137, 'Moldova', 'md', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(138, 'Monaco', 'mc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(139, 'Mongolia', 'mn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(140, 'Montenegro', 'me', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(141, 'Montserrat', 'ms', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(142, 'Morocco', 'ma', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(143, 'Mozambique', 'mz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(144, 'Myanmar', 'mm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(145, 'Namibia', 'na', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(146, 'Nauru', 'nr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(147, 'Nepal', 'np', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(148, 'Netherlands', 'nl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(149, 'Netherlands Antilles', 'an', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(150, 'New Caledonia', 'nc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(151, 'New Zealand', 'nz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(152, 'Nicaragua', 'ni', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(153, 'Niger', 'ne', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(154, 'Nigeria', 'ng', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(155, 'Niue', 'nu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(156, 'North Korea', 'kp', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(157, 'Northern Mariana Islands', 'mp', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(158, 'Norway', 'no', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(159, 'Oman', 'om', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(160, 'Pakistan', 'pk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(161, 'Palau', 'pw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(162, 'Palestine', 'ps', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(163, 'Panama', 'pa', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(164, 'Papua New Guinea', 'pg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(165, 'Paraguay', 'py', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(166, 'Peru', 'pe', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(167, 'Philippines', 'ph', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(168, 'Pitcairn', 'pn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(169, 'Poland', 'pl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(170, 'Portugal', 'pt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(171, 'Puerto Rico', 'pr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(172, 'Qatar', 'qa', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(173, 'Republic of the Congo', 'cg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(174, 'Reunion', 're', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(175, 'Romania', 'ro', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(176, 'Russia', 'ru', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(177, 'Rwanda', 'rw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(178, 'Saint Barthelemy', 'bl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(179, 'Saint Helena', 'sh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(180, 'Saint Kitts and Nevis', 'kn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(181, 'Saint Lucia', 'lc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(182, 'Saint Martin', 'mf', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(183, 'Saint Pierre and Miquelon', 'pm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(184, 'Saint Vincent and the Grenadines', 'vc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(185, 'Samoa', 'ws', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(186, 'San Marino', 'sm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(187, 'Sao Tome and Principe', 'st', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(188, 'Saudi Arabia', 'sa', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(189, 'Senegal', 'sn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(190, 'Serbia', 'rs', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(191, 'Seychelles', 'sc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(192, 'Sierra Leone', 'sl', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(193, 'Singapore', 'sg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(194, 'Sint Maarten', 'sx', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(195, 'Slovakia', 'sk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(196, 'Slovenia', 'si', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(197, 'Solomon Islands', 'sb', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(198, 'Somalia', 'so', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(199, 'South Africa', 'za', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(200, 'South Korea', 'kr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(201, 'South Sudan', 'ss', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(202, 'Spain', 'es', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(203, 'Sri Lanka', 'lk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(204, 'Sudan', 'sd', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(205, 'Suriname', 'sr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(206, 'Svalbard and Jan Mayen', 'sj', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(207, 'Swaziland', 'sz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(208, 'Sweden', 'se', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(209, 'Switzerland', 'ch', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(210, 'Syria', 'sy', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(211, 'Taiwan', 'tw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(212, 'Tajikistan', 'tj', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(213, 'Tanzania', 'tz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(214, 'Thailand', 'th', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(215, 'Togo', 'tg', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(216, 'Tokelau', 'tk', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(217, 'Tonga', 'to', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(218, 'Trinidad and Tobago', 'tt', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(219, 'Tunisia', 'tn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(220, 'Turkey', 'tr', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(221, 'Turkmenistan', 'tm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(222, 'Turks and Caicos Islands', 'tc', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(223, 'Tuvalu', 'tv', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(224, 'U.S. Virgin Islands', 'vi', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(225, 'Uganda', 'ug', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(226, 'Ukraine', 'ua', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(227, 'United Arab Emirates', 'ae', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(228, 'United Kingdom', 'gb', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(229, 'United States', 'us', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(230, 'Uruguay', 'uy', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(231, 'Uzbekistan', 'uz', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(232, 'Vanuatu', 'vu', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(233, 'Vatican', 'va', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(234, 'Venezuela', 've', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(235, 'Vietnam', 'vn', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(236, 'Wallis and Futuna', 'wf', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(237, 'Western Sahara', 'eh', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(238, 'Yemen', 'ye', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(239, 'Zambia', 'zm', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL),
(240, 'Zimbabwe', 'zw', 1, 0, 0, '2021-07-31 19:09:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_cart`
--

CREATE TABLE `customer_cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `cart_details` text COLLATE utf8mb4_unicode_ci,
  `platform` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_device_tokens`
--

CREATE TABLE `customer_device_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_role_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: Android, 2: iOS',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : Active, 0 : Inactive, 2 : Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_login_logs`
--

CREATE TABLE `customer_login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `login_through` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_loyalty`
--

CREATE TABLE `customer_loyalty` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_verified` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `email_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `email_verify_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_1` text COLLATE utf8mb4_unicode_ci,
  `address_2` text COLLATE utf8mb4_unicode_ci,
  `address_3` text COLLATE utf8mb4_unicode_ci,
  `date_of_birth` date DEFAULT NULL,
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Male, 2: Female',
  `marital_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `anniversary_date` date DEFAULT NULL,
  `spouse_dob` date DEFAULT NULL,
  `city_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `pin_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_from` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `referral_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_by_customer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_app_installed` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `app_installed_date` date DEFAULT NULL,
  `app_installed_browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

CREATE TABLE `customer_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `delivery_boy_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `shipping_address_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `billing_address_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `delivery_date` date DEFAULT NULL,
  `net_amount` decimal(10,4) NOT NULL,
  `gross_amount` decimal(10,4) NOT NULL,
  `discounted_amount` decimal(14,4) DEFAULT NULL,
  `delivery_charge` decimal(10,4) NOT NULL,
  `payment_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_items` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `total_items_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `reject_cancel_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchased_from` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `is_coupon_applied` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `promo_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_basket_in_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `order_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled',
  `customer_invoice_url` text COLLATE utf8mb4_unicode_ci,
  `delivery_boy_invoice_url` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_details`
--

CREATE TABLE `customer_order_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `products_id` int(10) UNSIGNED NOT NULL,
  `product_units_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item_quantity` int(10) UNSIGNED NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `selling_price` decimal(14,4) NOT NULL,
  `special_price` decimal(14,4) DEFAULT NULL COMMENT 'This is the discounted price',
  `special_price_start_date` date DEFAULT NULL,
  `special_price_end_date` date DEFAULT NULL,
  `reject_cancel_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_basket` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `order_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_details_basket`
--

CREATE TABLE `customer_order_details_basket` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_details_id` int(10) UNSIGNED NOT NULL,
  `products_id` int(10) UNSIGNED NOT NULL,
  `product_units_id` int(10) UNSIGNED NOT NULL,
  `item_quantity` int(10) UNSIGNED NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `selling_price` decimal(14,4) NOT NULL,
  `special_price` decimal(14,4) DEFAULT NULL COMMENT 'This is the discounted price',
  `special_price_start_date` date DEFAULT NULL,
  `special_price_end_date` date DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_status_track`
--

CREATE TABLE `customer_order_status_track` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_details_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `order_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_otp`
--

CREATE TABLE `customer_otp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` mediumint(9) NOT NULL,
  `sms_delivered` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : Yes, 0 : No',
  `error_message` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'API returned error message.',
  `otp_used` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 : Yes, 0 : No',
  `platform_generated_on` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 : Android, 2 : iOS, 3 : WebPOS, 4 : Website',
  `otp_generated_for` int(11) NOT NULL DEFAULT '1' COMMENT '201 : Login, 202 : Registration',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_wishlist`
--

CREATE TABLE `customer_wishlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `products_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_basket` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2016_09_01_093327_create_user_device_tokens_table', 1),
(9, '2016_09_01_154549_create_sp_check_email_verified', 1),
(10, '2016_09_01_154549_create_sp_update_device_tokens', 1),
(11, '2016_10_22_133718_create_sp_verify_email', 1),
(12, '2016_11_07_110934_create_user_communication_messages_table', 1),
(13, '2016_11_10_060254_create_sp_get_user_communication_message_sa', 1),
(14, '2016_11_10_060310_create_sp_insert_user_communication_message_sa', 1),
(15, '2016_11_10_060317_create_sp_edit_user_communication_message_sa', 1),
(16, '2016_11_10_060323_create_sp_update_user_communication_message_sa', 1),
(17, '2016_11_11_104750_create_user_push_notifications', 1),
(18, '2016_11_14_142351_create_sp_get_notification_data', 1),
(19, '2016_11_14_142444_create_sp_get_email_notification_data', 1),
(20, '2016_11_14_142455_create_sp_get_push_notification_data', 1),
(21, '2016_11_14_142502_create_sp_get_sms_notification_data', 1),
(22, '2016_11_14_142518_create_sp_get_push_and_sms_notification_data', 1),
(23, '2016_11_14_165011_create_sp_get_user_push_notifications', 1),
(24, '2019_08_19_000000_create_failed_jobs_table', 1),
(25, '2020_06_04_000001_create_permissions_table', 1),
(26, '2020_06_04_000002_create_roles_table', 1),
(27, '2020_06_04_000006_create_trips_table', 1),
(28, '2020_06_04_000007_create_permission_role_pivot_table', 1),
(29, '2020_06_04_000008_create_cities_table', 1),
(30, '2020_06_04_000008_create_region_user_communication_messages_pivot_table', 1),
(31, '2020_06_04_000008_create_role_user_pivot_table', 1),
(32, '2020_06_04_000008_create_user_user_communication_messages_pivot_table', 1),
(33, '2020_06_04_000010_add_relationship_fields_to_trips_table', 1),
(34, '2020_10_03_193259_create_products_table', 1),
(35, '2020_10_05_094739_create_admins_table', 1),
(36, '2020_10_06_140834_create_brands_master_table', 1),
(37, '2020_10_06_142815_create_categories_master_table', 1),
(38, '2020_10_06_143224_create_states_table', 1),
(39, '2020_10_06_143539_create_countries_table', 1),
(40, '2020_10_06_143706_create_customer_login_logs_table', 1),
(41, '2020_10_06_144606_create_customer_cart_table', 1),
(42, '2020_10_06_145725_create_customer_loyalty_table', 1),
(43, '2020_10_06_154306_create_customer_orders_table', 1),
(44, '2020_10_06_155720_create_customer_order_details_table', 1),
(45, '2020_10_06_160301_create_customer_order_status_track_table', 1),
(46, '2020_10_06_160501_create_pin_codes_table', 1),
(47, '2020_10_06_162245_create_product_inventory_table', 1),
(48, '2020_10_06_162438_create_user_types_table', 1),
(49, '2020_10_06_162702_create_sms_templates_table', 1),
(50, '2020_10_06_162906_create_system_emails_table', 1),
(51, '2020_10_06_163748_create_unit_master_table', 1),
(52, '2020_11_02_000009_add_relationship_fields_to_cities_table', 1),
(53, '2020_11_02_000009_add_relationship_fields_to_pin_codes_table', 1),
(54, '2020_12_02_085608_create_customer_otp_table', 1),
(55, '2020_12_02_184448_create_sp_get_sms_templates', 1),
(56, '2020_12_05_194436_create_sp_search_pincode', 1),
(57, '2020_12_05_194436_create_sp_validate_otp', 1),
(58, '2021_01_24_005212_create_region_master_table', 1),
(59, '2021_01_24_005419_create_pin_code_region_table', 1),
(60, '2021_01_24_005429_create_basket_product_units_table', 1),
(61, '2021_01_24_005429_create_region_user_table', 1),
(62, '2021_02_05_193259_create_product_images_table', 1),
(63, '2021_02_16_193259_create_product_units_table', 1),
(64, '2021_02_16_193259_create_user_details_table', 1),
(65, '2021_02_21_162245_create_product_location_inventory_table', 1),
(66, '2021_02_27_142815_create_banners_table', 1),
(67, '2021_03_05_002023_create_user_address_table', 1),
(68, '2021_03_14_155720_create_customer_order_details_basket_table', 1),
(69, '2021_03_14_194436_create_sp_assign_delivery_boy_to_order', 1),
(70, '2021_03_14_194436_create_sp_cancel_order', 1),
(71, '2021_03_14_194436_create_sp_get_order_details', 1),
(72, '2021_03_14_194436_create_sp_get_order_list', 1),
(73, '2021_03_14_194436_create_sp_get_product_list', 1),
(74, '2021_03_14_194436_create_sp_get_user_type_region_data', 1),
(75, '2021_03_14_194436_create_sp_place_order_details', 1),
(76, '2021_03_14_194436_create_sp_validate_basket_product', 1),
(77, '2021_03_14_194436_create_sp_validate_product', 1),
(78, '2021_03_24_194436_create_sp_change_order_status', 1),
(79, '2021_03_24_194436_create_sp_get_order_list_for_delivery_boy', 1),
(80, '2021_04_10_194436_create_sp_store_device_token', 1),
(81, '2021_04_18_154306_create_promo_code_master_table', 1),
(82, '2021_04_18_154306_create_promo_codes_table', 1),
(83, '2021_04_18_194436_create_sp_get_promo_codes', 1),
(84, '2021_04_18_194436_create_sp_validate_promo_code', 1),
(85, '2021_05_31_194436_create_sp_check_delivery_boy_availability', 1),
(86, '2021_06_01_194436_create_sp_get_customer_for_birthday_wishes', 1),
(87, '2021_06_01_194436_create_sp_get_low_quantity_product', 1),
(88, '2021_06_06_194436_create_sp_get_order_delivery_day', 1),
(89, '2021_06_07_160301_create_customer_wishlist', 1),
(90, '2021_06_07_194436_create_sp_get_wishlist', 1),
(91, '2021_06_07_194436_create_sp_store_product_in_wishlist', 1),
(92, '2021_06_10_142815_create_purchase_form_table', 1),
(93, '2021_06_10_152807_create_push_notifications_templates_table', 1),
(94, '2021_06_15_142815_create_campaign_categories_master_table', 1),
(95, '2021_06_15_142815_create_campaign_master_table', 1),
(96, '2021_06_15_142815_create_promo_code_format_master_table', 1),
(97, '2021_06_23_154306_create_config_settings_table', 1),
(98, '2021_06_24_234130_create_sp_add_update_campaign_offer', 1),
(99, '2021_07_13_125728_create_user_login_logs_table', 1),
(100, '2021_07_15_154112_create_sp_store_user_login_logs', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'user_management_access', NULL, NULL, NULL),
(2, 'permission_create', NULL, NULL, NULL),
(3, 'permission_edit', NULL, NULL, NULL),
(4, 'permission_show', NULL, NULL, NULL),
(5, 'permission_delete', NULL, NULL, NULL),
(6, 'permission_access', NULL, NULL, NULL),
(7, 'role_create', NULL, NULL, NULL),
(8, 'role_edit', NULL, NULL, NULL),
(9, 'role_show', NULL, NULL, NULL),
(10, 'role_delete', NULL, NULL, NULL),
(11, 'role_access', NULL, NULL, NULL),
(12, 'user_create', NULL, NULL, NULL),
(13, 'user_edit', NULL, NULL, NULL),
(14, 'user_show', NULL, NULL, NULL),
(15, 'user_delete', NULL, NULL, NULL),
(16, 'user_access', NULL, NULL, NULL),
(17, 'country_create', NULL, NULL, NULL),
(18, 'country_edit', NULL, NULL, NULL),
(19, 'country_show', NULL, NULL, NULL),
(20, 'country_delete', NULL, NULL, NULL),
(21, 'country_access', NULL, NULL, NULL),
(22, 'city_create', NULL, NULL, NULL),
(23, 'city_edit', NULL, NULL, NULL),
(24, 'city_show', NULL, NULL, NULL),
(25, 'city_delete', NULL, NULL, NULL),
(26, 'city_access', NULL, NULL, NULL),
(27, 'trip_create', NULL, NULL, NULL),
(28, 'trip_edit', NULL, NULL, NULL),
(29, 'trip_show', NULL, NULL, NULL),
(30, 'trip_delete', NULL, NULL, NULL),
(31, 'trip_access', NULL, NULL, NULL),
(32, 'profile_password_edit', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32);

-- --------------------------------------------------------

--
-- Table structure for table `pin_codes`
--

CREATE TABLE `pin_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `pin_code` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `state_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `city_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pin_code_region`
--

CREATE TABLE `pin_code_region` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `pin_code_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `product_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `custom_text` text COLLATE utf8mb4_unicode_ci,
  `display_custom_text_or_date` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Custom Text, 0: Date',
  `images` text COLLATE utf8mb4_unicode_ci,
  `voucher_value` decimal(14,4) DEFAULT NULL,
  `selling_price` decimal(14,4) NOT NULL,
  `special_price` decimal(14,4) DEFAULT NULL COMMENT 'This is the discounted price',
  `special_price_start_date` date DEFAULT NULL,
  `special_price_end_date` date DEFAULT NULL,
  `opening_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `current_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `min_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity_perday_percust` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity_perday_allcust` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `notify_for_qty_below` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `stock_availability` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: In Stock, 0: Out of Stock',
  `show_in_search_results` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Yes, 0: No',
  `pay_for_product_in` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: COD, 0: Online',
  `is_basket` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `products_id` int(10) UNSIGNED NOT NULL,
  `image_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_inventory`
--

CREATE TABLE `product_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_units_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_location_inventory`
--

CREATE TABLE `product_location_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_units_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `current_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_units`
--

CREATE TABLE `product_units` (
  `id` int(10) UNSIGNED NOT NULL,
  `products_id` int(10) UNSIGNED NOT NULL,
  `unit_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `selling_price` decimal(14,4) NOT NULL,
  `special_price` decimal(14,4) DEFAULT NULL COMMENT 'This is the discounted price',
  `special_price_start_date` date DEFAULT NULL,
  `special_price_end_date` date DEFAULT NULL,
  `opening_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `min_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity_perday_percust` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `max_quantity_perday_allcust` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `notify_for_qty_below` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `promo_code_master_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `promo_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_code_used` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive, 2: Expired',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_code_format_master`
--

CREATE TABLE `promo_code_format_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `promo_code_master_id` int(10) UNSIGNED NOT NULL,
  `code_format` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0: None, 1: Numeric, 2: Alphanumeric',
  `code_prefix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_suffix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_length` int(10) UNSIGNED NOT NULL,
  `code_sample` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_code_master`
--

CREATE TABLE `promo_code_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `campaign_category_id` int(10) UNSIGNED NOT NULL,
  `campaign_master_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `platforms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma separated platform ids',
  `category_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma seperated ids',
  `sub_category_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_customer` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Open, 2: Custom (Customer List), 3: Gender, 4: Marital Status',
  `target_customer_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Ex. User ids',
  `target_product` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Product wise 0: None',
  `target_product_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Ex. Product ids',
  `reward_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Multiple, 2: Percentage, 3: Direct',
  `reward_type_x_value` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: 2X/3X, 2: 25%,50%, 3: 100 pts',
  `campaign_use` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Unlimited, 2: Limited',
  `campaign_use_value` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `referral_user_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0: None, 1: Referrer, 2: Refree',
  `code_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Generic, 2: Unique',
  `promo_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_expire_in_days` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `code_qty` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `priority` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_form`
--

CREATE TABLE `purchase_form` (
  `id` int(10) UNSIGNED NOT NULL,
  `supplier_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(14,4) NOT NULL,
  `order_date` date NOT NULL,
  `total_in_kg` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_units` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `push_notifications_templates`
--

CREATE TABLE `push_notifications_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deeplink` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region_master`
--

CREATE TABLE `region_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region_user`
--

CREATE TABLE `region_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region_user_communication_messages`
--

CREATE TABLE `region_user_communication_messages` (
  `user_communication_messages_id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', NULL, NULL, NULL),
(2, 'User', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sms_templates`
--

CREATE TABLE `sms_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_emails`
--

CREATE TABLE `system_emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_to` text COLLATE utf8mb4_unicode_ci,
  `email_cc` text COLLATE utf8mb4_unicode_ci,
  `email_bcc` text COLLATE utf8mb4_unicode_ci,
  `email_from` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text1` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `text2` text COLLATE utf8mb4_unicode_ci,
  `email_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(10) UNSIGNED NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `city_from_id` int(10) UNSIGNED NOT NULL,
  `city_to_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit_master`
--

CREATE TABLE `unit_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `cat_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_verified` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `mobile_number_verified_at` timestamp NULL DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `email_verified_at` datetime DEFAULT NULL,
  `email_verify_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_1` text COLLATE utf8mb4_unicode_ci,
  `address_2` text COLLATE utf8mb4_unicode_ci,
  `address_3` text COLLATE utf8mb4_unicode_ci,
  `date_of_birth` date DEFAULT NULL,
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Male, 2: Female',
  `marital_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `anniversary_date` date DEFAULT NULL,
  `spouse_dob` date DEFAULT NULL,
  `city_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `pin_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_from` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `referral_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referred_by_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_app_installed` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `app_installed_date` date DEFAULT NULL,
  `app_installed_browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_plain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `mobile_number`, `mobile_verified`, `mobile_number_verified_at`, `email`, `email_verified`, `email_verified_at`, `email_verify_key`, `address_1`, `address_2`, `address_3`, `date_of_birth`, `gender`, `marital_status`, `anniversary_date`, `spouse_dob`, `city_id`, `pin_code`, `registered_from`, `referral_code`, `referred_by_user_id`, `is_app_installed`, `app_installed_date`, `app_installed_browser`, `status`, `password`, `password_plain`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'Admin', '9999999999', 0, NULL, 'admin@admin.com', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, NULL, 1, NULL, 0, 0, NULL, NULL, 1, '$2y$10$mpESFRG3sVMKh0xoKda8NOzGw4AYi3Ld9aoFVGTlrYAIgeLHzC4li', 'password', NULL, 0, 0, '2021-07-31 19:09:25', '2021-07-31 19:14:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landmark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `state_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `is_primary` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_communication_messages`
--

CREATE TABLE `user_communication_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 : Message',
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `offer_id` int(10) UNSIGNED DEFAULT NULL,
  `region_type` int(10) UNSIGNED DEFAULT '1',
  `user_role` int(10) UNSIGNED DEFAULT '4',
  `user_type` int(10) UNSIGNED DEFAULT '1',
  `push_text` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deep_link_screen` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sms_text` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notify_users_by` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0000' COMMENT '1000: Email, 0100 : Push Notification, 0010 : SMS, 0001 : SMS and Notifications',
  `email_from_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_from_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_tags` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender_filter` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: Male, 1: Female, 2: Other',
  `min_points_filter` decimal(14,4) NOT NULL,
  `max_points_filter` decimal(14,4) NOT NULL,
  `upload_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: None, 1: Emails, 2: Mobile Numbers',
  `uploaded_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 : Yes, 0 : No',
  `test_email_address` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_mobile_number` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_send_time` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 : Active, 0 : Inactive',
  `processed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1: Yes, 0: No',
  `email_count` int(11) NOT NULL DEFAULT '0',
  `sms_count` int(11) NOT NULL DEFAULT '0',
  `push_notification_count` int(11) NOT NULL DEFAULT '0',
  `push_notification_received_count` int(11) NOT NULL DEFAULT '0',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_photo` text COLLATE utf8mb4_unicode_ci,
  `aadhar_card_photo` text COLLATE utf8mb4_unicode_ci,
  `pan_card_photo` text COLLATE utf8mb4_unicode_ci,
  `license_card_photo` text COLLATE utf8mb4_unicode_ci,
  `rc_book_photo` text COLLATE utf8mb4_unicode_ci,
  `bank_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhar_number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan_number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4' COMMENT '1:Two wheeler,2:Three wheeler,3:Four wheeler,4:Other',
  `vehicle_number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:New, 1:Submitted, 2: Approve, 3: Rejected',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_logs`
--

CREATE TABLE `user_login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `platform` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Android, 2: iOS',
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_login` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Login, 2: Logout',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_push_notifications`
--

CREATE TABLE `user_push_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `mobile_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `communication_msg_id` int(11) NOT NULL DEFAULT '0',
  `custom_data` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `push_text` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deep_link_screen` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` tinyint(4) NOT NULL DEFAULT '0',
  `notification_received` tinyint(4) NOT NULL DEFAULT '0',
  `received_at` timestamp NULL DEFAULT NULL,
  `tap_status` tinyint(4) NOT NULL DEFAULT '0',
  `inapp_tap_count` int(11) NOT NULL DEFAULT '0',
  `tap_at` timestamp NULL DEFAULT NULL,
  `last_tap_at` timestamp NULL DEFAULT NULL,
  `tap_from` tinyint(4) DEFAULT NULL COMMENT '0: NO 1: Android 2: iOS 3: website',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_user_communication_messages`
--

CREATE TABLE `user_user_communication_messages` (
  `user_communication_messages_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`),
  ADD KEY `admins_composite_index_1` (`id`,`users_id`),
  ADD KEY `admins_users_id_index` (`users_id`),
  ADD KEY `admins_email_index` (`email`),
  ADD KEY `admins_first_name_index` (`first_name`),
  ADD KEY `admins_last_name_index` (`last_name`),
  ADD KEY `admins_status_index` (`status`),
  ADD KEY `admins_user_type_id_index` (`user_type_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `banners_name_unique` (`name`),
  ADD KEY `banners_type_index` (`type`),
  ADD KEY `banners_display_order_index` (`display_order`),
  ADD KEY `banners_status_index` (`status`);

--
-- Indexes for table `basket_product_units`
--
ALTER TABLE `basket_product_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basket_product_units_basket_id_index` (`basket_id`),
  ADD KEY `basket_product_units_product_units_id_index` (`product_units_id`),
  ADD KEY `basket_product_units_quantity_index` (`quantity`),
  ADD KEY `basket_product_units_status_index` (`status`);

--
-- Indexes for table `brands_master`
--
ALTER TABLE `brands_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_master_brand_name_unique` (`brand_name`),
  ADD KEY `brands_master_cat_id_index` (`cat_id`),
  ADD KEY `brands_master_status_index` (`status`);

--
-- Indexes for table `campaign_categories_master`
--
ALTER TABLE `campaign_categories_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_categories_master_name_index` (`name`),
  ADD KEY `campaign_categories_master_status_index` (`status`);

--
-- Indexes for table `campaign_master`
--
ALTER TABLE `campaign_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_master_name_index` (`name`),
  ADD KEY `campaign_master_campaign_category_id_index` (`campaign_category_id`),
  ADD KEY `campaign_master_status_index` (`status`);

--
-- Indexes for table `categories_master`
--
ALTER TABLE `categories_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_master_cat_name_unique` (`cat_name`),
  ADD KEY `categories_master_cat_parent_id_index` (`cat_parent_id`),
  ADD KEY `categories_master_status_index` (`status`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cities_name_unique` (`name`),
  ADD KEY `cities_country_id_index` (`country_id`),
  ADD KEY `cities_state_id_index` (`state_id`),
  ADD KEY `cities_status_index` (`status`);

--
-- Indexes for table `config_settings`
--
ALTER TABLE `config_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_settings_name_unique` (`name`),
  ADD KEY `config_settings_value_index` (`value`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `countries_name_unique` (`name`),
  ADD KEY `countries_status_index` (`status`);

--
-- Indexes for table `customer_cart`
--
ALTER TABLE `customer_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_cart_customer_id_index` (`customer_id`),
  ADD KEY `customer_cart_platform_index` (`platform`);

--
-- Indexes for table `customer_device_tokens`
--
ALTER TABLE `customer_device_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_device_tokens_user_id_index` (`user_id`),
  ADD KEY `customer_device_tokens_user_role_id_index` (`user_role_id`),
  ADD KEY `customer_device_tokens_device_token_index` (`device_token`),
  ADD KEY `customer_device_tokens_device_type_index` (`device_type`),
  ADD KEY `customer_device_tokens_status_index` (`status`);

--
-- Indexes for table `customer_login_logs`
--
ALTER TABLE `customer_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_login_logs_customer_id_index` (`customer_id`),
  ADD KEY `customer_login_logs_login_through_index` (`login_through`);

--
-- Indexes for table `customer_loyalty`
--
ALTER TABLE `customer_loyalty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_loyalty_mobile_number_unique` (`mobile_number`),
  ADD UNIQUE KEY `customer_loyalty_email_verify_key_unique` (`email_verify_key`),
  ADD UNIQUE KEY `customer_loyalty_referral_code_unique` (`referral_code`),
  ADD KEY `customer_loyalty_first_name_index` (`first_name`),
  ADD KEY `customer_loyalty_mobile_verified_index` (`mobile_verified`),
  ADD KEY `customer_loyalty_email_verified_index` (`email_verified`),
  ADD KEY `customer_loyalty_city_id_index` (`city_id`),
  ADD KEY `customer_loyalty_pin_code_index` (`pin_code`),
  ADD KEY `customer_loyalty_registered_from_index` (`registered_from`),
  ADD KEY `customer_loyalty_referred_by_customer_id_index` (`referred_by_customer_id`),
  ADD KEY `customer_loyalty_is_app_installed_index` (`is_app_installed`),
  ADD KEY `customer_loyalty_status_index` (`status`);

--
-- Indexes for table `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_orders_customer_id_index` (`customer_id`),
  ADD KEY `customer_orders_delivery_boy_id_index` (`delivery_boy_id`),
  ADD KEY `customer_orders_shipping_address_id_index` (`shipping_address_id`),
  ADD KEY `customer_orders_billing_address_id_index` (`billing_address_id`),
  ADD KEY `customer_orders_delivery_date_index` (`delivery_date`),
  ADD KEY `customer_orders_payment_type_index` (`payment_type`),
  ADD KEY `customer_orders_razorpay_order_id_index` (`razorpay_order_id`),
  ADD KEY `customer_orders_razorpay_payment_id_index` (`razorpay_payment_id`),
  ADD KEY `customer_orders_razorpay_signature_index` (`razorpay_signature`),
  ADD KEY `customer_orders_total_items_index` (`total_items`),
  ADD KEY `customer_orders_total_items_quantity_index` (`total_items_quantity`),
  ADD KEY `customer_orders_reject_cancel_reason_index` (`reject_cancel_reason`),
  ADD KEY `customer_orders_purchased_from_index` (`purchased_from`),
  ADD KEY `customer_orders_is_coupon_applied_index` (`is_coupon_applied`),
  ADD KEY `customer_orders_is_basket_in_order_index` (`is_basket_in_order`),
  ADD KEY `customer_orders_order_status_index` (`order_status`);

--
-- Indexes for table `customer_order_details`
--
ALTER TABLE `customer_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_order_details_customer_id_index` (`customer_id`),
  ADD KEY `customer_order_details_order_id_index` (`order_id`),
  ADD KEY `customer_order_details_products_id_index` (`products_id`),
  ADD KEY `customer_order_details_product_units_id_index` (`product_units_id`),
  ADD KEY `customer_order_details_expiry_date_index` (`expiry_date`),
  ADD KEY `customer_order_details_special_price_start_date_index` (`special_price_start_date`),
  ADD KEY `customer_order_details_special_price_end_date_index` (`special_price_end_date`),
  ADD KEY `customer_order_details_reject_cancel_reason_index` (`reject_cancel_reason`),
  ADD KEY `customer_order_details_is_basket_index` (`is_basket`),
  ADD KEY `customer_order_details_order_status_index` (`order_status`);

--
-- Indexes for table `customer_order_details_basket`
--
ALTER TABLE `customer_order_details_basket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_order_details_basket_customer_id_index` (`customer_id`),
  ADD KEY `customer_order_details_basket_order_id_index` (`order_id`),
  ADD KEY `customer_order_details_basket_order_details_id_index` (`order_details_id`),
  ADD KEY `customer_order_details_basket_products_id_index` (`products_id`),
  ADD KEY `customer_order_details_basket_product_units_id_index` (`product_units_id`),
  ADD KEY `customer_order_details_basket_expiry_date_index` (`expiry_date`),
  ADD KEY `customer_order_details_basket_special_price_start_date_index` (`special_price_start_date`),
  ADD KEY `customer_order_details_basket_special_price_end_date_index` (`special_price_end_date`);

--
-- Indexes for table `customer_order_status_track`
--
ALTER TABLE `customer_order_status_track`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_order_status_track_order_details_id_index` (`order_details_id`),
  ADD KEY `customer_order_status_track_order_status_index` (`order_status`);

--
-- Indexes for table `customer_otp`
--
ALTER TABLE `customer_otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_otp_mobile_number_index` (`mobile_number`),
  ADD KEY `customer_otp_sms_delivered_index` (`sms_delivered`),
  ADD KEY `customer_otp_otp_used_index` (`otp_used`);

--
-- Indexes for table `customer_wishlist`
--
ALTER TABLE `customer_wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_wishlist_user_id_index` (`user_id`),
  ADD KEY `customer_wishlist_products_id_index` (`products_id`),
  ADD KEY `customer_wishlist_is_basket_index` (`is_basket`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD KEY `role_id_fk_1586949` (`role_id`),
  ADD KEY `permission_id_fk_1586949` (`permission_id`);

--
-- Indexes for table `pin_codes`
--
ALTER TABLE `pin_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pin_codes_pin_code_index` (`pin_code`),
  ADD KEY `pin_codes_country_id_index` (`country_id`),
  ADD KEY `pin_codes_state_id_index` (`state_id`),
  ADD KEY `pin_codes_city_id_index` (`city_id`),
  ADD KEY `pin_codes_status_index` (`status`);

--
-- Indexes for table `pin_code_region`
--
ALTER TABLE `pin_code_region`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pin_code_region_region_id_index` (`region_id`),
  ADD KEY `pin_code_region_pin_code_id_index` (`pin_code_id`),
  ADD KEY `pin_code_region_status_index` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_brand_id_index` (`brand_id`),
  ADD KEY `products_category_id_index` (`category_id`),
  ADD KEY `products_product_name_index` (`product_name`),
  ADD KEY `products_short_description_index` (`short_description`),
  ADD KEY `products_display_custom_text_or_date_index` (`display_custom_text_or_date`),
  ADD KEY `products_special_price_start_date_index` (`special_price_start_date`),
  ADD KEY `products_special_price_end_date_index` (`special_price_end_date`),
  ADD KEY `products_opening_quantity_index` (`opening_quantity`),
  ADD KEY `products_current_quantity_index` (`current_quantity`),
  ADD KEY `products_min_quantity_index` (`min_quantity`),
  ADD KEY `products_max_quantity_index` (`max_quantity`),
  ADD KEY `products_max_quantity_perday_percust_index` (`max_quantity_perday_percust`),
  ADD KEY `products_max_quantity_perday_allcust_index` (`max_quantity_perday_allcust`),
  ADD KEY `products_notify_for_qty_below_index` (`notify_for_qty_below`),
  ADD KEY `products_stock_availability_index` (`stock_availability`),
  ADD KEY `products_show_in_search_results_index` (`show_in_search_results`),
  ADD KEY `products_pay_for_product_in_index` (`pay_for_product_in`),
  ADD KEY `products_is_basket_index` (`is_basket`),
  ADD KEY `products_view_count_index` (`view_count`),
  ADD KEY `products_status_index` (`status`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_products_id_index` (`products_id`),
  ADD KEY `product_images_status_index` (`status`);

--
-- Indexes for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_inventory_product_units_id_index` (`product_units_id`),
  ADD KEY `product_inventory_location_id_index` (`location_id`);

--
-- Indexes for table `product_location_inventory`
--
ALTER TABLE `product_location_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_location_inventory_product_units_id_index` (`product_units_id`),
  ADD KEY `product_location_inventory_location_id_index` (`location_id`);

--
-- Indexes for table `product_units`
--
ALTER TABLE `product_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_units_products_id_index` (`products_id`),
  ADD KEY `product_units_unit_id_index` (`unit_id`),
  ADD KEY `product_units_special_price_start_date_index` (`special_price_start_date`),
  ADD KEY `product_units_special_price_end_date_index` (`special_price_end_date`),
  ADD KEY `product_units_opening_quantity_index` (`opening_quantity`),
  ADD KEY `product_units_min_quantity_index` (`min_quantity`),
  ADD KEY `product_units_max_quantity_index` (`max_quantity`),
  ADD KEY `product_units_max_quantity_perday_percust_index` (`max_quantity_perday_percust`),
  ADD KEY `product_units_max_quantity_perday_allcust_index` (`max_quantity_perday_allcust`),
  ADD KEY `product_units_notify_for_qty_below_index` (`notify_for_qty_below`),
  ADD KEY `product_units_status_index` (`status`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_codes_promo_code_master_id_index` (`promo_code_master_id`),
  ADD KEY `promo_codes_user_id_index` (`user_id`),
  ADD KEY `promo_codes_promo_code_index` (`promo_code`),
  ADD KEY `promo_codes_is_code_used_index` (`is_code_used`),
  ADD KEY `promo_codes_status_index` (`status`);

--
-- Indexes for table `promo_code_format_master`
--
ALTER TABLE `promo_code_format_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_code_format_master_promo_code_master_id_index` (`promo_code_master_id`),
  ADD KEY `promo_code_format_master_code_format_index` (`code_format`),
  ADD KEY `promo_code_format_master_code_prefix_index` (`code_prefix`),
  ADD KEY `promo_code_format_master_code_suffix_index` (`code_suffix`),
  ADD KEY `promo_code_format_master_code_length_index` (`code_length`),
  ADD KEY `promo_code_format_master_status_index` (`status`);

--
-- Indexes for table `promo_code_master`
--
ALTER TABLE `promo_code_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_code_master_campaign_category_id_index` (`campaign_category_id`),
  ADD KEY `promo_code_master_campaign_master_id_index` (`campaign_master_id`),
  ADD KEY `promo_code_master_title_index` (`title`),
  ADD KEY `promo_code_master_platforms_index` (`platforms`),
  ADD KEY `promo_code_master_category_ids_index` (`category_ids`),
  ADD KEY `promo_code_master_sub_category_ids_index` (`sub_category_ids`),
  ADD KEY `promo_code_master_target_customer_index` (`target_customer`),
  ADD KEY `promo_code_master_target_product_index` (`target_product`),
  ADD KEY `promo_code_master_reward_type_index` (`reward_type`),
  ADD KEY `promo_code_master_reward_type_x_value_index` (`reward_type_x_value`),
  ADD KEY `promo_code_master_campaign_use_index` (`campaign_use`),
  ADD KEY `promo_code_master_campaign_use_value_index` (`campaign_use_value`),
  ADD KEY `promo_code_master_referral_user_type_index` (`referral_user_type`),
  ADD KEY `promo_code_master_code_type_index` (`code_type`),
  ADD KEY `promo_code_master_promo_code_index` (`promo_code`),
  ADD KEY `promo_code_master_code_expire_in_days_index` (`code_expire_in_days`),
  ADD KEY `promo_code_master_code_qty_index` (`code_qty`),
  ADD KEY `promo_code_master_priority_index` (`priority`),
  ADD KEY `promo_code_master_status_index` (`status`);

--
-- Indexes for table `purchase_form`
--
ALTER TABLE `purchase_form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_form_supplier_name_index` (`supplier_name`),
  ADD KEY `purchase_form_product_name_index` (`product_name`),
  ADD KEY `purchase_form_unit_index` (`unit`),
  ADD KEY `purchase_form_category_index` (`category`),
  ADD KEY `purchase_form_order_date_index` (`order_date`),
  ADD KEY `purchase_form_total_in_kg_index` (`total_in_kg`),
  ADD KEY `purchase_form_total_units_index` (`total_units`);

--
-- Indexes for table `push_notifications_templates`
--
ALTER TABLE `push_notifications_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_notifications_templates_name_index` (`name`),
  ADD KEY `push_notifications_templates_title_index` (`title`),
  ADD KEY `push_notifications_templates_deeplink_index` (`deeplink`),
  ADD KEY `push_notifications_templates_status_index` (`status`);

--
-- Indexes for table `region_master`
--
ALTER TABLE `region_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_master_region_name_index` (`region_name`),
  ADD KEY `region_master_status_index` (`status`);

--
-- Indexes for table `region_user`
--
ALTER TABLE `region_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_user_region_id_index` (`region_id`),
  ADD KEY `region_user_user_id_index` (`user_id`),
  ADD KEY `region_user_status_index` (`status`);

--
-- Indexes for table `region_user_communication_messages`
--
ALTER TABLE `region_user_communication_messages`
  ADD KEY `user_communication_messages_id_fk_1586675743958` (`user_communication_messages_id`),
  ADD KEY `region_id_fk_158545689898958` (`region_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD KEY `user_id_fk_1586958` (`user_id`),
  ADD KEY `role_id_fk_1586958` (`role_id`);

--
-- Indexes for table `sms_templates`
--
ALTER TABLE `sms_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sms_templates_title_index` (`title`),
  ADD KEY `sms_templates_name_index` (`name`),
  ADD KEY `sms_templates_message_index` (`message`),
  ADD KEY `sms_templates_sender_id_index` (`sender_id`),
  ADD KEY `sms_templates_status_index` (`status`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `states_name_unique` (`name`),
  ADD KEY `states_country_id_index` (`country_id`),
  ADD KEY `states_status_index` (`status`);

--
-- Indexes for table `system_emails`
--
ALTER TABLE `system_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_emails_name_index` (`name`),
  ADD KEY `system_emails_description_index` (`description`),
  ADD KEY `system_emails_email_from_index` (`email_from`),
  ADD KEY `system_emails_subject_index` (`subject`),
  ADD KEY `system_emails_email_type_index` (`email_type`),
  ADD KEY `system_emails_tags_index` (`tags`),
  ADD KEY `system_emails_status_index` (`status`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_from_fk_1587040` (`city_from_id`),
  ADD KEY `city_to_fk_1587042` (`city_to_id`);

--
-- Indexes for table `unit_master`
--
ALTER TABLE `unit_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_master_cat_id_index` (`cat_id`),
  ADD KEY `unit_master_unit_index` (`unit`),
  ADD KEY `unit_master_status_index` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_mobile_number_unique` (`mobile_number`),
  ADD KEY `users_first_name_index` (`first_name`),
  ADD KEY `users_mobile_verified_index` (`mobile_verified`),
  ADD KEY `users_email_verified_index` (`email_verified`),
  ADD KEY `users_city_id_index` (`city_id`),
  ADD KEY `users_pin_code_index` (`pin_code`),
  ADD KEY `users_registered_from_index` (`registered_from`),
  ADD KEY `users_referred_by_user_id_index` (`referred_by_user_id`),
  ADD KEY `users_is_app_installed_index` (`is_app_installed`),
  ADD KEY `users_status_index` (`status`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_address_user_id_foreign` (`user_id`),
  ADD KEY `user_address_pin_code_index` (`pin_code`),
  ADD KEY `user_address_city_id_index` (`city_id`),
  ADD KEY `user_address_state_id_index` (`state_id`),
  ADD KEY `user_address_mobile_number_index` (`mobile_number`),
  ADD KEY `user_address_status_index` (`status`),
  ADD KEY `user_address_is_primary_index` (`is_primary`);

--
-- Indexes for table `user_communication_messages`
--
ALTER TABLE `user_communication_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_communication_messages_message_type_index` (`message_type`),
  ADD KEY `user_communication_messages_reference_id_index` (`reference_id`),
  ADD KEY `user_communication_messages_offer_id_index` (`offer_id`),
  ADD KEY `user_communication_messages_region_type_index` (`region_type`),
  ADD KEY `user_communication_messages_user_role_index` (`user_role`),
  ADD KEY `user_communication_messages_user_type_index` (`user_type`),
  ADD KEY `user_communication_messages_notify_users_by_index` (`notify_users_by`),
  ADD KEY `user_communication_messages_gender_filter_index` (`gender_filter`),
  ADD KEY `user_communication_messages_upload_type_index` (`upload_type`),
  ADD KEY `user_communication_messages_test_mode_index` (`test_mode`),
  ADD KEY `user_communication_messages_status_index` (`status`),
  ADD KEY `user_communication_messages_processed_index` (`processed`),
  ADD KEY `user_communication_messages_email_count_index` (`email_count`),
  ADD KEY `user_communication_messages_sms_count_index` (`sms_count`),
  ADD KEY `user_communication_messages_push_notification_count_index` (`push_notification_count`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_details_user_id_index` (`user_id`),
  ADD KEY `user_details_role_id_index` (`role_id`),
  ADD KEY `user_details_vehicle_type_index` (`vehicle_type`),
  ADD KEY `user_details_status_index` (`status`);

--
-- Indexes for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_login_logs_user_id_index` (`user_id`),
  ADD KEY `user_login_logs_platform_index` (`platform`),
  ADD KEY `user_login_logs_is_login_index` (`is_login`),
  ADD KEY `user_login_logs_status_index` (`status`);

--
-- Indexes for table `user_push_notifications`
--
ALTER TABLE `user_push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_push_notifications_user_id_index` (`user_id`),
  ADD KEY `user_push_notifications_mobile_number_index` (`mobile_number`),
  ADD KEY `user_push_notifications_communication_msg_id_index` (`communication_msg_id`),
  ADD KEY `user_push_notifications_notification_type_index` (`notification_type`),
  ADD KEY `user_push_notifications_notification_received_index` (`notification_received`),
  ADD KEY `user_push_notifications_received_at_index` (`received_at`),
  ADD KEY `user_push_notifications_tap_status_index` (`tap_status`),
  ADD KEY `user_push_notifications_inapp_tap_count_index` (`inapp_tap_count`),
  ADD KEY `user_push_notifications_tap_at_index` (`tap_at`),
  ADD KEY `user_push_notifications_last_tap_at_index` (`last_tap_at`),
  ADD KEY `user_push_notifications_tap_from_index` (`tap_from`),
  ADD KEY `user_push_notifications_created_at_index` (`created_at`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_types_name_index` (`name`),
  ADD KEY `user_types_status_index` (`status`);

--
-- Indexes for table `user_user_communication_messages`
--
ALTER TABLE `user_user_communication_messages`
  ADD KEY `user_communication_messages_id_fk_1584547677656958` (`user_communication_messages_id`),
  ADD KEY `user_id_fk_158695476756558` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `basket_product_units`
--
ALTER TABLE `basket_product_units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `brands_master`
--
ALTER TABLE `brands_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `campaign_categories_master`
--
ALTER TABLE `campaign_categories_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `campaign_master`
--
ALTER TABLE `campaign_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `categories_master`
--
ALTER TABLE `categories_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `config_settings`
--
ALTER TABLE `config_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;
--
-- AUTO_INCREMENT for table `customer_cart`
--
ALTER TABLE `customer_cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_device_tokens`
--
ALTER TABLE `customer_device_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_login_logs`
--
ALTER TABLE `customer_login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_loyalty`
--
ALTER TABLE `customer_loyalty`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_order_details`
--
ALTER TABLE `customer_order_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_order_details_basket`
--
ALTER TABLE `customer_order_details_basket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_order_status_track`
--
ALTER TABLE `customer_order_status_track`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_otp`
--
ALTER TABLE `customer_otp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_wishlist`
--
ALTER TABLE `customer_wishlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;
--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `pin_codes`
--
ALTER TABLE `pin_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pin_code_region`
--
ALTER TABLE `pin_code_region`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_inventory`
--
ALTER TABLE `product_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_location_inventory`
--
ALTER TABLE `product_location_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_units`
--
ALTER TABLE `product_units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promo_code_format_master`
--
ALTER TABLE `promo_code_format_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promo_code_master`
--
ALTER TABLE `promo_code_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `purchase_form`
--
ALTER TABLE `purchase_form`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `push_notifications_templates`
--
ALTER TABLE `push_notifications_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `region_master`
--
ALTER TABLE `region_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `region_user`
--
ALTER TABLE `region_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sms_templates`
--
ALTER TABLE `sms_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `system_emails`
--
ALTER TABLE `system_emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `unit_master`
--
ALTER TABLE `unit_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_communication_messages`
--
ALTER TABLE `user_communication_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_push_notifications`
--
ALTER TABLE `user_push_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `country_fk_1586974` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `state_fk_158676974` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_id_fk_1586949` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_id_fk_1586949` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pin_codes`
--
ALTER TABLE `pin_codes`
  ADD CONSTRAINT `city_fk_1586974` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `country_fk_15876756974` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  ADD CONSTRAINT `state_fk_1586767376974` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `region_user_communication_messages`
--
ALTER TABLE `region_user_communication_messages`
  ADD CONSTRAINT `region_id_fk_158545689898958` FOREIGN KEY (`region_id`) REFERENCES `region_master` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_communication_messages_id_fk_1586675743958` FOREIGN KEY (`user_communication_messages_id`) REFERENCES `user_communication_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_id_fk_1586958` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk_1586958` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `city_from_fk_1587040` FOREIGN KEY (`city_from_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `city_to_fk_1587042` FOREIGN KEY (`city_to_id`) REFERENCES `cities` (`id`);

--
-- Constraints for table `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `user_address_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_user_communication_messages`
--
ALTER TABLE `user_user_communication_messages`
  ADD CONSTRAINT `user_communication_messages_id_fk_1584547677656958` FOREIGN KEY (`user_communication_messages_id`) REFERENCES `user_communication_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk_158695476756558` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
