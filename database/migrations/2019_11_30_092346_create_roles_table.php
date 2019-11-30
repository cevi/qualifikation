<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('is_admin')->default(0);
            $table->integer('is_campleader')->default(0);
            $table->timestamps();
        });
    }
//INSERT INTO `roles` (`id`, `name`, `is_admin`, `is_campleader`, `created_at`, `updated_at`) VALUES (NULL, 'Administrator', '1', '0', NULL, NULL), (NULL, 'Lagerleiter', '0', '1', NULL, NULL), (NULL, 'Gruppenleiter', '0', '0', NULL, NULL), (NULL, 'Teilnehmer', '0', '0', NULL, NULL);
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
