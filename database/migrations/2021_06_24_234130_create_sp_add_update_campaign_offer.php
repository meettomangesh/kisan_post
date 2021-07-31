<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpAddUpdateCampaignOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS addUpdateCampaignOffer;
        CREATE PROCEDURE addUpdateCampaignOffer(IN inputData JSON)
addUpdateCampaignOffer:BEGIN
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
                -- IF codeType = 1 THEN
                    INSERT INTO promo_code_format_master (`promo_code_master_id`,`code_format`,`code_prefix`,`code_suffix`,`code_length`,`created_by`) VALUES
                        (lastInsertedId,codeFormat,codePrefix,codeSuffix,codeLength,1);
                    IF LAST_INSERT_ID() = 0 THEN
                        ROLLBACK;
                        SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'There is problem to add campaign offers code format.','data',JSON_OBJECT(),'statusCode',500) AS response;
                        LEAVE addUpdateCampaignOffer;
                    END IF;
                -- END IF;
                
                IF codeType = 1 THEN 
                    -- CODE FOR GENERIC CODE
                    SET quantity = 1;
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
                    -- CODE FOR UNIQUE CODE
                    WHILE i < JSON_LENGTH(targetCustomerValue) DO
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
END"

        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS addUpdateCampaignOffer');
    }
}
