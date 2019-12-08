<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_chapters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chapter_id')->index()->unsigned()->nullable();
            $table->bigInteger('survey_id')->index()->unsigned()->nullable();
            $table->timestamps();
        });
        Schema::table('survey_chapters', function (Blueprint $table) {
            //
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_chapters', function (Blueprint $table) {
            //
            $table->dropForeign('survey_chapters_chapter_id_foreign');
            $table->dropForeign('survey_chapters_survey_id_foreign');
        });
        Schema::dropIfExists('survey_chapters');
    }
}
