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
        Schema::create('flickr_photosets', function (Blueprint $table) {
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('id')->primary();

            $table->string('owner')->index();
            $table->foreign('owner')->references('nsid')->on('flickr_contacts');

            $table->integer('primary');
            $table->string('secret');
            $table->integer('server');
            $table->integer('farm');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('count_photos')->default(0);
            $table->integer('count_videos')->default(0);

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
        Schema::dropIfExists('flickr_photosets');
    }
};
