<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpUpdateUserCommunicationMessageSa extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS updateCommunicationMessageSa; 
        CREATE PROCEDURE updateCommunicationMessageSa(
            	IN update_edit_id INT,
                
                IN update_message_title VARCHAR(100),
                IN update_message_type TINYINT,
                IN update_reference_id INT,    
                IN update_push_text VARCHAR(320),
                IN update_deep_link_screen  VARCHAR(50), 
                IN update_sms_text VARCHAR(320),
                IN update_notify_users_by VARCHAR(10),
                IN update_email_from_name VARCHAR(100),
                IN update_email_from_email VARCHAR(100),
                IN update_email_subject VARCHAR(200),
                IN update_email_body VARCHAR(1000),
                IN update_email_tags VARCHAR(1000),
                IN update_test_mode TINYINT,
                IN update_test_email_address VARCHAR(200),
                IN update_test_mobile_number VARCHAR(1000),
                IN update_message_send_time DATETIME,
                IN update_status TINYINT,
                IN update_updated_by INT, 
                IN update_updated_at timestamp, 
                OUT success INT
			)
            BEGIN
                
                SET success = 0;
                UPDATE 
                    user_communication_messages 
                SET 
                    
                    message_title = update_message_title, 
                    message_type = update_message_type, 
                    offer_id = update_reference_id, 
                    push_text = update_push_text, 
                    deep_link_screen = update_deep_link_screen, 
                    sms_text = update_sms_text, 
                    notify_users_by = update_notify_users_by, 
                    email_from_name = update_email_from_name, 
                    email_from_email = update_email_from_email, 
                    email_subject = update_email_subject, 
                    email_body = update_email_body, 
                    email_tags = update_email_tags, 
                    test_mode = update_test_mode, 
                    test_email_address = update_test_email_address, 
                    test_mobile_number = update_test_mobile_number, 
                    message_send_time = update_message_send_time, 
                    status = update_status, 
                    updated_by = update_updated_by, 
                    updated_at = update_updated_at 
                WHERE 
                    id = update_edit_id;

                IF ROW_COUNT() > 0 THEN
                    SET success = 1;
                    END IF;
                    SELECT success;
                
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS updateCommunicationMessageSa');
    }

}
