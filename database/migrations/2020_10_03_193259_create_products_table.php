<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('brand_id')->default(0)->unsigned()->index();
            $table->integer('category_id')->default(0)->unsigned()->index();
            $table->string('product_name', 50)->index();
            $table->text('description')->nullable();
            $table->string('short_description', 255)->index();
            $table->string('sku', 50)->unique()->index();
            $table->date('expiry_date')->default(null)->nullable();
            $table->text('custom_text')->nullable();
            $table->tinyInteger('display_custom_text_or_date')->default(0)->unsigned()->index()->comment = "1: Custom Text, 0: Date";
            $table->text('images')->nullable();
            $table->decimal('voucher_value', 14, 4)->nullable();
            $table->decimal('selling_price', 14, 4);
            $table->decimal('special_price', 14, 4)->nullable()->comment = "This is the discounted price";
            $table->date('special_price_start_date')->nullable()->index();
            $table->date('special_price_end_date')->nullable()->index();
            $table->integer('opening_quantity')->default(0)->unsigned()->index();
            $table->integer('current_quantity')->default(0)->unsigned()->index();
            $table->integer('min_quantity')->default(0)->unsigned()->index();
            $table->integer('max_quantity')->default(0)->unsigned()->index();
            $table->integer('max_quantity_perday_percust')->default(0)->unsigned()->index();
            $table->integer('max_quantity_perday_allcust')->default(0)->unsigned()->index();
            $table->tinyInteger('notify_for_qty_below')->default(0)->unsigned()->index(); 
            $table->tinyInteger('stock_availability')->default(1)->unsigned()->index()->comment = "1: In Stock, 0: Out of Stock";
            $table->tinyInteger('show_in_search_results')->default(1)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->tinyInteger('pay_for_product_in')->default(1)->unsigned()->index()->comment = "1: COD, 0: Online";
            $table->tinyInteger('is_basket')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->integer('view_count')->default(0)->unsigned()->index();
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('products');
    }
}
