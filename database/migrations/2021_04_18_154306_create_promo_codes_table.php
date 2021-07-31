<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('promo_code_master_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('promo_code', 20)->default(null)->index()->nullable();
            $table->date('start_date')->default(null)->nullable();
            $table->date('end_date')->default(null)->nullable();
            $table->tinyInteger('is_code_used')->default(0)->unsigned()->index()->comment = "1: Yes, 0: No";
            $table->tinyInteger('status')->default(1)->unsigned()->index()->comment = "1: Active, 0: Inactive, 2: Expired";
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
        Schema::dropIfExists('promo_codes');
    }
}
