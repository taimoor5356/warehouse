<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShippingInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shipping_info', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->text('address_1');
            $table->text('address_2');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->integer('city_id');
            $table->string('postal_code');
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
        Schema::dropIfExists('order_shipping_info');
    }
}
