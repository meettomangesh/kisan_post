<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_form', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('supplier_name', 250)->index();
            $table->string('product_name', 250)->index();
            $table->string('unit', 20)->index();
            $table->string('category', 50)->index();
            $table->decimal('price', 14, 4);
            $table->date('order_date')->index();
            $table->string('total_in_kg', 10)->index();
            $table->string('total_units', 10)->index();
            $table->string('image_name', 1000);
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
        Schema::dropIfExists('purchase_form');
    }
}
