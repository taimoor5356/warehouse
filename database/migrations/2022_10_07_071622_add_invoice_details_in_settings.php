<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceDetailsInSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->string('company_name')->default('warehousesystem')->after('id');
            $table->string('company_number')->default('925-918-2281')->after('company_name');
            $table->longText('company_address')->nullable()->after('company_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn('company_name');
            $table->dropColumn('company_number');
            $table->dropColumn('company_address');
        });
    }
}
