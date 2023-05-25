<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOtwQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otw_inventory', function(Blueprint $table) {
            $table->renameColumn('otw_qty', 'qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('otw_inventory', function(Blueprint $table) {
            $table->renameColumn('qty', 'otw_qty');
        });
    }
}
