<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvNosColToMergedInvoices extends Migration
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
            $table->string('inv_nos')->nullable()->after('invoice_ids');
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
            $table->dropColumn('inv_nos');
        });
    }
}
