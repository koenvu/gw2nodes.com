<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pois', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id');

            $table->integer('map_id')->unsigned();
            $table->foreign('map_id')->references('id')->on('maps')->onUpdate('cascade')->onDelete('cascade');
            
            $table->string('name');
            $table->string('type');
            $table->integer('x');
            $table->integer('y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pois');
    }
}
