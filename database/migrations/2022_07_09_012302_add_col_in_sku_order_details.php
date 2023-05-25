<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInSkuOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sku_order_details', function (Blueprint $table) {
            //
            $table->bigInteger('sku_product_id')->nullable()->after('sku_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sku_order_details', function (Blueprint $table) {
            //
            $table->dropColumn('sku_product_id');
        });
    }
}
