<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsCustomerIdAndReturnOrderIdInInventoryHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('user_id')->nullable();
            $table->unsignedBigInteger('return_order_id')->after('customer_id')->nullable();
            $table->unsignedBigInteger('item_status')->after('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('return_order_id');
            $table->dropColumn('item_status');
        });
    }
}
