DELIMITER $$
DROP PROCEDURE IF EXISTS checkAvailabilityOfCampaign$$
CREATE PROCEDURE `checkAvailabilityOfCampaign`(IN inputData JSON)
campaignPriority:BEGIN

DECLARE platform,rewardType,targetCustomer,codeType,referralUserType,campaignUse,rewardTypeYValueUnit,userGender TINYINT(1) DEFAULT 0;
DECLARE campaignUseValue TINYINT(3) DEFAULT 0;
DECLARE rewardTypeYValue DECIMAL(10,4) DEFAULT 0;
DECLARE promoCodeTitle VARCHAR(255);
DECLARE campaignCategoryId,campaignMasterId,userId,orderId,pcmID,genPromoCodeResStatusCode,codeExpireInDays,rewardTypeXValue,LogCnt,userDateOfBirth,userMonthOfBirth,userAnnivDate,userAnnivMonth,offset,categoryId,subCategoryId  INT(10) DEFAULT 0;
DECLARE status VARCHAR(10);
DECLARE response VARCHAR(200);
DECLARE expiryDate,dateToCheck,userCreatedAt,orderedDate DATE;
DECLARE logCount,targetCustomerCount TINYINT(3) DEFAULT 0;
DECLARE targetCustomerValue VARCHAR(1000) DEFAULT 0;
DECLARE reponseCode INT(3) DEFAULT 0;
DECLARE promoCode VARCHAR(10) DEFAULT '';
DECLARE codeQty INT(10) DEFAULT 1;
DECLARE generatePromoCodeRequest,generatePromoCodeResponse,generatePromoCodeData JSON;

IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','statusCode',520) AS response;
    LEAVE campaignPriority;
ELSE
    SET campaignCategoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.campaign_category_id'));
    SET campaignMasterId = IFNULL(JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.campaign_master_id')), 0);
    SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
    SET orderId = IFNULL(JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.order_id')), 0);
    SET orderedDate = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.ordered_date'));
    SET platform = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.platform'));
    SET referralUserType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.referral_user_type'));
    SET categoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.category_id'));
    SET subCategoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sub_category_id'));

    /* IF orderId > 0 THEN
        SET dateToCheck = orderedDate;
    ELSE
        SET dateToCheck = CURDATE();
    END IF; */
    SET dateToCheck = CURDATE();

    SELECT gender,created_at,MONTH(date_of_birth),DAY(date_of_birth),MONTH(anniversary_date),DAY(anniversary_date)
    INTO userGender,userCreatedAt,userMonthOfBirth,userDateOfBirth,userAnnivMonth,userAnnivDate
    FROM users WHERE id = userId AND users.status IN (1,2);

	SELECT pcm.id,pcm.title,pcm.target_customer,pcm.target_customer_value,pcm.code_type,pcm.promo_code,pcm.code_expire_in_days,pcm.code_qty
    INTO
    pcmID,promoCodeTitle,targetCustomer,targetCustomerValue,codeType,promoCode,codeExpireInDays,codeQty
    FROM promo_code_master AS pcm
    JOIN campaign_master AS cm ON cm.id = pcm.campaign_master_id
    WHERE pcm.campaign_category_id = campaignCategoryId AND pcm.status = 1 AND pcm.start_date <= dateToCheck AND pcm.end_date >= dateToCheck
    AND IF(pcm.platforms IS NOT NULL, FIND_IN_SET(platform, pcm.platforms), 1=1) AND IF(pcm.category_ids IS NOT NULL, FIND_IN_SET(categoryId, pcm.category_ids), 1=1) AND IF(pcm.sub_category_ids IS NOT NULL, FIND_IN_SET(subCategoryId, pcm.sub_category_ids), 1=1)
    AND pcm.referral_user_type = referralUserType AND IF(pcm.campaign_category_id = 3, pcm.campaign_master_id = campaignMasterId, 1 = 1)
    ORDER BY pcm.priority LIMIT 1 OFFSET offset;

	IF pcmID != 0  THEN
        -- Promo code quantity check
        IF (SELECT COUNT(id) FROM promo_codes WHERE promo_code_master_id = pcmID) >= codeQty THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Promo code is out of stock.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',102) AS response;
            LEAVE campaignPriority;
        END IF;

		IF targetCustomer = 2 OR targetCustomer = 3 THEN
			IF targetCustomer = 2 THEN
                SELECT COUNT(*) INTO targetCustomerCount FROM `promo_code_master` WHERE (target_customer_value LIKE userId OR target_customer_value LIKE CONCAT('%',userId,'%') OR target_customer_value LIKE CONCAT(userId,'%') OR target_customer_value LIKE CONCAT('%',userId) ) AND id = pcmID;
				IF targetCustomerCount = 0 THEN
					SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'User id is not present in list.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',102) AS response;
                    LEAVE campaignPriority;
				END IF;
			ELSEIF targetCustomer = 3 THEN
                IF userGender != targetCustomerValue THEN
					SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'No campaign for this user gender.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',102) AS response;
					LEAVE campaignPriority;
				END IF;
			END IF;
		END IF;

		IF campaignUse = 2 THEN
			SELECT COUNT(*) INTO logCount FROM promo_codes WHERE promo_code_master_id = pcmID AND user_id = customerId;

			IF logCount >= (campaignUseValue * 1) THEN 
				SELECT JSON_OBJECT('status', 'FAILURE', 'message', CONCAT('Campaign use limit is ', campaignUseValue, ' times and already applied for this user.'),'data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',103) AS response;
				LEAVE campaignPriority;
			END IF;
		END IF;

		/* IF (rewardTypeYValueUnit = 2 AND rewardYValue < rewardTypeYValue) OR (rewardTypeYValueUnit = 3 AND points < rewardTypeYValue) THEN
			SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Value is less.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',104) AS response;
			LEAVE campaignPriority;
		END IF; */
		
        IF codeType = 2 THEN
            SET generatePromoCodeRequest = JSON_OBJECT('user_id',userId,'promo_code_master_id',pcmID,'code_qty',codeQty);
            CALL generatePromoCode(generatePromoCodeRequest, generatePromoCodeResponse);
            SET genPromoCodeResStatusCode = JSON_EXTRACT(generatePromoCodeResponse,'$.statusCode');

            -- IF coupon is not present
            IF genPromoCodeResStatusCode != 200 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Promo code is out of stock.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',101) AS response;
                LEAVE campaignPriority;
            END IF;

            SET generatePromoCodeData = JSON_EXTRACT(generatePromoCodeResponse,'$.data');
            SET promoCode = JSON_UNQUOTE(JSON_EXTRACT(generatePromoCodeData,'$.promo_code'));
        END IF;
        
        SET expiryDate = DATE_ADD(CURDATE(), INTERVAL codeExpireInDays DAY);
        -- Assign promo code to user
        INSERT INTO promo_codes (promo_code_master_id,user_id,promo_code,start_date,end_date,created_by)
        VALUES (pcmID,userId,promoCode,CURDATE(),expiryDate,1);

        IF LAST_INSERT_ID() > 0 THEN
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Campaign applied successfully for this user.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',200) AS response;
            LEAVE campaignPriority;
        ELSE
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Promo code is not assigned.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',105) AS response;
		    LEAVE campaignPriority;
        END IF;

	ELSE
		SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Campaign not present.','data',JSON_OBJECT('promo_code_title',promoCodeTitle),'statusCode',101) AS response;
		LEAVE campaignPriority;
	END IF;
END IF;
END$$
DELIMITER ;