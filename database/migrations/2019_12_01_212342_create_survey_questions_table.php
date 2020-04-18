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
            $table->bigInteger('question_id')->index()->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
        Schema::table('survey_questions', function (Blueprint $table) {
            //
            $table->foreign('survey_chapter_id')->references('id')->on('survey_chapters')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions');
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
