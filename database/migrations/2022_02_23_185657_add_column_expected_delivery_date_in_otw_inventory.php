<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExpectedDeliveryDateInOtwInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otw_inventory', function (Blueprint $table) {
            $table->date('expected_delivery_date')->after('shipping_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otw_inventory', function (Blueprint $table) {
            $table->dropColumn('expected_delivery_date');
        });
    }
}
