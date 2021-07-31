DELIMITER $$
DROP PROCEDURE IF EXISTS getWishlist$$
CREATE PROCEDURE getWishlist(IN inputData JSON)
getWishlist:BEGIN
    DECLARE userId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;

    IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE getWishlist;
    END IF;

    SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
    SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
    SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
    IF noOfRecords IS NULL OR pageNumber IS NULL THEN
        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
        LEAVE getWishlist;
    END IF;

    IF pageNumber > 0 THEN
        SET pageNumber = pageNumber * noOfRecords;
    END IF;

    /* SELECT cw.id AS wishlist_id,cw.product_units_id,cw.is_basket,COALESCE(p1.id, p2.id) id,COALESCE(p1.product_name, p2.product_name) product_name,COALESCE(p1.short_description, p2.short_description) short_description,COALESCE(p1.expiry_date, p2.expiry_date) expiry_date,TRUNCATE(COALESCE(p1.selling_price, p2.selling_price), 2) selling_price,TRUNCATE(COALESCE(p1.special_price, p2.special_price), 2) special_price,COALESCE(p1.special_price_start_date, p2.special_price_start_date) special_price_start_date,COALESCE(p1.special_price_end_date, p2.special_price_end_date) special_price_end_date,COALESCE(p1.min_quantity, p2.min_quantity) min_quantity,COALESCE(p1.max_quantity, p2.max_quantity) max_quantity
    -- ,COALESCE(p1.status, p2.status) p_status,COALESCE(p1.deleted_at, p2.deleted_at) p_deleted_at,COALESCE(p1.stock_availability, p2.stock_availability) p_stock_availability
    ,IF(COALESCE(p1.status, p2.status) = 1 AND COALESCE(p1.deleted_at, p2.deleted_at) IS NULL AND COALESCE(p1.stock_availability, p2.stock_availability) = 1 AND IF(COALESCE(p1.expiry_date, p2.expiry_date) IS NOT NULL, COALESCE(p1.expiry_date, p2.expiry_date) >= CURDATE(), 1=1), 1, 0) AS is_active
    FROM customer_wishlist AS cw
    LEFT JOIN products AS p1 ON p1.id = (SELECT products_id FROM product_units WHERE id = cw.product_units_id) AND cw.is_basket = 0
    LEFT JOIN products AS p2 ON p2.id = cw.product_units_id AND cw.is_basket = 1
    LEFT JOIN categories_master AS c ON (c.id = p1.category_id OR c.id = p2.category_id) AND c.status = 1
    WHERE cw.user_id = userId
    -- HAVING p_status = 1 AND p_deleted_at IS NULL AND p_stock_availability = 1 AND IF(expiry_date IS NOT NULL, expiry_date >= CURDATE(), 1=1)
    ORDER BY cw.id DESC LIMIT noOfRecords OFFSET pageNumber; */

    SELECT cw.id AS wishlist_id,cw.is_basket,p.id,p.product_name,p.short_description,p.expiry_date,TRUNCATE(p.selling_price, 2) AS selling_price,TRUNCATE(p.special_price, 2) AS special_price,p.special_price_start_date,p.special_price_end_date,p.min_quantity,p.max_quantity
    ,IF(p.status = 1 AND p.deleted_at IS NULL AND p.stock_availability = 1 AND IF(p.expiry_date IS NOT NULL, p.expiry_date >= CURDATE(), 1=1) AND cm.status = 1, 1, 0) AS is_active
    FROM customer_wishlist AS cw
    LEFT JOIN products AS p ON p.id = cw.products_id
    LEFT JOIN categories_master AS cm ON cm.id = p.category_id
    WHERE cw.user_id = userId
    ORDER BY cw.id DESC LIMIT noOfRecords OFFSET pageNumber;
END$$
DELIMITER ;