<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_sizes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('photo_id')->unique();
            $table->foreign('photo_id')->references('id')->on('flickr_photos')->onDelete('cascade');

            $table->json('sizes');

            $table->timestamps();
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
        Schema::dropIfExists('photo_sizes');
    }
};
