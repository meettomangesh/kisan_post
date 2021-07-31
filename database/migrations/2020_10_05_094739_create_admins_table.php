<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('users_id')->unsigned()->nullable()->index();
            $table->string('username', 50)->unique()->index();
            $table->string('email', 100)->index();
            $table->string('first_name', 50)->index();
            $table->string('last_name', 50)->index();
            $table->string('password', 100);
            $table->tinyInteger('gender')->default(0)->unsigned()->comment = "1: Male, 0: Female";
            $table->string('contact', 20)->comment = "Contact number either phone or mobile";
            $table->string('avatar', 255)->comment = "User profile picture";
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->integer('user_type_id')->unsigned()->index();
            $table->rememberToken();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->default(0)->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('null ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->softDeletes();
            $table->index(array('id', 'users_id'), 'admins_composite_index_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
