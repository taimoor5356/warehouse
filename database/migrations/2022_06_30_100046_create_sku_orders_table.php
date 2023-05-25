<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('sku_id')->nullable();
            $table->string('sku_id_name')->nullable();
            $table->string('name')->nullable();
            $table->double('weight', 5,2)->default('0.00');
            $table->integer('brand_id')->nullable();
            $table->double('purchasing_cost', 15,2)->default('0.00');
            $table->double('selling_cost', 15,2)->default('0.00');
            $table->double('grand_total_amount', 15,2)->default('0.00');
            $table->integer('pick_pack_flat_status')->default('0');
            $table->double('service_charges', 15,2)->default('0.00');
            $table->json('service_charges_detail')->nullable();
            $table->double('mailer_cost')->default('0.00');
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
        Schema::dropIfExists('sku_orders');
    }
}
