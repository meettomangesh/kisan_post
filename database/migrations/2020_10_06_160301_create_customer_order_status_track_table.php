<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrderStatusTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_order_status_track', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('order_details_id')->default(0)->unsigned()->index();
            $table->tinyInteger('order_status')->default(1)->unsigned()->index()->comment = "0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled";
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
        Schema::dropIfExists('customer_order_status_track');
    }
}
