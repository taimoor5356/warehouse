<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveLabelColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
      {
          Schema::table('customers', function($table) {
             $table->dropColumn(['label']);
          });
      }

      public function down()
      {
          Schema::table('articles', function($table) {
             $table->string('label');
          });
      }
}
