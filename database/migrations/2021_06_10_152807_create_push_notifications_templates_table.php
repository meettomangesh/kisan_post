<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notifications_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->index();
            $table->string('title', 100)->index();
            $table->string('message', 1000);
            $table->string('deeplink', 255)->index();
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->default(0)->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('null ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('push_notifications_templates');
    }
}
