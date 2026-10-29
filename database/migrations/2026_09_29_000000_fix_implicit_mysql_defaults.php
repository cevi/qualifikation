<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Spalten, die der Code beim Anlegen nicht setzt (CampsController, AdminSurveysController,
     * AdminGroupsController, PermissionRoleSeeder). Bisher hat das nur funktioniert, weil MySQL
     * mit strict=false stillschweigend 0 bzw. 0000-00-00 einsetzt, oder es ist gescheitert
     * (groups.foreign_id bei leerem Formularfeld).
     */
    public function up(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->boolean('global_camp')->default(false)->change();
            $table->boolean('demo')->default(false)->change();
            $table->date('end_date')->nullable()->change();
        });
        Schema::table('surveys', function (Blueprint $table) {
            $table->boolean('demo')->default(false)->change();
            $table->text('comment')->nullable()->change();
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('foreign_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->boolean('global_camp')->change();
            $table->boolean('demo')->change();
            $table->date('end_date')->change();
        });
        Schema::table('surveys', function (Blueprint $table) {
            $table->boolean('demo')->change();
            $table->text('comment')->change();
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('foreign_id')->change();
        });
    }
};
