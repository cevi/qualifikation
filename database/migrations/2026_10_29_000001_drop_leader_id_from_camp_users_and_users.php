<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Die Leitenden stehen jetzt in camp_user_leader. users.leader_id war eine Kopie für den aktuellen Kurs.
     */
    public function up(): void
    {
        Schema::table('camp_users', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropIndex(['leader_id']);
            $table->dropColumn('leader_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropIndex(['leader_id']);
            $table->dropColumn('leader_id');
        });
    }

    /**
     * Spalten wieder anlegen; bei mehreren Leitenden bleibt eine (die mit der kleinsten ID).
     */
    public function down(): void
    {
        Schema::table('camp_users', function (Blueprint $table) {
            $table->bigInteger('leader_id')->index()->unsigned()->nullable();
            $table->foreign('leader_id')->references('id')->on('users');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('leader_id')->index()->unsigned()->nullable();
            $table->foreign('leader_id')->references('id')->on('users')->onDelete('set Null');
        });

        DB::table('camp_users')->update([
            'leader_id' => DB::raw('(SELECT MIN(leader_id) FROM camp_user_leader WHERE camp_user_leader.camp_user_id = camp_users.id)'),
        ]);
        DB::table('users')->update([
            'leader_id' => DB::raw('(SELECT MIN(camp_users.leader_id) FROM camp_users WHERE camp_users.user_id = users.id AND camp_users.camp_id = users.camp_id)'),
        ]);
    }
};
