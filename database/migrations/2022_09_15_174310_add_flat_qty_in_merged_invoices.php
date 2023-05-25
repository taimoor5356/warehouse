<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlatQtyInMergedInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merged_invoices', function (Blueprint $table) {
            //
            $table->bigInteger('flat_pick_pack_qty')->default('0')->after('pack_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merged_invoices', function (Blueprint $table) {
            //
            $table->dropColumn('flat_pick_pack_qty');
        });
    }
}
