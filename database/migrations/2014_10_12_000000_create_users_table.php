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
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('role_id')->index()->unsigned()->nullable();
            $table->string('avatar')->nullable();
            $table->bigInteger('leader_id')->index()->unsigned()->nullable();
            $table->bigInteger('camp_id')->index()->unsigned()->nullable();
            $table->dateTime('password_change_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('demo')->default(false);
            $table->string('slug');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
