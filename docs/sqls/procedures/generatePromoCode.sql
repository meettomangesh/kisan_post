DELIMITER $$
DROP PROCEDURE IF EXISTS generatePromoCode$$
CREATE PROCEDURE `generatePromoCode`(IN inputData JSON, OUT response JSON)
generatePromoCode:BEGIN

DECLARE promoCode,codePrefix,codeSuffix VARCHAR (20) DEFAULT NULL;
DECLARE userId,pcmID,codeLength,codeQty,quantity,couponLengthTemp INTEGER;
DECLARE codeFormat TINYINT(1) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SET response = JSON_OBJECT('status','FAILURE','message','Please provide valid data.','data',JSON_OBJECT(),'statusCode',520);
        LEAVE generatePromoCode;
    ELSE
        SET userId = JSON_EXTRACT(inputData,'$.user_id');
        SET pcmID = JSON_EXTRACT(inputData,'$.promo_code_master_id');
        SET codeQty = JSON_EXTRACT(inputData,'$.code_qty');

        SELECT code_format,code_prefix,code_suffix,code_length INTO codeFormat,codePrefix,codeSuffix,codeLength
        FROM promo_code_format_master WHERE promo_code_master_id = pcmID;

        IF (SELECT COUNT(id) FROM promo_codes WHERE promo_code_master_id = pcmID) < codeQty THEN
            SET couponLengthTemp = codeLength - IFNULL(LENGTH(codePrefix), 0) - IFNULL(LENGTH(codeSuffix), 0);
            SET quantity = 1;
            do_this:LOOP
                IF codeFormat = 1 THEN
                    -- SELECT CONCAT_WS('', IFNULL(codePrefix, ''), LPAD(FLOOR(RAND() * 10000000000), couponLengthTemp, '1'), IFNULL(codeSuffix, '')) INTO promoCode;
                    SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(FLOOR(RAND() * 10000000000), couponLengthTemp, '1'), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                ELSE
                    -- SELECT CONCAT_WS('', IFNULL(codePrefix, ''), LPAD(CONV(FLOOR(RAND()*POW(36,8)), 10, 36), couponLengthTemp, 0), IFNULL(codeSuffix, '')) INTO promoCode;
                    SELECT CONCAT_WS('', IFNULL(CAST(codePrefix AS CHAR CHARACTER SET utf8), ''), LPAD(CONV(FLOOR(RAND()*POW(36,8)), 10, 36), couponLengthTemp, 0), IFNULL(CAST(codeSuffix AS CHAR CHARACTER SET utf8), '')) INTO promoCode;
                END IF;

                IF (SELECT COUNT(id) FROM promo_codes WHERE promo_code = promoCode) = 0 THEN
                    SET quantity = quantity - 1;
                    IF quantity = 0 THEN
                        LEAVE do_this;
                    END IF;
                END IF;
            END LOOP do_this;

            SET response = JSON_OBJECT('status','SUCCESS','message','Promo code is generated successfully.','data',JSON_OBJECT('statusCode',200,'promo_code',promoCode),'statusCode',200);
            LEAVE generatePromoCode;
        ELSE
            SET response = JSON_OBJECT('status','FAILURE','message','Promo code is out of stock.','data',JSON_OBJECT(),'statusCode',104);
            LEAVE generatePromoCode;    
        END IF;
    END IF;
END$$
DELIMITER ;