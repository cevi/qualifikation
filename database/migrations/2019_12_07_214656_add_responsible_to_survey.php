<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponsibleToSurvey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->bigInteger('responsible_id')->index()->unsigned()->nullable();
            //
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('responsible_id')->references('id')->on('users')->onDelete('set null');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->dropForeign(['user_id']);
            $table->dropForeign(['responsible_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('responsible_id');
        });
    }
}
