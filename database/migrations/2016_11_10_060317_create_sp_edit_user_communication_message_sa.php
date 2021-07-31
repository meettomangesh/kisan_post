<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpEditUserCommunicationMessageSa extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS editCommunicationMessageSa; 
        CREATE PROCEDURE `editCommunicationMessageSa`(IN `edit_id` INT)
BEGIN
                
                SELECT 
                 c.id, 
                
                c.message_title,
                c.message_type,
                CASE 
                    WHEN 
                        c.message_type = "1" 
                    THEN 
                        "Offer"
                    ELSE
                        "Message"
                    END AS message_type_name,
                c.offer_id,
                c.push_text, 
                c.deep_link_screen, 
                c.sms_text, 
                c.notify_users_by,
                c.email_from_name,
                c.email_from_email, 
                c.email_subject, 
                c.email_body, 
                c.email_tags, 
                c.test_mode, 
                c.test_email_address, 
                c.test_mobile_number, 
                c.message_send_time, 
                c.status, 
                c.created_by, 
                c.updated_by, 
                c.created_at, 
                c.updated_at,
                cm.name
            FROM
                customer_communication_messages as c
            WHERE
                c.id = edit_id;
                
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS editCommunicationMessageSa');
    }

}
