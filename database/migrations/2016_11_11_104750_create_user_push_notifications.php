<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPushNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_push_notifications', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('mobile_number', 50)->index();
            $table->integer('communication_msg_id')->default(0)->index();
            $table->string('custom_data', 50);
            $table->string('push_text', 320);
            $table->string('deep_link_screen', 50);
            $table->tinyInteger('notification_type')->default(0)->index()->comment = "";
            $table->tinyInteger('notification_received')->default(0)->index()->comment = "";
            $table->timestamp('received_at')->index()->nullable();
            $table->tinyInteger('tap_status')->default(0)->index()->comment = "";
            $table->integer('inapp_tap_count')->default(0)->index();
            $table->timestamp('tap_at')->index()->nullable();
            $table->timestamp('last_tap_at')->index()->nullable();
            $table->tinyInteger('tap_from')->nullable()->index()->comment = "0: NO 1: Android 2: iOS 3: website";
            $table->integer('created_by')->unsigned();
            $table->timestamp('created_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_push_notifications');
    }

}
