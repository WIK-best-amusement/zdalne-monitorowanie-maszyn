<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeviceSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('device_settings', function(Blueprint $table)
		{
			$table->foreign('device_id', 'fk_device_settings_devices1')->references('id')->on('devices')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('device_settings', function(Blueprint $table)
		{
			$table->dropForeign('fk_device_settings_devices1');
		});
	}

}
