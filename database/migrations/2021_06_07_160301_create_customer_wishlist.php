<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerWishlist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_wishlist', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->default(0)->unsigned()->index();
            $table->integer('products_id')->default(0)->unsigned()->index();
            $table->tinyInteger('is_basket')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->integer('created_by')->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('customer_wishlist');
    }
}
