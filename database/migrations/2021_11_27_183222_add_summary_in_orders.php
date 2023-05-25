<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSummaryInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->integer('labelqty')->nullable()->after('notes');
            $table->integer('pickqty')->nullable()->after('labelqty');
            $table->integer('packqty')->nullable()->after('pickqty');
            $table->integer('mailerqty')->nullable()->after('packqty');
            $table->integer('postageqty')->nullable()->after('mailerqty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
