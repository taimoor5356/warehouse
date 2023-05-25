<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceChargesToSkuProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sku_products', function (Blueprint $table) {
            //
            $table->double('label', 15, 2)->after('selling_cost')->nullable();
            $table->double('pick', 15, 2)->after('label')->nullable();
            $table->double('pack', 15, 2)->after('pick')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sku_products', function (Blueprint $table) {
            //
        });
    }
}
