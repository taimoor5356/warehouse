<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUpcomingqtyField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::table('upcoming_inventory', function(Blueprint $table) {
            $table->renameColumn('upcoming_qty', 'qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('upcoming_inventory', function(Blueprint $table) {
            $table->renameColumn('qty', 'upcoming_qty');
        });
    }
}
