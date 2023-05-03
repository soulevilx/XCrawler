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
        Schema::create('flickr_contacts', function (Blueprint $table) {
            $table->string('uuid')->unique();
            $table->string('nsid')->primary();
            $table->string('username')->nullable();
            $table->integer('iconserver')->nullable();
            $table->integer('iconfarm')->nullable();
            $table->boolean('ignored')->nullable();
            $table->boolean('rev_ignored')->nullable();
            $table->string('realname')->nullable();
            $table->boolean('friend')->nullable();
            $table->boolean('family')->nullable();
            $table->string('path_alias')->nullable();
            $table->text('location')->nullable();
            $table->boolean('ispro')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->json('details')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nsid', 'username', 'iconserver', 'iconfarm']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
