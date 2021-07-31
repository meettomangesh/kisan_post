DELIMITER $$
DROP PROCEDURE IF EXISTS bulkAssignReferralCode$$
CREATE PROCEDURE bulkAssignReferralCode()
bulkAssignReferralCode:BEGIN

    DECLARE userId,ready INT(10) DEFAULT 0;
    DECLARE notFound TINYINT(1) DEFAULT 0;
    DECLARE referralCode VARCHAR(20) DEFAULT '';

    block1:BEGIN
        DECLARE bulkAssignCursor CURSOR FOR
		SELECT u.id
		FROM users AS u
        JOIN role_user AS ru ON ru.user_id = u.id
        WHERE ru.role_id = 4 AND u.referral_code IS NULL LIMIT 2000 OFFSET 0;

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
        OPEN bulkAssignCursor;
        bulkAssignLoop: LOOP
            FETCH bulkAssignCursor INTO userId;
            IF(notFound = 1) THEN
                LEAVE bulkAssignLoop;
            END IF;

            SET ready = 0;
            WHILE NOT ready DO
                SELECT lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0) INTO referralCode;
                IF NOT EXISTS (select id from users where referral_code = referralCode) THEN
                    UPDATE users SET referral_code = referralCode, updated_at = CURRENT_TIMESTAMP() WHERE id = userId;
                    SET ready = 1;
                END IF;
            END WHILE;

        END LOOP bulkAssignLoop;
        CLOSE bulkAssignCursor;
    END block1;

    SELECT JSON_OBJECT('status','SUCCESS','message','Bulk assign referral code data.','data',JSON_OBJECT('statusCode',200),'statusCode',200) as response;
    LEAVE bulkAssignReferralCode;

END$$
DELIMITER ;