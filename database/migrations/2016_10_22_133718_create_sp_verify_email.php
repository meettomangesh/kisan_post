<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpVerifyEmail extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS verifyEmail; 
		CREATE PROCEDURE verifyEmail(IN emailVerifyKey VARCHAR(50))
	BEGIN
	DECLARE customerEmailAddress, emailAddress VARCHAR(255) DEFAULT "";
    DECLARE emailVerified TINYINT(4);
    
    SET emailVerified = 0;

	SELECT email INTO customerEmailAddress FROM users WHERE email_verify_key = emailVerifyKey LIMIT 1;
	IF customerEmailAddress != "" THEN
		SET emailVerified = 2;
		UPDATE users
		SET email_verified=1
		WHERE email_verify_key = emailVerifyKey;
		IF ROW_COUNT() > 0 THEN
			SET emailVerified = 1;
			SET emailAddress = customerEmailAddress;	
		END IF;
    END IF;
	
	SELECT emailAddress, emailVerified;
END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS verifyEmail');
    }
}
