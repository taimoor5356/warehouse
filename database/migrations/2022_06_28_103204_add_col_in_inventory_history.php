<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInInventoryHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            //
            $table->bigInteger('supplier_inventory_received')->default(0)->after('manual_add');
            $table->bigInteger('return_add')->default(0)->after('supplier_inventory_received');
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
            //
            $table->dropColumn('supplier_inventory_received');
            $table->dropColumn('return_add');
        });
    }
}
