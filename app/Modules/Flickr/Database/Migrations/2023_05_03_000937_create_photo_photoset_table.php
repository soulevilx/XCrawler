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
        Schema::create('photo_photoset', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('photo_id');
            $table->foreign('photo_id')->references('id')->on('flickr_photos');

            $table->unsignedBigInteger('photoset_id');
            $table->foreign('photoset_id')->references('id')->on('flickr_photosets');

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
        Schema::dropIfExists('photo_photoset');
    }
};
