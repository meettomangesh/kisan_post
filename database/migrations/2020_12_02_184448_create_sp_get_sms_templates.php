<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpGetSmsTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSmsTemplates; 
        CREATE PROCEDURE getSmsTemplates(IN templateName VARCHAR(255))
        
        BEGIN

        SELECT
            message
        FROM
            sms_templates
        WHERE
            name = templateName;;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSmsTemplates');
    }
}
