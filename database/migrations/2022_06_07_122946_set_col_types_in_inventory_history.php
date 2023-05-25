<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetColTypesInInventoryHistory extends Migration
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
            $table->bigInteger('product_id')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->bigInteger('user_id')->nullable()->change();
            $table->bigInteger('status')->nullable()->change();
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
        });
    }
}
