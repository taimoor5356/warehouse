<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnItemStatusToOrderReturnDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_return_details', function (Blueprint $table) {
            $table->tinyInteger('item_status')->default(0)->after('qty')->comment('0 rpresent no status, 1 represent return, 2 represent damaged,3 represent opened');
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
            $table->dropColumn('item_status');
        });
    }
}
