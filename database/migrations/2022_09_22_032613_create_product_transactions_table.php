<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('buy_price',$precision = 10);
            $table->decimal('sell_price',$precision = 10);
            $table->string('currency');
            $table->integer('amount');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('transaction_id');
            $table->float('weight')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onUpdate('cascade')->onDelete('cascade');
            $table->index('product_id');
            $table->index('transaction_id');
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
        Schema::dropIfExists('product_transactions');
    }
}
