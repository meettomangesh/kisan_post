<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodeFormatMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_code_format_master', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('promo_code_master_id')->unsigned()->index();
            $table->tinyInteger('code_format')->default(0)->unsigned()->index()->comment = "0: None, 1: Numeric, 2: Alphanumeric";
            $table->string('code_prefix', 5)->default(null)->nullable()->index();
            $table->string('code_suffix', 5)->default(null)->nullable()->index();
            $table->integer('code_length')->unsigned()->index();
            $table->string('code_sample', 20)->default(null)->nullable();
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
        Schema::dropIfExists('promo_code_format_master');
    }
}
