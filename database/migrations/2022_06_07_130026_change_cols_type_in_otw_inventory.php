<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInOtwInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otw_inventory', function (Blueprint $table) {
            //
            $table->bigInteger('product_id')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->dateTime('shipping_date')->nullable()->change();
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
            //
        });
    }
}
