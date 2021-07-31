DELIMITER $$
    DROP FUNCTION IF EXISTS `implode`$$
    CREATE FUNCTION `implode`(inputData JSON) RETURNS TEXT
    DETERMINISTIC
    BEGIN
        DECLARE funIsForInsert,includePostcodeAddNotFound,jCount INTEGER DEFAULT 0;
		DECLARE cusrRemoveIncludePostcode,cusrAddIncludePostcode VARCHAR(50) DEFAULT NULL;
        DECLARE returnImplodedStr TEXT DEFAULT NULL;
        
        IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
            SET returnImplodedStr = NULL ;
		ELSE

                SET @ipObj = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.paramObjectArr'));
				SET @i = 0; 
				SET jCount = 0;
				SET jCount = jCount + JSON_LENGTH( @ipObj, '$' );
				
				keyword_loop: WHILE ( @i <= jCount ) DO

					SET @keyWord =  JSON_UNQUOTE(JSON_EXTRACT( @ipObj, CONCAT( '$[', @i, ']')));
					
                    IF @keyWord IS NOT NULL THEN 
						SET returnImplodedStr = CONCAT_WS(",",returnImplodedStr,@keyWord);
					END IF;
					SET @i = @i + 1;

				END WHILE keyword_loop;
                return returnImplodedStr;
            
        END IF;
    END$$
DELIMITER ;
