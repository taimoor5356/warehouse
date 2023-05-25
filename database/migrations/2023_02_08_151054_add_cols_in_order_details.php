<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            //
            $table->string('sku_name')->nullable()->after('sku_id');
            $table->string('sku_name_id')->nullable()->after('sku_name');
            $table->double('sku_weight', 8, 2)->nullable()->after('sku_name_id');
            $table->double('sku_total_cost', 8,2 )->nullable()->after('sku_weight');
            $table->tinyInteger('sku_pick_pack_flat_status')->nullable()->after('sku_weight');
            $table->double('sku_pick_pack_flat_cost', 8, 2)->nullable()->after('sku_pick_pack_flat_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            //
            $table->dropColumn('sku_name');
            $table->dropColumn('sku_name_id');
            $table->dropColumn('sku_weight');
            $table->dropColumn('sku_total_cost');
            $table->dropColumn('sku_pick_pack_flat_status');
            $table->dropColumn('sku_pick_pack_flat_cost');
        });
    }
}
