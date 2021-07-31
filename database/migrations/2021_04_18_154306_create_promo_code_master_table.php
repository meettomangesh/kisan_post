<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_code_master', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('campaign_category_id')->unsigned()->index();
            $table->integer('campaign_master_id')->unsigned()->index();
            $table->string('title', 255)->index()->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->default(null)->nullable();
            $table->date('end_date')->default(null)->nullable();
            $table->string('platforms', 100)->index()->nullable()->comment = "Comma separated platform ids";
            $table->string('category_ids', 255)->index()->nullable()->comment = "Comma seperated ids";
            $table->string('sub_category_ids', 255)->index()->nullable();
            $table->tinyInteger('target_customer')->default(1)->unsigned()->index()->comment = "1: Open, 2: Custom (Customer List), 3: Gender, 4: Marital Status";
            $table->text('target_customer_value')->nullable()->comment = "Ex. User ids";
            $table->tinyInteger('target_product')->default(0)->unsigned()->index()->comment = "1: Product wise 0: None";
            $table->text('target_product_value')->nullable()->comment = "Ex. Product ids";
            $table->tinyInteger('reward_type')->default(0)->unsigned()->index()->comment = "1: Multiple, 2: Percentage, 3: Direct";
            $table->integer('reward_type_x_value')->default(0)->unsigned()->index()->comment = "1: 2X/3X, 2: 25%,50%, 3: 100 pts";
            $table->tinyInteger('campaign_use')->default(1)->unsigned()->index()->comment = "1: Unlimited, 2: Limited";
            $table->tinyInteger('campaign_use_value')->default(0)->unsigned()->index();
            $table->tinyInteger('referral_user_type')->default(0)->unsigned()->index()->comment = "0: None, 1: Referrer, 2: Refree";
            $table->tinyInteger('code_type')->default(1)->unsigned()->index()->comment = "1: Generic, 2: Unique";
            $table->string('promo_code', 20)->default(null)->index()->nullable();
            $table->integer('code_expire_in_days')->default(0)->unsigned()->index();
            $table->integer('code_qty')->default(0)->unsigned()->index();
            $table->tinyInteger('priority')->default(1)->unsigned()->index();
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive";
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
        Schema::dropIfExists('promo_code_master');
    }
}
