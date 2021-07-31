<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_otp', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_number', 15)->index();
            $table->mediumInteger('otp');
            $table->tinyInteger('sms_delivered')->default(1)->index()->comment = "1 : Yes, 0 : No";
            $table->string('error_message', 200)->nullable()->comment = "API returned error message.";
            $table->tinyInteger('otp_used')->default(0)->index()->comment = "1 : Yes, 0 : No";
            $table->tinyInteger('platform_generated_on')->default(0)->comment = "1 : Android, 2 : iOS, 3 : WebPOS, 4 : Website";
            $table->integer('otp_generated_for')->default(1)->comment = "201 : Login, 202 : Registration";
            // $table->timestamps();
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
        Schema::dropIfExists('customer_otp');
    }
}
