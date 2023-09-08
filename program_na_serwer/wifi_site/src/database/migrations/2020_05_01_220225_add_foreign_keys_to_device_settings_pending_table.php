<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeviceSettingsPendingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('device_settings_pending', function(Blueprint $table)
		{
			$table->foreign('device_id', 'fk_device_settings_pending_devices1')->references('id')->on('devices')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('option_id', 'fk_device_settings_pending_options1')->references('id')->on('options')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('device_settings_pending', function(Blueprint $table)
		{
			$table->dropForeign('fk_device_settings_pending_devices1');
			$table->dropForeign('fk_device_settings_pending_options1');
		});
	}

}
