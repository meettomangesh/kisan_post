<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('landmark')->nullable();
            $table->string('pin_code', 15)->index()->nullable();
            $table->string('area');
            $table->integer('city_id')->default(0)->unsigned()->index();
            $table->integer('state_id')->default(0)->unsigned()->index();
            $table->string('mobile_number', 20)->index();
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->tinyInteger('is_primary')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_address');
    }
}
