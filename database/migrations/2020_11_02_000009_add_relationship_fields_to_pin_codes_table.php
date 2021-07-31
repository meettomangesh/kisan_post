<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPincodesTable extends Migration
{
    public function up()
    {
        Schema::table('pin_codes', function (Blueprint $table) {

            $table->foreign('country_id', 'country_fk_15876756974')->references('id')->on('countries');
            $table->foreign('state_id', 'state_fk_1586767376974')->references('id')->on('states');
            $table->foreign('city_id', 'city_fk_1586974')->references('id')->on('cities');
        });
    }
}
