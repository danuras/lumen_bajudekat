<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('image_url');
            $table->unsignedBigInteger('categories_id')->nullable();
            $table->foreign('categories_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->index('categories_id');
            $table->longText('description');
            $table->decimal('buy_price',$precision = 10);
            $table->decimal('sell_price',$precision = 10);
            $table->string('currency');
            $table->float('weight')->unsigned();
            $table->unsignedInteger('discount')->default(0);
            $table->datetime('discount_expired_at')->nullable();
            $table->integer('stock')->default(0);
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
        Schema::dropIfExists('products');
    }
}
