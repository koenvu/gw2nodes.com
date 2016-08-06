<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->onUpdate('cascade');
            $table->integer('build_id');

            $table->integer('buys_quantity');
            $table->mediumInteger('buys_unit_price');

            $table->integer('sells_quantity');
            $table->mediumInteger('sells_unit_price');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prices');
    }
}
