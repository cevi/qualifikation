<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
                ['id' => config('status.survey_1offen'),
                'name' => '1. Selbsteinschätzung Offen'],
                ['id' => config('status.survey_2offen'),
                'name' => '2 Selbsteinschätzung Offen'],
                ['id' => config('status.survey_tnAbgeschlossen'),
                'name' => 'TN Selbsteinschätzung Abgeschlossen'],
                ['id' => config('status.survey_fertig'),
                'name' => 'Qualifikationsprozess Abgeschlossen'],
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
