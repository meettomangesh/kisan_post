<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUserDeviceTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('customer_device_tokens', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->default(0)->unsigned()->index();
            $table->integer('user_role_id')->default(0)->unsigned()->index();
            $table->string('device_token', 255)->index();
            $table->string('device_id', 255)->nullable();
            $table->tinyInteger('device_type')->index()->default(1)->comment = "1: Android, 2: iOS";
            $table->tinyInteger('status')->default(1)->index()->comment = "1 : Active, 0 : Inactive, 2 : Deleted";
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
        Schema::drop('user_device_tokens');
    }

}
