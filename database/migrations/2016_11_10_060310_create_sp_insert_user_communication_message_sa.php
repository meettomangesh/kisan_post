<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpInsertUserCommunicationMessageSa extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS insertCommunicationMessageSa; 
        CREATE PROCEDURE insertCommunicationMessageSa(
            
            IN message_title VARCHAR(100),
            IN message_type TINYINT,
            IN reference_id INT,            
            IN push_text VARCHAR(320),
            IN deep_link_screen VARCHAR(50), 
            IN sms_text VARCHAR(320),
            IN notify_users_by VARCHAR(10),
            IN email_from_name VARCHAR(100),
            IN email_from_email VARCHAR(100),
            IN email_subject VARCHAR(200),
            IN email_body VARCHAR(1000),
            IN email_tags VARCHAR(250),
            IN test_mode TINYINT,
            IN test_email_address VARCHAR(200),
            IN test_mobile_number VARCHAR(1000),
            IN message_send_time DATETIME,
            IN status TINYINT,
            IN created_by INT, 
            IN updated_by INT, 
            IN created_at timestamp, 
            OUT last_inserted_id INT
			)
            BEGIN
                
                INSERT INTO 
                user_communication_messages (
                   
                    message_title, 
                    message_type, 
                    reference_id,
                    push_text, 
                    deep_link_screen, 
                    sms_text, 
                    notify_users_by, 
                    email_from_name, 
                    email_from_email, 
                    email_subject, 
                    email_body, 
                    email_tags, 
                    test_mode, 
                    test_email_address, 
                    test_mobile_number, 
                    message_send_time, 
                    status, 
                    created_by, 
                    updated_by, 
                    created_at
                ) 
                VALUES (
                    
                    message_title, 
                    message_type, 
                    reference_id,
                    push_text, 
                    deep_link_screen, 
                    sms_text, 
                    notify_users_by, 
                    email_from_name, 
                    email_from_email, 
                    email_subject, 
                    email_body, 
                    email_tags, 
                    test_mode, 
                    test_email_address, 
                    test_mobile_number, 
                    message_send_time, 
                    status, 
                    created_by, 
                    updated_by, 
                    created_at
                );

            SET last_inserted_id = LAST_INSERT_ID();
            SELECT last_inserted_id;
                
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS insertCommunicationMessageSa');
    }

}
