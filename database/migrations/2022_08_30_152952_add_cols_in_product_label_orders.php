<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInProductLabelOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_label_orders', function (Blueprint $table) {
            //
            $table->bigInteger('order_id')->nullable()->after('product_id');
            $table->bigInteger('sku_id')->nullable()->after('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_label_orders', function (Blueprint $table) {
            //
            $table->dropColumn('order_id');
            $table->dropColumn('sku_id');
        });
    }
}
