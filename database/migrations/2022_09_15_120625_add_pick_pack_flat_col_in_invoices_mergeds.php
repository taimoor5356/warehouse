<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickPackFlatColInInvoicesMergeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices_mergeds', function (Blueprint $table) {
            //
            $table->double('pick_pack_flat_unit_cost')->default('0.00')->after('pack_unit_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices_mergeds', function (Blueprint $table) {
            //
            $table->dropColumn('pick_pack_flat_unit_cost');
        });
    }
}
