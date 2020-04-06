<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveyStatusesToSurvey extends Migration
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
            $table->bigInteger('survey_status_id')->index()->unsigned()->nullable();
            $table->foreign('survey_status_id')->references('id')->on('survey_statuses');
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
            $table->dropForeign('survey_survey_status_id_foreign');
            $table->dropColumn('survey_status_id');
        });
    }
}
