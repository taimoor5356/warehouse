<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInOrderReturnDetails extends Migration
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
            $table->string('order_number')->nullable()->after('item_status');
            $table->string('name')->nullable()->after('order_number');
            $table->string('state')->nullable()->after('name');
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
            $table->dropColumn('order_number');
            $table->dropColumn('name');
            $table->dropColumn('state');
        });
    }
}
