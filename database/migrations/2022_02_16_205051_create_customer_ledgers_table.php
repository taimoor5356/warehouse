<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('order_number')->nullable();
            $table->integer('product_id')->nullable();
            $table->float('qty', 30, 4)->default(0)->nullable();
            $table->integer('item_status')->nullable();
            $table->float('return_service_charges', 10, 4)->default(0)->nullable();
            $table->float('debit', 30, 4)->default(0)->nullable();
            $table->float('credit', 30, 4)->default(0)->nullable();
            $table->float('balance', 30, 4)->default(0)->nullable();
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
        Schema::dropIfExists('customer_ledgers');
    }
}
