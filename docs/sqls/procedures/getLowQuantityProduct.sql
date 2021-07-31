DELIMITER $$
DROP PROCEDURE IF EXISTS getLowQuantityProduct$$
CREATE PROCEDURE getLowQuantityProduct()
getLowQuantityProduct:BEGIN

    SELECT pu.id, p.product_name, um.unit, pli.current_quantity
    FROM product_units AS pu
    JOIN products AS p ON p.id = pu.products_id
    JOIN unit_master AS um ON um.id = pu.unit_id
    JOIN product_location_inventory AS pli ON pli.product_units_id = pu.id
    WHERE pli.current_quantity < 10;
    -- pli.current_quantity < p.notify_for_qty_below;

END$$
DELIMITER ;