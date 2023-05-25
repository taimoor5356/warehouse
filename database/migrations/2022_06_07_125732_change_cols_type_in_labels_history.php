<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInLabelsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels_history', function (Blueprint $table) {
            //
            $table->bigInteger('customer_id')->nullable()->change();
            $table->bigInteger('brand_id')->nullable()->change();
            $table->bigInteger('user_id')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->dateTime('date')->nullable()->change();
            $table->integer('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels_history', function (Blueprint $table) {
            //
        });
    }
}
