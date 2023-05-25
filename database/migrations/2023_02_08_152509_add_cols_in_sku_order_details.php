<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInSkuOrderDetails extends Migration
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
            $table->bigInteger('order_details_id')->nullable()->after('product_id');
            $table->string('product_name')->nullable()->after('order_details_id');
            $table->double('pick_pack_flat_unit_cost', 8, 2)->nullable()->after('product_name');
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
            $table->dropColumn('order_details_id');
            $table->dropColumn('product_name');
            $table->dropColumn('pick_pack_flat_unit_cost');
        });
    }
}
