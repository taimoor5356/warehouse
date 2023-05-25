<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMergedInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merged_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->double('total_cost', 15,2)->default(0);
            $table->BigInteger('label_qty')->default(0);
            $table->BigInteger('pick_qty')->default(0);
            $table->BigInteger('pack_qty')->default(0);
            $table->BigInteger('mailer_qty')->default(0);
            $table->BigInteger('postage_qty')->default(0);
            $table->BigInteger('pick_pack_flat_qty')->default(0);
            $table->double('label_charges', 15, 2)->default(0);
            $table->double('pick_charges', 15, 2)->default(0);
            $table->double('pack_charges', 15, 2)->default(0);
            $table->double('mailer_charges', 15, 2)->default(0);
            $table->double('postage_charges', 15, 2)->default(0);
            $table->double('pick_pack_flat_charges', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merged_invoices');
    }
}
