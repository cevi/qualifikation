<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThirdSurveyToSurveysQuestions extends Migration
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
            $table->dropForeign(['answer_id']);
            $table->renameColumn('answer_id', 'answer_second_id');
            $table->foreign('answer_second_id')->references('id')->on('answers');
            $table->renameColumn('comment', 'comment_second');
            $table->bigInteger('answer_first_id')->index()->unsigned()->nullable();
            $table->foreign('answer_first_id')->references('id')->on('answers');
            $table->text('comment_first')->nullable();
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
            $table->dropForeign(['answer_first_id']);
            $table->dropColumn('answer_first_id');
            $table->dropColumn('comment_first');
        });
    }
}
