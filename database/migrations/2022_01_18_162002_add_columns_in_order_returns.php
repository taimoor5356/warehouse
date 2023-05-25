<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInOrderReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_returns', function (Blueprint $table) {
            //
            $table->string('order_number')->nullable()->after('order_id');
            $table->integer('brand_id')->nullable()->after('customer_id');
            $table->double('total_price', 15,2)->nullable()->after('brand_id');
            $table->integer('total_qty')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_returns', function (Blueprint $table) {
            //
        });
    }
}
