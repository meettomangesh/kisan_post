<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCommunicationMessagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_communication_messages', function(Blueprint $table)
        {
            $table->increments('id');
           // $table->integer('conference_id')->unsigned()->index();
            $table->string('message_title', 100);
            $table->boolean('message_type')->index()->default(1)->comment = "1 : Message";
            $table->integer('reference_id')->unsigned()->index()->nullable();  
            $table->integer('offer_id')->unsigned()->index()->nullable();
            $table->integer('region_type')->default(1)->unsigned()->index()->nullable();
            $table->integer('user_role')->default(4)->unsigned()->index()->nullable();
            $table->integer('user_type')->default(1)->unsigned()->index()->nullable();                    
            $table->string('push_text', 320);
            $table->string('deep_link_screen', 50);
            $table->string('sms_text', 500);
            $table->string('notify_users_by', 10)->default('0000')->index()->comment = "1000: Email, 0100 : Push Notification, 0010 : SMS, 0001 : SMS and Notifications";
            $table->string('email_from_name', 100);
            $table->string('email_from_email', 100);
            $table->string('email_subject', 200);
            $table->text('email_body');
            $table->string('email_tags', 250)->nullable();
            $table->tinyInteger('gender_filter')->default(0)->index()->comment = "0: Male, 1: Female, 2: Other";
            $table->decimal('min_points_filter', 14, 4);
            $table->decimal('max_points_filter', 14, 4);
            $table->tinyInteger('upload_type')->default(0)->index()->comment = "0: None, 1: Emails, 2: Mobile Numbers";
            $table->text('uploaded_data');
            $table->boolean('test_mode')->index()->default(1)->comment = "1 : Yes, 0 : No";
            $table->string('test_email_address', 1000);
            $table->string('test_mobile_number', 1000);
            $table->dateTime('message_send_time');
            $table->boolean('status')->index()->default(1)->comment = "1 : Active, 0 : Inactive";
            $table->tinyInteger('processed')->default(0)->index()->comment = "1: Yes, 0: No";
            $table->integer('email_count')->default(0)->index();
            $table->integer('sms_count')->default(0)->index();
            $table->integer('push_notification_count')->default(0)->index();
            $table->integer('push_notification_received_count')->default(0);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_communication_messages');
    }

}
