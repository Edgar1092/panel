<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 45);
            $table->string('name', 45);
            $table->string('serial', 45)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->string('brand', 45)->nullable();
            $table->string('manufacturer', 45)->nullable();
            $table->string('os', 45)->nullable();
            $table->string('version', 45)->nullable();
            $table->boolean('offline')->default(0);
            $table->datetime('sync_at')->default(Carbon::now());
            $table->softDeletes('deleted_at', 0);
            $table->timestamps(0);

            $table->foreignId('user_id')
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
        Schema::dropIfExists('screens');
    }
}
