<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sku_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('sku_order_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->bigInteger('quantity')->default('0');
            $table->double('purchasing_cost', 15,2)->default('0.00');
            $table->double('selling_cost', 15,2)->default('0.00');
            $table->double('label', 15,2)->default('0.00');
            $table->double('pick', 15,2)->default('0.00');
            $table->double('pack', 15,2)->default('0.00');
            $table->integer('pick_pack_flat_status')->default('0');
            $table->integer('is_active')->default('1');
            $table->integer('seller_cost_status')->default('0');
            $table->date('date')->nullable();
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
        Schema::dropIfExists('sku_order_details');
    }
}
