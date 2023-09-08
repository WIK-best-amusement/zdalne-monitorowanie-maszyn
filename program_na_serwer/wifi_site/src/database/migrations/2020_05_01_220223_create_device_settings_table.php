<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device_settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('device_id')->unsigned()->index('fk_device_settings_devices1_idx');
			$table->integer('option_id');
			$table->string('value', 30)->nullable();
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
		Schema::drop('device_settings');
	}

}
