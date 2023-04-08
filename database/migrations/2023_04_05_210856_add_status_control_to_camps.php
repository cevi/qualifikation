<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camps', function (Blueprint $table) {
            //
            $table->bigInteger('survey_status_id')->index()->unsigned()->nullable();
            $table->foreign('survey_status_id')->references('id')->on('survey_statuses')->nullOnDelete();
            $table->boolean('status_control')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camps', function (Blueprint $table) {
            //
            $table->dropForeign(['survey_status_id']);
            $table->dropColumn('survey_status_id');
            $table->dropColumn('status_control');
        });
    }
};
