<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlist_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->time('start_at');
            $table->time('end_at');
            $table->timestamps(0);

            $table->foreignId('playlist_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('content_id')
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
        Schema::dropIfExists('playlist_content');
    }
}
