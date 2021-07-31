<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_emails', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100)->index();
            $table->string('description', 255)->index();
            $table->text('email_to')->nullable();
            $table->text('email_cc')->nullable();
            $table->text('email_bcc')->nullable();
            $table->string('email_from', 100)->index();
            $table->string('subject', 255)->index();
            $table->text('text1');
            $table->text('text2')->nullable();
            $table->tinyInteger('email_type')->default(1)->unsigned()->index();
            $table->string('tags', 255)->index()->nullable();
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
        Schema::dropIfExists('system_emails');
    }
}
