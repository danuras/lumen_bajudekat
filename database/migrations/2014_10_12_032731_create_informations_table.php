<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informations', function (Blueprint $table) {
            
            $table->string('short_description');
            $table->string('description');
            $table->string('no_telp');
            $table->string('email');
            $table->string('link_market_place');
            $table->string('link_tiktok');
            $table->string('link_instagram');
            $table->string('link_facebook');
            $table->string('link_twitter');
            $table->string('link_pinterest');
            $table->string('link_linkedin');
            $table->string('link_youtube');
            $table->string('city');
            $table->string('address');
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
        Schema::dropIfExists('informations');
    }
}
