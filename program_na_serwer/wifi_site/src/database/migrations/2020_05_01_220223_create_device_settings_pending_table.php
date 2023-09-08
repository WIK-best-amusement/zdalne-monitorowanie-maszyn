<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceSettingsPendingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device_settings_pending', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('device_id')->unsigned()->index('fk_device_settings_pending_devices1_idx');
			$table->integer('option_id')->index('fk_device_settings_pending_options1_idx');
			$table->string('value', 30)->nullable();
			$table->timestamps();
			$table->integer('sent_to_mqtt')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device_settings_pending');
	}

}
