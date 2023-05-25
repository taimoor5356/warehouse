<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidColInInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->double('paid', 15,2)->default('0.00')->after('is_paid');
            $table->double('remaining', 15,2)->default('0.00')->after('paid');
            $table->string('paid_date')->nullable()->after('remaining');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->dropColumn('paid');
            $table->dropColumn('remaining');
            $table->dropColumn('paid_date');
        });
    }
}
