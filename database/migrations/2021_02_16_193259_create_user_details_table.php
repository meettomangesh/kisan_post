<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('role_id')->default(0)->unsigned()->index();
            $table->text('user_photo')->nullable();
            $table->text('aadhar_card_photo')->nullable();
            $table->text('pan_card_photo')->nullable();
            $table->text('license_card_photo')->nullable();
            $table->text('rc_book_photo')->nullable();
            $table->string('bank_name', 250)->nullable();
            $table->string('account_number', 250)->nullable();
            $table->string('ifsc_code', 250)->nullable();
            $table->string('aadhar_number', 250)->nullable();
            $table->string('pan_number', 250)->nullable();
            $table->string('license_number', 250)->nullable();
            $table->tinyInteger('vehicle_type')->default(4)->unsigned()->index()->comment = "1:Two wheeler,2:Three wheeler,3:Four wheeler,4:Other";
            $table->string('vehicle_number', 250)->nullable();

            $table->tinyInteger('status')->default(0)->unsigned()->index()->comment = "0:New, 1:Submitted, 2: Approve, 3: Rejected";
            $table->integer('created_by')->default(0)->unsigned();
            $table->integer('updated_by')->default(0)->unsigned();
            // $table->timestamps();
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
        Schema::dropIfExists('user_details');
    }
}
