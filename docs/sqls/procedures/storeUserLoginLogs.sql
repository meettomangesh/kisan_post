DELIMITER $$
DROP PROCEDURE IF EXISTS storeUserLoginLogs$$
CREATE PROCEDURE storeUserLoginLogs(IN inputData JSON)
storeUserLoginLogs:BEGIN
    
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
DELIMITER ;