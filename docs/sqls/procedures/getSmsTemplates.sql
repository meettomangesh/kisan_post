DELIMITER $$
DROP PROCEDURE IF EXISTS getSmsTemplates$$
 CREATE PROCEDURE getSmsTemplates(IN templateName VARCHAR(255))
        
        BEGIN

        SELECT
            message
        FROM
            sms_templates
        WHERE
            name = templateName;
        END$$
DELIMITER ;
