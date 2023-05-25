<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsInOrderReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_returns', function (Blueprint $table) {
            //
            $table->integer('status')->nullable()->after('id');
            $table->string('name')->nullable()->after('status');
            $table->integer('state')->nullable()->after('name');
            $table->longText('notes')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_returns', function (Blueprint $table) {
            //
            $table->dropColumn('status');
            $table->dropColumn('name');
            $table->dropColumn('state');
            $table->dropColumn('notes');
        });
    }
}
