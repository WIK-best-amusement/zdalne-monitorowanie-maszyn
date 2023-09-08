<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupsDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('groups_devices', function(Blueprint $table)
		{
			$table->foreign('device_id', 'fk_groups_devices_devices1')->references('id')->on('devices')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('group_id', 'fk_groups_devices_groups1')->references('id')->on('groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups_devices', function(Blueprint $table)
		{
			$table->dropForeign('fk_groups_devices_devices1');
			$table->dropForeign('fk_groups_devices_groups1');
		});
	}

}
