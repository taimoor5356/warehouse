<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChargesColInMergedInvoices extends Migration
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
            $table->string('label_unit_cost', 15,2)->nullable()->after('pick_pack_flat_charges');
            $table->string('pick_unit_cost', 15,2)->nullable()->after('label_unit_cost');
            $table->string('pack_unit_cost', 15,2)->nullable()->after('pick_unit_cost');
            $table->string('mailer_unit_cost', 15,2)->nullable()->after('pack_unit_cost');
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
            $table->dropColumn('label_unit_cost');
            $table->dropColumn('pick_unit_cost');
            $table->dropColumn('pack_unit_cost');
            $table->dropColumn('mailer_unit_cost');
        });
    }
}
