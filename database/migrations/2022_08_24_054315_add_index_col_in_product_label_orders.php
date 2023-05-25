<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexColInProductLabelOrders extends Migration
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
            $table->index(['customer_id']);
            $table->index(['brand_id']);
            $table->index(['product_id']);
            $table->index(['created_at']);
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
        });
    }
}
