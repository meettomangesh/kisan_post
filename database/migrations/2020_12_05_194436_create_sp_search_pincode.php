<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpSearchPincode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS searchPincode; 
        CREATE PROCEDURE `searchPincode`(IN pinCode VARCHAR(100))
        searchPincode:BEGIN 
            IF pinCode IS NULL OR pinCode = '' THEN 
                SELECT
                pin_codes.id,
                pin_code AS pc,
                pin_codes.country_id,
                pin_codes.state_id,
                pin_codes.city_id,
                cities.name AS city_name,
                states.name AS state_name,
                countries.name AS country_name
                FROM
                    pin_codes
                JOIN cities ON cities.id=pin_codes.city_id
                JOIN states ON states.id=pin_codes.state_id
                JOIN countries ON countries.id=pin_codes.country_id
                WHERE
                    pin_codes.status = 1 
                ORDER BY id;
            ELSE 
                SELECT
                pin_codes.id,
                pin_code AS pc,
                pin_codes.country_id,
                pin_codes.state_id,
                pin_codes.city_id,
                cities.name AS city_name,
                states.name AS state_name,
                countries.name AS country_name
                FROM
                    pin_codes
                JOIN cities ON cities.id=pin_codes.city_id
                JOIN states ON states.id=pin_codes.state_id
                JOIN countries ON countries.id=pin_codes.country_id
                WHERE
                    pin_codes.status = 1        
                AND
                pin_codes.pin_code LIKE CONCAT(pinCode, '%')
                ORDER BY id;
            END IF;

    END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS searchPincode');
    }
}
