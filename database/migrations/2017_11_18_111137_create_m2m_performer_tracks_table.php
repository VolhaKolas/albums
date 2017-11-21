<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateM2mPerformerTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m2m_performer_tracks', function (Blueprint $table) {
            $table->increments('m2m_performer_track_id');
            $table->integer('track_id')->length(10)->unsigned();
            $table->foreign('track_id')->references('track_id')->on('tracks')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('performer_id')->length(10)->unsigned();
            $table->foreign('performer_id')->references('performer_id')->on('performers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m2m_performer_tracks');
    }
}
