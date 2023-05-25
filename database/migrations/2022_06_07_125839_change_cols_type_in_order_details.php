<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInOrderDetails extends Migration
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
            $table->bigInteger('sku_id')->nullable()->change();
            $table->bigInteger('order_id')->nullable()->change();
            $table->decimal('cost_of_good', 15,2)->default('0.00')->change();
            $table->decimal('sku_purchasing_cost', 15,2)->default('0.00')->change();
            $table->decimal('sku_selling_cost', 15,2)->default('0.00')->change();
            $table->json('service_charges_detail')->nullable()->change();
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
        });
    }
}
