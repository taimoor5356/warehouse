<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColInMergedInvoices extends Migration
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
            $table->string('invoice_ids')->nullable()->after('id');
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
            $table->dropColumn('invoice_ids');
        });
    }
}
