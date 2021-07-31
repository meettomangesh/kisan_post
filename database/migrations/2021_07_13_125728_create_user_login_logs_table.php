<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->tinyInteger('platform')->default(1)->unsigned()->index()->comment = "1: Android, 2: iOS";
            $table->timestamp('login_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('is_login')->default(1)->unsigned()->index()->comment = "1: Login, 2: Logout";
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('null ON UPDATE CURRENT_TIMESTAMP'))->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_logs');
    }
}
