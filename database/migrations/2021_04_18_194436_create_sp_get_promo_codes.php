<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpGetPromoCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getPromoCodes;
        CREATE PROCEDURE getPromoCodes(IN inputData JSON)
        getPromoCodes:BEGIN
            DECLARE userId,noOfRecords,pageNumber INTEGER(10) DEFAULT 0;
        
            IF inputData IS NOT NULL AND JSON_VALID(inputData) = 0 THEN
                SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Please provide valid data.','data',JSON_OBJECT(),'statusCode',520) AS response;
                LEAVE getPromoCodes;
            ELSE
                SET userId = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.user_id'));
                SET noOfRecords = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.no_of_records'));
                SET pageNumber = JSON_UNQUOTE(JSON_EXTRACT(inputData,'$.page_number'));
                IF userId IS NULL OR userId = 0 THEN
                    SELECT JSON_OBJECT('status', 'FAILURE', 'message', 'Something missing in input.','data',JSON_OBJECT(),'statusCode',520) AS response;
                    LEAVE getPromoCodes;
                END IF;
            END IF;
        
            IF pageNumber > 0 THEN
                SET pageNumber = pageNumber * noOfRecords;
            END IF;
        
            SELECT pc.promo_code,pc.start_date,pc.end_date,pcm.title,
            CASE WHEN pcm.reward_type = 2 THEN CONCAT(pcm.reward_type_x_value,'%')
                 WHEN pcm.reward_type = 3 THEN CONCAT('Rs.',pcm.reward_type_x_value)
            ELSE '' END AS promo_code_value
            FROM promo_codes AS pc
            JOIN promo_code_master AS pcm ON pcm.id = pc.promo_code_master_id
            WHERE pc.user_id = userId AND pc.is_code_used = 0 AND pc.status = 1 AND pc.start_date <= CURDATE() AND pc.end_date >= CURDATE()
            ORDER BY pc.id DESC
            LIMIT noOfRecords
            OFFSET pageNumber;
        
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPromoCodes');
    }
}
