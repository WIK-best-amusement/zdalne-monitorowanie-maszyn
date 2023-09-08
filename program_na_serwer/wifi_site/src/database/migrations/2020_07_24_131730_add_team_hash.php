<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeamHash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('hash')->nullable();
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->integer('team_id')->nullable();
        });

        Schema::table('modems', function (Blueprint $table) {
            $table->integer('team_id')->nullable();
        });

        DB::statement("UPDATE teams LEFT JOIN device_groups ON device_groups.user_id = teams.user_id SET hash = group_hash");
        DB::statement("UPDATE devices SET team_id = device_group_id");
        DB::statement("UPDATE modems SET team_id = device_group_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('hash');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
}
