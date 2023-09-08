<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingDatesFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function(Blueprint $table)
        {
            $table->timestamps();
        });

        Schema::table('groups_devices', function(Blueprint $table)
        {
            $table->timestamps();
        });

        Schema::table('groups_settlements', function(Blueprint $table)
        {
            $table->timestamps();
        });

        Schema::table('groups_users', function(Blueprint $table)
        {
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
        Schema::table('groups', function(Blueprint $table)
        {
            $table->dropTimestamps();
        });

        Schema::table('groups_devices', function(Blueprint $table)
        {
            $table->dropTimestamps();
        });

        Schema::table('groups_settlements', function(Blueprint $table)
        {
            $table->dropTimestamps();
        });

        Schema::table('groups_users', function(Blueprint $table)
        {
            $table->dropTimestamps();
        });
    }
}
