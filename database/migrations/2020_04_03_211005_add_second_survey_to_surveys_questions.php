<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondSurveyToSurveysQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            //
            $table->bigInteger('answer_leader_id')->index()->unsigned()->nullable();
            $table->foreign('answer_leader_id')->references('id')->on('answers');
            $table->text('comment_leader')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            //
            $table->dropForeign(['answer_leader_id']);
            $table->dropColumn('answer_leader_id');
            $table->dropColumn('comment_leader');
        });
    }
}
