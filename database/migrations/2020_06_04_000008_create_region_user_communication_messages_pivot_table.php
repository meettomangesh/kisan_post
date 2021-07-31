<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionUserCommunicationMessagesPivotTable extends Migration
{
    public function up()
    {
        Schema::create('region_user_communication_messages', function (Blueprint $table) {
            $table->unsignedInteger('user_communication_messages_id');
            $table->foreign('user_communication_messages_id', 'user_communication_messages_id_fk_1586675743958')->references('id')->on('user_communication_messages')->onDelete('cascade');
            $table->unsignedInteger('region_id');
            $table->foreign('region_id', 'region_id_fk_158545689898958')->references('id')->on('region_master')->onDelete('cascade');
        });
    }
}
