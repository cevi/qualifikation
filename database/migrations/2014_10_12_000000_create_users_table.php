<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->bigInteger('role_id')->index()->unsigned()->nullable();
            $table->bigInteger('is_active')->default(0);
            $table->bigInteger('leader_id')->index()->unsigned()->nullable();
            $table->bigInteger('camp_id')->index()->unsigned()->nullable();
            $table->bigInteger('survey_own_id')->index()->unsigned()->nullable();
            $table->bigInteger('survey_leader_id')->index()->unsigned()->nullable();
            $table->rememberToken();
            $table->timestamps();

            // $table->foreign('role_id')->references('id')->on('roles');
            // $table->foreign('leader_id')->references('id')->on('users');
            // $table->foreign('camp_id')->references('id')->on('camps')->onDelete('cascade');
            // $table->foreign('survey_own_id')->references('id')->on('surveys');
            // $table->foreign('survey_leader_id')->references('id')->on('surveys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
