<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::table('survey_statuses')->insert( 
            array(
                ['id' => config('status.survey_neu'),
                'name' => 'Neu'],
                ['id' => config('status.survey_1_offen'),
                'name' => '1. Offen'],
                ['id' => config('status.survey_1_abgeschlossen'),
                'name' => '1. Abgeschlossen'],
                ['id' => config('status.survey_2_offen'),
                'name' => '2. Offen'],
                ['id' => config('status.survey_2_abgeschlossen'),
                'name' => '2. Abgeschlossen'],
                ['id' => config('status.survey_fertig'),
                'name' => 'Fertig'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_statuses');
    }
}
