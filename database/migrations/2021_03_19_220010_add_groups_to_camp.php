<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupsToCamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camps', function (Blueprint $table) {
            //
            $table->bigInteger('group_id')->index()->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->integer('foreign_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camps', function (Blueprint $table) {
            //
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            $table->dropColumn('foreign_id');
        });
    }
}
