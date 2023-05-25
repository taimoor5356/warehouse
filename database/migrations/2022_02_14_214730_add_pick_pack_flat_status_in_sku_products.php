<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickPackFlatStatusInSkuProducts extends Migration
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
            $table->integer('pick_pack_flat_status')->nullable()->after('pack')->default('0');
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
