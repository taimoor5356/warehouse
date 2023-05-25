<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_return_details', function (Blueprint $table) {
            $table->id();
            $table->integer('order_return_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->double('price', 15,2)->nullable();
            $table->integer('qty')->nullable();
            $table->integer('status')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('order_return_details');
    }
}
