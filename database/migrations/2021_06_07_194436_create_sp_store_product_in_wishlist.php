<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpStoreProductInWishlist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS storeProductInWishlist;
        CREATE PROCEDURE storeProductInWishlist(IN inputData JSON)
        storeProductInWishlist:BEGIN
            DECLARE userId,productsId,wishlistId INTEGER(10) DEFAULT 0;
            DECLARE isBasket TINYINT(1) DEFAULT 0;
            DECLARE EXIT HANDLER FOR 1062
            BEGIN
                ROLLBACK;
                SELECT JSON_OBJECT('status','FAILURE','message','Already present in wishlist.','data',JSON_OBJECT('statusCode',404),'statusCode',404) AS response;
            END;
            
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
            SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
            SET productsId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.products_id'));
            SET isBasket = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.is_basket'));
        
            IF userId = 0 OR productsId = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
           
            IF NOT EXISTS(SELECT id FROM users WHERE id = userId) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Invalid user.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            END IF;
        
            IF NOT EXISTS(SELECT id FROM products WHERE id = productsId AND IF(isBasket = 1, is_basket = 1, 1=1)) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Invalid product.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            ELSEIF EXISTS(SELECT id FROM customer_wishlist WHERE user_id = userId AND products_id = productsId) THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Already present in wishlist.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE storeProductInWishlist;
            ELSE
                INSERT INTO customer_wishlist (user_id,products_id,is_basket,created_by)
                VALUES (userId,productsId,isBasket,userId);
                SET wishlistId = LAST_INSERT_ID();
            END IF;
        
            SELECT JSON_OBJECT('status', 'SUCCESS', 'message', 'Product/Basket stored successfully.','data',JSON_OBJECT('wishlist_id', wishlistId),'statusCode',200) AS response;
            LEAVE storeProductInWishlist;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS storeProductInWishlist');
    }
}
