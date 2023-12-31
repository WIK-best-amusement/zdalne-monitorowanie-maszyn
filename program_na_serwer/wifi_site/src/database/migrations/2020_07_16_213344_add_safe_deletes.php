<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSafeDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups_devices', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups_devices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
