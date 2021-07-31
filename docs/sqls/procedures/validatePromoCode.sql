DELIMITER $$
DROP PROCEDURE IF EXISTS validatePromoCode$$
CREATE PROCEDURE validatePromoCode(IN inputVoucherData JSON)
validatePromoCode:BEGIN     
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
DELIMITER ;