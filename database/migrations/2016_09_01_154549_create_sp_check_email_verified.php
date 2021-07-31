<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSpCheckEmailVerified extends Migration {

    /**
      /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS checkEmailVerified; 
           
        CREATE  PROCEDURE checkEmailVerified(IN customerId INT)
        BEGIN  
        SELECT email, email_verified, email_verify_key 
        FROM users 
        WHERE id=customerId;
    END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkEmailVerified');
    }
}
