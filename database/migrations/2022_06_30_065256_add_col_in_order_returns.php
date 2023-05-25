<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInOrderReturns extends Migration
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
            $table->double('cust_return_charges', 8,2)->default('0.00')->after('customer_id');
            $table->double('cost_of_goods', 8,2)->default('0.00')->after('cust_return_charges');
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
            $table->dropColumn('cust_return_charges');
            $table->dropColumn('cost_of_goods');
        });
    }
}
