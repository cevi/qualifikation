<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('survey_chapter_id')->index()->unsigned()->nullable();
            $table->foreign('survey_chapter_id')->references('id')->on('survey_chapters')->onDelete('cascade');
            $table->bigInteger('question_id')->index()->unsigned()->nullable();
            $table->foreign('question_id')->references('id')->on('questions');
            $table->bigInteger('answer_first_id')->index()->unsigned()->nullable();
            $table->foreign('answer_first_id')->references('id')->on('answers');
            $table->text('comment_first')->nullable();
            $table->bigInteger('answer_second_id')->index()->unsigned()->nullable();
            $table->foreign('answer_second_id')->references('id')->on('answers');
            $table->text('comment_second')->nullable();
            $table->bigInteger('answer_leader_id')->index()->unsigned()->nullable();
            $table->foreign('answer_leader_id')->references('id')->on('answers');
            $table->text('comment_leader')->nullable();
            $table->timestamps();
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
            $table->dropForeign(['question_id']);
            $table->dropForeign(['survey_chapter_id']);
        });
        Schema::dropIfExists('survey_questions');
    }
}
