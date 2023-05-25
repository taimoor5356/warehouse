<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnChargesColInMergedInvoices extends Migration
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
            $table->decimal('return_charges', 8, 2)->default('0.00')->after('pick_pack_flat_charges');
            $table->integer('return_qty')->default('0')->after('return_charges');
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
            $table->dropColumn('return_charges');
            $table->dropColumn('return_qty');
        });
    }
}
