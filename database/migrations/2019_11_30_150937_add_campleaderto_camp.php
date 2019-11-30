<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampleadertoCamp extends Migration
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
            $table->bigInteger('user_id')->index()->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('camp_status_id')->references('id')->on('camp_statuses');
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
            $table->dropForeign('camps_user_id_foreign');
            $table->dropForeign('camps_camp_status_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
