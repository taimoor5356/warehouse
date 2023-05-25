<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInInventoryHistory extends Migration
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
            $table->bigInteger('manual_add')->default(0)->after('qty');
            $table->bigInteger('manual_reduce')->default(0)->after('manual_add');
            $table->bigInteger('sales')->default(0)->after('manual_reduce');
            $table->bigInteger('total')->default(0)->after('sales');
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
            $table->dropColumn('manual_add');
            $table->dropColumn('manual_reduce');
            $table->dropColumn('sales');
            $table->dropColumn('total');
        });
    }
}
