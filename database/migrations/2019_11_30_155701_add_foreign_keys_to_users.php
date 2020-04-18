<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('leader_id')->references('id')->on('users')->onDelete('set Null');
            $table->foreign('camp_id')->references('id')->on('camps')->onDelete('set Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropForeign(['role_id']);
            $table->dropForeign(['leader_id']);
            $table->dropForeign(['camp_id']);
        });
    }
}
