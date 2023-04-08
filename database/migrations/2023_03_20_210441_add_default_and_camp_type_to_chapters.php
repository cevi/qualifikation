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
        Schema::table('chapters', function (Blueprint $table) {
            //
            $table->bigInteger('camp_type_id')->index()->unsigned()->nullable();
            $table->foreign('camp_type_id')->references('id')->on('camp_types')->onDelete('cascade');
            $table->boolean('default_chapter')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapters', function (Blueprint $table) {
            //
            $table->dropForeign(['camp_type_id']);
            $table->dropColumn('camp_type_id');
            $table->dropColumn('default_chapter');
        });
    }
};
