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
                ['id' => config('status.survey_offen'),
                'name' => 'Offen'],
                ['id' => config('status.survey_abgeschlossen'),
                'name' => 'Abgeschlossen'],
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
