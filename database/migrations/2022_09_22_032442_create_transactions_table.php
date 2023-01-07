<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedSmallInteger('status');
            $table->unsignedBigInteger('cust_id');
            $table->string('nomor_resi')->nullable();
            $table->string('payment')->nullable();
            $table->string('kurir')->nullable();
            $table->double('total')->nullable();
            $table->double('ongkos')->nullable();
            $table->string('address')->nullable();
            $table->foreign('cust_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->index('cust_id');
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
        Schema::dropIfExists('transactions');
    }
}
