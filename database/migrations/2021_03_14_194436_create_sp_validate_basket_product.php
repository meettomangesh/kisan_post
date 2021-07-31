<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpValidateBasketProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS validateBasketProducts;
        CREATE PROCEDURE validateBasketProducts(IN inputData JSON)
        validateBasketProducts:BEGIN
            DECLARE basketId,productsId,productUnitsId,notFound INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateBasketProducts;
            END IF;
            SET basketId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.basket_id'));
            
            IF basketId = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE validateBasketProducts;
            END IF;
        
            block1:BEGIN
                DECLARE basketProductUnitsCursor CURSOR FOR
                SELECT bpu.product_units_id, pu.products_id
                FROM basket_product_units AS bpu
                JOIN product_units AS pu ON pu.id = bpu.product_units_id
                WHERE bpu.basket_id = basketId;
        
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;
                OPEN basketProductUnitsCursor;
                basketProductUnitsLoop: LOOP
                    FETCH basketProductUnitsCursor INTO productUnitsId,productsId;
                    IF(notFound = 1) THEN
                        LEAVE basketProductUnitsLoop;
                    END IF;
        
                    IF NOT EXISTS(SELECT id FROM product_units WHERE id = productUnitsId AND products_id = productsId AND min_quantity <= 1 AND max_quantity >= 1 AND status = 1 AND deleted_at IS NULL) THEN
                        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Product unit data is not valid.','data',JSON_OBJECT(),'statusCode',520) AS response;
                        LEAVE validateBasketProducts;
                    END IF;
        
                    IF NOT EXISTS(SELECT id FROM product_location_inventory WHERE product_units_id = productUnitsId AND current_quantity >= 1) THEN
                        SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Quantity not available.','data',JSON_OBJECT(),'statusCode',520) AS response;
                        LEAVE validateBasketProducts;
                    END IF;
        
                END LOOP basketProductUnitsLoop;
                CLOSE basketProductUnitsCursor;
            END block1;        
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Basket data is valid.','data',JSON_OBJECT(),'statusCode',200) AS response;
            LEAVE validateBasketProducts;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS validateBasketProducts');
    }
}
