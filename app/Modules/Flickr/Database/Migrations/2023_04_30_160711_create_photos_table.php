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
        Schema::create('flickr_photos', function (Blueprint $table) {
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('id')->primary();

            $table->string('owner')->index();
            $table->foreign('owner')->references('nsid')->on('flickr_contacts');

            $table->string('secret')->nullable();
            $table->integer('server')->nullable();
            $table->integer('farm')->nullable();
            $table->string('title')->nullable();
            $table->boolean('ispublic')->nullable();
            $table->boolean('isfriend')->nullable();
            $table->boolean('isfamily')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['id', 'secret', 'server', 'farm']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flickr_photos');
    }
};
