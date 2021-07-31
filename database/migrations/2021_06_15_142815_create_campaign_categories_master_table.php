<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignCategoriesMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_categories_master', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 250)->index();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('campaign_categories_master');
    }
}
