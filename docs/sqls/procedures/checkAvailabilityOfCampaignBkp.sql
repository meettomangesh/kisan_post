DELIMITER $$
DROP PROCEDURE IF EXISTS checkAvailabilityOfCampaign$$
CREATE PROCEDURE `checkAvailabilityOfCampaign`(IN `customerId` INT(10), IN `merchantId` INT(10), IN `loyaltyId` INT(10), IN `locationId` INT(10), IN `platform` TINYINT(1), IN `campaignCategoryId` INT(10), IN `points` DECIMAL(10,4), IN `rewardYValue` DECIMAL(10,4), IN `categoryId` INT(10), IN `brandId` INT(10), IN `referenceId` INT(10), IN `billDate` DATE, IN `offset` INT(4), IN `referenceIdType` TINYINT(1), IN `campId` INT(10), IN `referralUserType` TINYINT(1))
campaignPriority:BEGIN

DECLARE rewardType,targetCustomer,type,campaignUse,rewardTypeYValueUnit,custGender,transUse,transUseType TINYINT(1) DEFAULT 0;
DECLARE campaignUseValue TINYINT(2) DEFAULT 0;
DECLARE bonusPoints,rewardTypeYValue,sumBillAmount,sumBillPointsEarned,sumCampPointsEarned DECIMAL(10,4) DEFAULT 0;
DECLARE campaignTitle VARCHAR(255);
DECLARE merchantCampaignId,logInsertedId,currentTierId,campaignId,brand_ID,category_ID,location_ID,rewardTypeXValue,merchantCouponsId,merchantCouponValuesId,customerIdInLog,LogCnt,custDateOfBirth, custMonthOfBirth,custAnnivDate, custAnnivMonth  INT(10) DEFAULT 0;
DECLARE status VARCHAR(10);
DECLARE response VARCHAR(200);
DECLARE expiryDate, dateToCheck, custCreatedAt, customerLoyaltyDOB DATE;
DECLARE logCount,targetCustomerCount TINYINT(3) DEFAULT 0;
DECLARE targetCustomerValue,billIds VARCHAR(1000) DEFAULT 0;
DECLARE rewardTypeExpireValue VARCHAR(10) DEFAULT 0;
DECLARE reponseCode INT(3) DEFAULT 0;
DECLARE allowedPlatform VARCHAR(4) DEFAULT '';
DECLARE couponCode VARCHAR(10) DEFAULT '';
DECLARE couponQty INT(10) DEFAULT 1;
DECLARE submissionDate DATETIME DEFAULT NULL;

    IF referenceId != 0 AND campaignCategoryId = 1 AND referenceIdType = 1 THEN
		SET dateToCheck = billDate;
		SELECT created_at INTO submissionDate FROM customer_bills WHERE id = referenceId AND customer_id = customerId AND merchant_id = merchantId;
	ELSEIF referenceId != 0 AND campaignCategoryId = 3 AND campId = 15 THEN
		SELECT bill_date,bill_amount INTO dateToCheck,rewardYValue FROM customer_bills WHERE id = referenceId AND merchant_id = merchantId;
	ELSE
		SET dateToCheck = CURDATE();
	END IF;

    SELECT current_tier_id,gender,created_at,month(date_of_birth),day(date_of_birth),month(anniversary_date),day(anniversary_date) INTO currentTierId,custGender,custCreatedAt,custMonthOfBirth,custDateOfBirth,custAnnivMonth,custAnnivDate
    FROM customer_loyalty WHERE customer_id = customerId AND merchant_id = merchantId AND customer_loyalty.status in (1,2);

	SELECT
	mcamp.id, mcamp.campaign_id, mcamp.campaign_title, mcamp.campaign_use, mcamp.campaign_use_value, mcamp.target_customer, mcamp.target_customer_value, mcamp.type, mcamp.allowed_platform, mcamp.transaction_use, mcamp.transaction_use_type,
	mcrv.location_id, mcrv.category_id, mcrv.brand_id, mcrv.reward_type, mcrv.reward_type_x_value, mcrv.reward_type_y_value, mcrv.reward_type_y_value_unit, mcrv.reward_type_expire_value

	INTO

	merchantCampaignId, campaignId,	campaignTitle, campaignUse, campaignUseValue, targetCustomer, targetCustomerValue, type, allowedPlatform,transUse,transUseType,
	location_ID, category_ID, brand_ID, rewardType, rewardTypeXValue, rewardTypeYValue, rewardTypeYValueUnit, rewardTypeExpireValue
	FROM merchant_campaigns AS mcamp
	JOIN merchant_campaign_reward_values AS mcrv ON mcamp.id = mcrv.merchant_campaigns_id AND find_in_set(mcrv.brand_id, mcamp.brand_id) AND find_in_set(mcrv.category_id, mcamp.category_id) AND find_in_set(mcrv.location_id, mcamp.location_id)
	WHERE mcamp.campaign_category_id = campaignCategoryId AND mcamp.merchant_id = merchantId AND mcamp.loyalty_id = loyaltyId AND mcamp.status = 1 AND mcamp.campaign_start_date <= dateToCheck AND mcamp.campaign_end_date >= dateToCheck AND mcrv.platform = platform AND (mcrv.category_id = 0 OR mcrv.category_id = categoryId) AND (mcrv.brand_id = 0 OR mcrv.brand_id = brandId) AND (mcrv.location_id = 0 OR mcrv.location_id = locationId) AND mcrv.referral_user_type = referralUserType AND IF(mcamp.campaign_category_id = 3, mcamp.campaign_id = campId, 1 = 1) AND IF(mcamp.campaign_category_id = 6, IF(mcamp.campaign_start_date <= custCreatedAt, 1=1, false),1=1)
	AND IF(mcamp.happy_hrs = 0, 1=1, IF(mcamp.happy_hrs = 1 AND billDate = DATE(submissionDate) AND (mcamp.happy_hrs_start_time <= TIME(submissionDate) AND mcamp.happy_hrs_end_time >= TIME(submissionDate)), 1=1, false))
	-- AND IF(mcamp.is_day_of_week = 0, 1=1, IF((DAYOFWEEK(dateToCheck) IN (mcamp.day_of_weeks)) = 1, true, false))
	AND IF(mcamp.is_day_of_week = 0, 1=1, IF((FIND_IN_SET(DAYOFWEEK(dateToCheck), mcamp.day_of_weeks)) >= 1, true, false))
	ORDER BY mcamp.priority LIMIT 1 OFFSET offset;

	IF merchantCampaignId != 0  THEN

		IF campaignId = 16 OR campaignId = 17 THEN
            SELECT count(ccl.id) INTO customerIdInLog
            FROM merchant_campaigns AS mcamp
			JOIN customer_loyalty cl ON cl.merchant_id = mcamp.merchant_id AND cl.loyalty_id = mcamp.loyalty_id
            LEFT JOIN customer_campaign_log ccl ON  cl.customer_id =  ccl.customer_id AND mcamp.id = ccl.merchant_campaigns_id
            WHERE mcamp.campaign_id = campaignId
            AND cl.status = 1 AND cl.customer_id = customerId AND date(ccl.created_at) < CURDATE()
            AND date(ccl.created_at) > DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY),INTERVAL 365 DAY);

            IF customerIdInLog > 0 THEN
                SELECT 'Error' AS status, 'Customer not eligible for birthday campaign.' AS response, campaignTitle, '102' AS reponseCode;	
                LEAVE campaignPriority;
            ELSE
                IF campaignUse = 2 THEN
                    IF campaignId = 16 THEN
                        IF ((custDateOfBirth != day(CURDATE())) OR (custMonthOfBirth != month(CURDATE()))) THEN
                                SELECT 'Error' AS status, 'Customer not eligible for birthday campaign.' AS response, campaignTitle, '102' AS reponseCode;	
                            LEAVE campaignPriority;
                        END IF;
                    END IF;
                    IF campaignId = 17 THEN
                        IF ((custAnnivDate != day(CURDATE())) OR (custAnnivMonth != month(CURDATE()))) THEN
                                SELECT 'Error' AS status, 'Customer not eligible for anniversary campaign.' AS response, campaignTitle, '102' AS reponseCode;	
                            LEAVE campaignPriority;
                        END IF;

                    END IF;
                ELSE

                    IF campaignId = 16 THEN
                        SELECT count(ccl.id) INTO customerIdInLog
                        FROM merchant_campaigns AS mcamp
                        JOIN customer_loyalty cl ON cl.merchant_id = mcamp.merchant_id AND cl.loyalty_id = mcamp.loyalty_id
                        LEFT JOIN customer_campaign_log ccl ON  cl.customer_id =  ccl.customer_id AND mcamp.id = ccl.merchant_campaigns_id
                        WHERE mcamp.campaign_id = campaignId AND cl.status = 1 AND cl.customer_id = customerId AND month(cl.date_of_birth) = month(CURDATE()) AND day(cl.date_of_birth) = day(CURDATE()) AND date(ccl.created_at) = CURDATE();
                    END IF;
                    IF campaignId = 17 THEN
                        SELECT count(ccl.id) INTO customerIdInLog
                        FROM merchant_campaigns AS mcamp
                        JOIN customer_loyalty cl ON cl.merchant_id = mcamp.merchant_id AND cl.loyalty_id = mcamp.loyalty_id
                        LEFT JOIN customer_campaign_log ccl ON  cl.customer_id =  ccl.customer_id AND mcamp.id = ccl.merchant_campaigns_id
                        WHERE mcamp.campaign_id = campaignId AND cl.status = 1 AND cl.customer_id = customerId AND month(cl.anniversary_date) = month(CURDATE()) AND day(cl.anniversary_date) = day(CURDATE()) AND date(ccl.created_at) = CURDATE();
                    END IF;

                    IF (campaignUse = 1 AND customerIdInLog > campaignUseValue) OR (campaignUse = 3 AND customerIdInLog > 1) THEN
                        SELECT 'Error' AS status, 'Customer exceeded transactions for birthday campaign.' AS response, campaignTitle, '102' AS reponseCode;
                        LEAVE campaignPriority;
                    END IF;
                END IF;
            END IF;
		END IF;

		IF targetCustomer = 2 OR targetCustomer = 3 OR targetCustomer = 4 THEN

			IF targetCustomer = 2 THEN

				SELECT COUNT(*) INTO targetCustomerCount FROM `merchant_campaigns` WHERE ( target_customer_value LIKE currentTierId OR target_customer_value LIKE CONCAT('%',currentTierId,'%') OR target_customer_value LIKE CONCAT(currentTierId,'%') OR target_customer_value LIKE CONCAT('%',currentTierId) ) AND id = merchantCampaignId;
				IF targetCustomerCount = 0 THEN
					SELECT 'Error' AS status, 'Customer tier is not matching.' AS response, campaignTitle, bonusPoints, '102' AS reponseCode;
					LEAVE campaignPriority;
				END IF;

			ELSEIF targetCustomer = 3 THEN

				SELECT COUNT(*) INTO targetCustomerCount FROM `merchant_campaigns` WHERE ( target_customer_value LIKE customerId OR target_customer_value LIKE CONCAT('%',customerId,'%') OR target_customer_value LIKE CONCAT(customerId,'%') OR target_customer_value LIKE CONCAT('%',customerId) ) AND id = merchantCampaignId;
				IF targetCustomerCount = 0 THEN
					SELECT 'Error' AS status, 'Customer id is not present in list.' AS response, campaignTitle, bonusPoints, '102' AS reponseCode;
					LEAVE campaignPriority;
				END IF;

			ELSEIF targetCustomer = 4 THEN

				IF custGender != targetCustomerValue THEN
					SELECT 'Error' AS status, 'No campaign for this customer gender.' AS response, campaignTitle, bonusPoints, '102' AS reponseCode;
					LEAVE campaignPriority;
				END IF;

			END IF;
		END IF;

		IF campaignUse = 1 OR campaignUse = 3 THEN
			SELECT COUNT(*) INTO logCount FROM customer_campaign_log WHERE merchant_campaigns_id = merchantCampaignId AND customer_id = customerId AND location_id = locationId AND transaction_log_type = 1;

			IF campaignUse = 3 AND logCount >= 1 THEN
				SELECT 'Error' AS status, 'Campaign use limit is only one time and already applied for this customer.' AS response, campaignTitle, bonusPoints, '103' AS reponseCode;
				LEAVE campaignPriority;
			ELSEIF (rewardTypeYValueUnit = 1 AND logCount >= (campaignUseValue * rewardTypeYValue)) OR (rewardTypeYValueUnit != 1 AND logCount >= (campaignUseValue * 1)) THEN 
				SELECT 'Error' AS status, CONCAT('Campaign use limit is ', campaignUseValue, ' times and already applied for this customer.') AS response, campaignTitle, bonusPoints, '103' AS reponseCode;
				LEAVE campaignPriority;
			END IF;

		END IF;

		IF (rewardTypeYValueUnit = 2 AND rewardYValue < rewardTypeYValue) OR (rewardTypeYValueUnit = 3 AND points < rewardTypeYValue) THEN
			SELECT 'Error' AS status, 'Value is less.' AS response, campaignTitle, bonusPoints, '104' AS reponseCode;
			LEAVE campaignPriority;
		END IF;

        IF (rewardTypeYValueUnit = 4) THEN
            SELECT IFNULL(SUM(cb.bill_amount), 0), GROUP_CONCAT(cb.id) INTO sumBillAmount, billIds FROM customer_bills AS cb WHERE cb.customer_id = customerId AND cb.merchant_id = merchantId AND cb.loyalty_id = loyaltyId AND cb.bill_date = billDate AND cb.status = 2;
            IF sumBillAmount < rewardTypeYValue THEN
                SELECT 'Error' AS status, 'Value is less.' AS response, campaignTitle, bonusPoints, '104' AS reponseCode;
			    LEAVE campaignPriority;
    		ELSE
				SELECT IFNULL(SUM(points_earned), 0) INTO sumBillPointsEarned FROM customer_points_earned_redeemed WHERE customer_id = customerId AND merchant_id = merchantId AND loyalty_id = loyaltyId AND earned_redeemed_flag = 1 AND earn_reason_id IN (1,3) AND FIND_IN_SET(reference_id, billIds);
                SELECT IFNULL(SUM(points_earned), 0) INTO sumCampPointsEarned FROM customer_campaign_log WHERE merchant_campaigns_id = merchantCampaignId AND customer_id = customerId AND FIND_IN_SET(reference_id, billIds) AND reference_id_type = 1 AND transaction_log_type = 1;
				SET points = sumBillPointsEarned - sumCampPointsEarned;
				IF points <= 0 THEN
					SELECT 'Error' AS status, 'Incorrect points.' AS response, campaignTitle, bonusPoints, '104' AS reponseCode;
			    	LEAVE campaignPriority;
				END IF;
            END IF;
		END IF;

		IF (rewardTypeYValueUnit = 5) THEN
			SELECT GROUP_CONCAT(cb.id) INTO billIds FROM customer_bills AS cb WHERE cb.customer_id = customerId AND cb.merchant_id = merchantId AND cb.loyalty_id = loyaltyId AND cb.bill_date = billDate AND cb.status = 2;
			SELECT IFNULL(SUM(points_earned), 0) INTO sumCampPointsEarned FROM customer_campaign_log WHERE merchant_campaigns_id = merchantCampaignId AND customer_id = customerId AND FIND_IN_SET(reference_id, billIds) AND reference_id_type = 1 AND transaction_log_type = 1;
			IF sumCampPointsEarned >= rewardTypeYValue THEN
                SELECT 'Error' AS status, 'Already reached.' AS response, campaignTitle, bonusPoints, '104' AS reponseCode;
			    LEAVE campaignPriority;
            END IF;
		END IF;

		IF rewardType = 1 AND type = 1 THEN
			SET bonusPoints = points * rewardTypeXValue;
			SET bonusPoints = bonusPoints - points;
			IF rewardTypeYValueUnit = 5 AND (sumCampPointsEarned + bonusPoints) > rewardTypeYValue THEN
				SET bonusPoints = bonusPoints - ((sumCampPointsEarned + bonusPoints) - rewardTypeYValue);
			END IF;
		ELSEIF rewardType = 2 AND type = 1 THEN
			SET bonusPoints = (points * rewardTypeXValue) / 100;
		ELSEIF rewardType = 3 AND type = 1 THEN
			SET bonusPoints = rewardTypeXValue;
		END IF;

		IF campaignId = 11 OR campaignId = 12 THEN
			SET expiryDate = DATE_ADD(CURDATE(),INTERVAL rewardTypeYValue + rewardTypeExpireValue - 1 DAY);
		ELSE
			SET expiryDate = DATE_ADD(CURDATE(),INTERVAL rewardTypeExpireValue - 1 DAY);
		END IF;

		IF type = 1 THEN

			CALL addBonusPoints(customerId, merchantId, loyaltyId, bonusPoints, expiryDate, campaignTitle, 1, @resp);

			IF @resp > 0 THEN
				INSERT INTO customer_campaign_log (
					merchant_campaigns_id, customer_points_earned_redeemed_id, customer_id, location_id, category_id, brand_id, reference_id, reference_id_type, referral_user_type, platform, points_earned, created_by, created_at
				) VALUES (
				    merchantCampaignId, @resp, customerId, location_ID, category_ID, brand_ID, referenceId, referenceIdType, referralUserType, platform, bonusPoints, 1, CURRENT_TIMESTAMP()
				);

				SET logInsertedId = LAST_INSERT_ID();

				IF logInsertedId > 0 THEN

					IF referenceId != 0 AND referenceIdType = 1 THEN
						UPDATE customer_points_earned_redeemed SET reference_id = referenceId WHERE id = @resp;

						UPDATE customer_bills SET points_earned = points_earned + bonusPoints WHERE id = referenceId;
					END IF;

					COMMIT;
					SELECT 'Success' AS status, 'Campaign applied successfully for this customer.' AS response, campaignTitle, bonusPoints, '200' AS reponseCode;
					LEAVE campaignPriority;
				END IF;
			ELSE
				ROLLBACK;
				SELECT 'Error' AS status, 'Log not inserted.' AS response, campaignTitle, bonusPoints, '105' AS reponseCode;
				LEAVE campaignPriority;
			END IF;

		ELSE

			CALL generateCouponCode(customerId, merchantId, loyaltyId, transUse, transUseType, @couponCodeResponse);
			SET couponCode = @couponCodeResponse;

			INSERT INTO merchant_coupons (
				merchant_campaigns_id, customer_id, merchant_id, loyalty_id, location_id, category_id, brand_id, coupon_code, coupon_qty, allowed_platform, target_customer, target_customer_value, type, transaction_use, transaction_use_type, coupon_start_date, coupon_end_date, coupon_usage, coupon_usage_value, created_by, created_at
			) VALUES (
			    merchantCampaignId, customerId, merchantId, loyaltyId, location_ID, category_ID, brand_ID, couponCode, couponQty, allowedPlatform, targetCustomer, targetCustomerValue, type, transUse, transUseType, CURDATE(), DATE_ADD(CURDATE(),INTERVAL rewardTypeYValue - 1 DAY), campaignUse, campaignUseValue, 1, CURRENT_TIMESTAMP()
			);

			SET merchantCouponsId = LAST_INSERT_ID();

			IF merchantCouponsId > 0 THEN

				INSERT INTO merchant_coupon_values (
					merchant_coupons_id, category_id, brand_id, location_id, platform, reward_type, reward_type_x_value, reward_type_y_value, reward_type_y_value_unit, reward_type_expire_value, created_by, created_at
				) VALUES (
				    merchantCouponsId, category_ID, brand_ID, location_ID, 1, rewardType, rewardTypeXValue, rewardTypeYValue, rewardTypeYValueUnit, rewardTypeExpireValue, 1, CURRENT_TIMESTAMP()
				),
				(
				    merchantCouponsId, category_ID, brand_ID, location_ID, 2, rewardType, rewardTypeXValue, rewardTypeYValue, rewardTypeYValueUnit, rewardTypeExpireValue, 1, CURRENT_TIMESTAMP()
				),
				(
				    merchantCouponsId, category_ID, brand_ID, location_ID, 3, rewardType, rewardTypeXValue, rewardTypeYValue, rewardTypeYValueUnit, rewardTypeExpireValue, 1, CURRENT_TIMESTAMP()
				),
				(
				    merchantCouponsId, category_ID, brand_ID, location_ID, 4, rewardType, rewardTypeXValue, rewardTypeYValue, rewardTypeYValueUnit, rewardTypeExpireValue, 1, CURRENT_TIMESTAMP()
				);

				SET merchantCouponValuesId = LAST_INSERT_ID();

				IF merchantCouponValuesId > 0 THEN

					INSERT INTO customer_campaign_log (
						merchant_campaigns_id, customer_points_earned_redeemed_id, customer_id, location_id, category_id, brand_id, reference_id, reference_id_type, platform, points_earned, created_by, created_at
					) VALUES (
				    		merchantCampaignId, 0, customerId, location_ID, category_ID, brand_ID, referenceId, referenceIdType, platform, bonusPoints, 1, CURRENT_TIMESTAMP()
					);

					COMMIT;
					SELECT 'Success' AS status, 'Campaign applied successfully for this customer.' AS response, campaignTitle, bonusPoints, '200' AS reponseCode;
					LEAVE campaignPriority;

				ELSE

					SELECT 'Error' AS status, 'Merchant coupon values insertion failed.' AS response, campaignTitle, bonusPoints, '106' AS reponseCode;
					LEAVE campaignPriority;

				END IF;

			ELSE

				SELECT 'Error' AS status, 'Merchant coupon insertion failed.' AS response, campaignTitle, bonusPoints, '106' AS reponseCode;
				LEAVE campaignPriority;

			END IF;

		END IF;

	ELSE
		SELECT 'Error' AS status, 'Campaign not present.' AS response, campaignTitle, bonusPoints, '101' AS reponseCode;
		LEAVE campaignPriority;

	END IF;
END$$
DELIMITER ;