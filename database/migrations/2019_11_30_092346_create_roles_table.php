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
            $table->integer('is_leader')->default(0);
            $table->timestamps();
        });
        // Insert some stuff
        DB::table('roles')->insert(
           array(
               ['id' => config('role_Administrator'), 'name' => 'Administrator', 'is_admin' => true, 'is_campleader' => false, 'is_leader' => false],
               ['id' => config('role_Lagerleiter'), 'name' => 'Lagerleiter', 'is_admin' => false, 'is_campleader' => true, 'is_leader' => false],
               ['id' => config('role_Gruppenleiter'), 'name' => 'Gruppenleiter', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true],
               ['id' => config('role_Teilnehmer'), 'name' => 'Teilnehmer', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => false],
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
        Schema::dropIfExists('roles');
    }
}
