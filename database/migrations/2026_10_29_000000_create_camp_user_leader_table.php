<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Leitende einer Person im Kurs (CampUser::leaders). Ersetzt camp_users.leader_id, damit eine Teilnehmende Person
     * mehrere gleichgestellte Leitende haben kann. Bestehende Zuteilungen werden übernommen.
     */
    public function up(): void
    {
        Schema::create('camp_user_leader', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camp_user_id')->constrained('camp_users')->cascadeOnDelete();
            $table->foreignId('leader_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['camp_user_id', 'leader_id']);
        });

        DB::table('camp_user_leader')->insertUsing(
            ['camp_user_id', 'leader_id', 'created_at', 'updated_at'],
            DB::table('camp_users')->whereNotNull('leader_id')
                ->select('id', 'leader_id', DB::raw('CURRENT_TIMESTAMP'), DB::raw('CURRENT_TIMESTAMP'))
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_user_leader');
    }
};
