<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->increments('track_id');
            $table->integer('album_id')->length(10)->unsigned();
            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('cascade')->onUpdate('cascade');
            $table->string('track_path', 50);
            $table->string('track_name', 100);
            $table->string('track_duration', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
