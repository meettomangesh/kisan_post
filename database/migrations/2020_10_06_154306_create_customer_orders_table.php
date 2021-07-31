<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('customer_id')->default(0)->unsigned()->index();
            $table->integer('delivery_boy_id')->default(0)->unsigned()->index();
            $table->integer('shipping_address_id')->default(0)->unsigned()->index();
            $table->integer('billing_address_id')->default(0)->unsigned()->index();
            $table->date('delivery_date')->nullable()->index();
            $table->decimal('net_amount', 10, 4);
            $table->decimal('gross_amount', 10, 4);
            $table->decimal('discounted_amount', 14, 4)->nullable();
            $table->decimal('delivery_charge', 10, 4);
            $table->string('payment_type', 20)->index()->nullable();
            $table->string('razorpay_order_id', 255)->index()->nullable();
            $table->string('razorpay_payment_id', 255)->index()->nullable();
            $table->string('razorpay_signature', 255)->index()->nullable();
            $table->integer('total_items')->default(0)->unsigned()->index();
            $table->integer('total_items_quantity')->default(0)->unsigned()->index();
            $table->string('reject_cancel_reason', 255)->index()->nullable();
            $table->tinyInteger('purchased_from')->default(1)->unsigned()->index();
            $table->tinyInteger('is_coupon_applied')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->string('promo_code', 20)->nullable();
            $table->tinyInteger('is_basket_in_order')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->tinyInteger('order_status')->default(1)->unsigned()->index()->comment = "0: Pending, 1: Placed, 2: Picked, 3: Out for delivery, 4: Delivered, 5: Cancelled";
            $table->text('customer_invoice_url')->nullable();
            $table->text('delivery_boy_invoice_url')->nullable();
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
        Schema::dropIfExists('customer_orders');
    }
}
