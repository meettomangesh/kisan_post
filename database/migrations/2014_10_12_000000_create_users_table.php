<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // $table->increments('id');
            // $table->string('name');
            // $table->string('mobile_number')->unique();
            // $table->timestamp('mobile_number_verified_at')->nullable();            
            // $table->string('email')->unique();
            // $table->datetime('email_verified_at')->nullable();
            // $table->string('password');
            // $table->string('remember_token')->nullable();
            // // $table->timestamps();
            // $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            // $table->timestamp('updated_at')->default(DB::raw('null ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            // $table->softDeletes();
            $table->increments('id')->unsigned();
            $table->string('first_name', 100)->index();
            $table->string('last_name', 100)->default(null)->nullable();
            $table->string('mobile_number', 20)->unique()->index();
            $table->tinyInteger('mobile_verified')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->timestamp('mobile_number_verified_at')->nullable();
            $table->string('email', 100)->default(null)->nullable();
            $table->tinyInteger('email_verified')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->datetime('email_verified_at')->nullable();
            $table->string('email_verify_key', 100)->default(null)->nullable();
            $table->text('address_1')->nullable();
            $table->text('address_2')->nullable();
            $table->text('address_3')->nullable();
            $table->date('date_of_birth')->default(null)->nullable();
            $table->tinyInteger('gender')->default(0)->unsigned()->comment = "1: Male, 2: Female";
            $table->tinyInteger('marital_status')->default(0)->unsigned()->comment = "1: Yes, 0: No";
            $table->date('anniversary_date')->default(null)->nullable();
            $table->date('spouse_dob')->default(null)->nullable();
            $table->integer('city_id')->default(0)->unsigned()->index();
            $table->string('pin_code', 15)->index()->nullable();
            $table->tinyInteger('registered_from')->default(1)->unsigned()->index();
            $table->string('referral_code', 20)->default(null)->nullable();
            $table->integer('referred_by_user_id')->default(0)->unsigned()->index();
            $table->tinyInteger('is_app_installed')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->date('app_installed_date')->default(null)->nullable();
            $table->string('app_installed_browser', 50)->default(null)->nullable();
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->string('password');
            $table->string('password_plain');
            $table->string('remember_token')->nullable();
            $table->integer('created_by')->default(0)->unsigned();
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
        Schema::dropIfExists('users');
    }
}
