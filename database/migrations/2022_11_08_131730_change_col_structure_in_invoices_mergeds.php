<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColStructureInInvoicesMergeds extends Migration
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
            $table->decimal('label_unit_cost', 15,2)->nullable()->change();
            $table->decimal('pick_unit_cost', 15,2)->nullable()->change();
            $table->decimal('pack_unit_cost', 15,2)->nullable()->change();
            $table->decimal('mailer_unit_cost', 15,2)->nullable()->change();
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
        });
    }
}
