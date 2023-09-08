<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeviceSettingsHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('device_settings_history', function(Blueprint $table)
		{
			$table->foreign('device_setting_id', 'fk_device_settings_history_device_settings1')->references('id')->on('device_settings')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('device_settings_history', function(Blueprint $table)
		{
			$table->dropForeign('fk_device_settings_history_device_settings1');
		});
	}

}
