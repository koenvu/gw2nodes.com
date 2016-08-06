<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('server');
            $table->integer('x');
            $table->integer('y');
            $table->boolean('is_rich')->default(0);

            $table->integer('container_id')->unsigned();
            $table->foreign('container_id')->references('id')->on('containers')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('map_id')->unsigned();
            $table->foreign('map_id')->references('id')->on('maps')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('build_id');
            $table->boolean('is_permanent')->default(0);
            $table->text('notes');

            $table->softDeletes();
            $table->timestamps();

            $table->index('build_id');
            $table->index('is_permanent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nodes');
    }
}
