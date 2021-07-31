DELIMITER $$
DROP PROCEDURE IF EXISTS getProductList$$
CREATE PROCEDURE getProductList(IN inputData JSON)
getProductList:BEGIN
    DECLARE searchValue,sortType,sortOn,subCategoryIds VARCHAR(100) DEFAULT '';
    DECLARE categoryId,subCategoryId,noOfRecords,pageNumber,basketCategoryId INTEGER(10) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE getProductList;
    ELSE
        SET categoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.category_id'));
        SET subCategoryId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sub_category_id'));
        SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
        SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
        SET searchValue = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.search_value'));
        SET sortType = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sort_type'));
        SET sortOn = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.sort_on'));
        IF noOfRecords IS NULL OR pageNumber IS NULL THEN
            SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
            LEAVE getProductList;
        END IF;
    END IF;

    IF pageNumber > 0 THEN
        SET pageNumber = pageNumber * noOfRecords;
    END IF;

    /* SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,IF(p.special_price IS NOT NULL AND p.special_price_start_date <= CURDATE() AND p.special_price_end_date >= CURDATE(), TRUNCATE(p.special_price, 2), 0.00)  AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity,pli.current_quantity
    FROM products AS p
    JOIN product_location_inventory AS pli ON p.id = pli.products_id
    WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND pli.current_quantity > 0 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND IF(categoryId = 0 OR categoryId IS NULL, 1=1, p.category_id = categoryId)
    -- AND (searchValue IS NULL, 1=1, p.product_name LIKE "%searchValue%")
    ORDER BY p.selling_price ASC
    LIMIT noOfRecords
    OFFSET pageNumber; */

    SET @whrCategory = ' 1=1 ';
    IF subCategoryId > 0 AND subCategoryId IS NOT NULL THEN
        SET @whrCategory = CONCAT(' p.category_id = ', subCategoryId, ' ');
    ELSEIF categoryId > 0 AND categoryId IS NOT NULL THEN
        SELECT id INTO basketCategoryId FROM categories_master WHERE cat_name = 'Basket';
        IF categoryId = basketCategoryId THEN 
            SET @whrCategory = CONCAT(' p.is_basket = 1 ');
        ELSE
            SELECT GROUP_CONCAT(id) INTO subCategoryIds FROM categories_master WHERE status = 1 AND cat_parent_id = categoryId;
            SET @whrCategory = CONCAT(' p.category_id IN (', subCategoryIds, ') ');
        END IF;
    END IF;

    SET @orderBy = ' p.product_name ASC ';
    /* SET @orderBy = ' p.selling_price ASC ';
    IF sortType != '' AND sortOn != '' AND sortType != 'null' AND sortOn != 'null' THEN
        SET @orderBy = CONCAT(' ', sortType, ' ', sortOn, ' ');
    END IF; */
    SET @whrSearch = ' 1=1 ';
    IF searchValue != '' AND searchValue != 'null' THEN
        SET @whrSearch = CONCAT(' p.product_name LIKE "%', searchValue, '%"');
    END IF;

    /* SET @sqlStmt = CONCAT('SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,IF(p.special_price IS NOT NULL AND p.special_price_start_date <= CURDATE() AND p.special_price_end_date >= CURDATE(), TRUNCATE(p.special_price, 2), 0.00)  AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity,pli.current_quantity
    FROM products AS p
    JOIN product_location_inventory AS pli ON p.id = pli.products_id
    WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND pli.current_quantity > 0 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND '
    , @whrCategory, ' AND ', @whrSearch, ' ORDER BY ', @orderBy, ' LIMIT ', noOfRecords, ' OFFSET ', pageNumber); */

    SET @sqlStmt = CONCAT('SELECT p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,TRUNCATE(p.special_price, 2) AS special_price,p.special_price_start_date,p.special_price_end_date,p.is_basket,p.min_quantity,p.max_quantity
    FROM products AS p
    LEFT JOIN categories_master AS c ON c.id = p.category_id AND c.status = 1
    WHERE p.deleted_at IS NULL AND p.status = 1 AND p.stock_availability = 1 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND '
    , @whrCategory, ' AND ', @whrSearch, ' ORDER BY ', @orderBy, ' LIMIT ', noOfRecords, ' OFFSET ', pageNumber);

    PREPARE stmt from @sqlStmt;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    -- SELECT JSON_OBJECT('status','SUCCESS', 'message','No record found.','data',JSON_OBJECT('statusCode',104),'statusCode',104) AS response;
    -- LEAVE getProductList;
END$$
DELIMITER ;
