<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassificationToCampUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camp_users', function (Blueprint $table) {
            //
            $table->bigInteger('classification_id')->index()->unsigned()->nullable();
            $table->foreign('classification_id')->references('id')->on('classifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camp_users', function (Blueprint $table) {
            //
            $table->dropForeign(['classification_id']);
            $table->dropColumn('classification_id');
        });
    }
}
