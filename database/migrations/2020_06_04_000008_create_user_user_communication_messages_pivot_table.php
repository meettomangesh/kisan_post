<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserUserCommunicationMessagesPivotTable extends Migration
{
    public function up()
    {
        Schema::create('user_user_communication_messages', function (Blueprint $table) {
            $table->unsignedInteger('user_communication_messages_id');
            $table->foreign('user_communication_messages_id', 'user_communication_messages_id_fk_1584547677656958')->references('id')->on('user_communication_messages')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_158695476756558')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
