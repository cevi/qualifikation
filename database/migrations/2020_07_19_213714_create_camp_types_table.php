<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camp_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
        });
        // Insert some stuff
        DB::table('camp_types')->insert(
           array(
               ['id' => config('status.camptype_JS1'), 'name' => 'J+S Leiter 1'],
               ['id' => config('status.camptype_JS2'), 'name' => 'J+S Leiter 2']
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
        Schema::dropIfExists('camp_types');
    }
}
