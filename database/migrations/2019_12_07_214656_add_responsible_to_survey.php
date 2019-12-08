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
            $table->bigInteger('survey_status_id')->index()->unsigned()->nullable();
            //
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('responsible_id')->references('id')->on('users');
            $table->foreign('survey_status_id')->references('id')->on('camp_statuses');
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
            $table->dropForeign('surveys_user_id_foreign');
            $table->dropForeign('surveys_responsible_id_foreign');
            $table->dropForeign('surveys_survey_status_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('responsible_id');
            $table->dropColumn('survey_status_id');
        });
    }
}
