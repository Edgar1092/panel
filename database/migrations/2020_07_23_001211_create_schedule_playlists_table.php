<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_playlists', function (Blueprint $table) {
            $table->id();
            $table->boolean('fulltime')->default(0);
            $table->timestamps(0);

            $table->foreignId('schedule_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('playlist_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('screen_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_playlists');
    }
}
