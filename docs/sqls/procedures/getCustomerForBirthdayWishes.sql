DELIMITER $$
DROP PROCEDURE IF EXISTS getCustomerForBirthdayWishes$$
CREATE PROCEDURE getCustomerForBirthdayWishes()
getCustomerForBirthdayWishes:BEGIN
    
    SELECT u.id AS user_id, u.first_name, u.mobile_number, u.mobile_verified, u.email, u.email_verified, u.date_of_birth, ru.role_id
    FROM users AS u
    JOIN role_user AS ru ON ru.user_id = u.id
    WHERE ru.role_id = 4 AND MONTH(date_of_birth) = MONTH(CURDATE());
    -- AND DAY(date_of_birth) = DAY(CURDATE());

END$$
DELIMITER ;