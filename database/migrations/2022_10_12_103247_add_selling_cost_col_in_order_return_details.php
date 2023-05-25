<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellingCostColInOrderReturnDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_return_details', function (Blueprint $table) {
            //
            $table->decimal('selling_cost', 8, 2)->default('0.00')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_return_details', function (Blueprint $table) {
            //
            $table->dropColumn('selling_cost');
        });
    }
}
