<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInUpcomingInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('upcoming_inventory', function (Blueprint $table) {
            //
            $table->bigInteger('product_id')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->dateTime('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('upcoming_inventory', function (Blueprint $table) {
            //
        });
    }
}
