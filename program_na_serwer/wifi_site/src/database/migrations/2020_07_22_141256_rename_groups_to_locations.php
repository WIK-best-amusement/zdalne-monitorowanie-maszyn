<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameGroupsToLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('groups', 'locations');

        Schema::rename('groups_devices', 'locations_devices');
        Schema::table('locations_devices', function (Blueprint $table) {
            $table->renameColumn('group_id', 'location_id');
        });

        Schema::rename('groups_roles', 'locations_roles');

        Schema::rename('groups_settlements', 'locations_settlements');
        Schema::table('locations_settlements', function (Blueprint $table) {
            $table->renameColumn('group_id', 'location_id');
        });

        Schema::rename('groups_users', 'locations_users');
        Schema::table('locations_users', function (Blueprint $table) {
            $table->renameColumn('group_id', 'location_id');
        });

        Schema::rename('groups_users_profits', 'locations_users_profits');
        Schema::table('locations_users_profits', function (Blueprint $table) {
            $table->renameColumn('group_id', 'location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
