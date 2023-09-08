<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('serial_number', 12)->comment('---MOD\\ndevice serial number,\\n32 bit unsigned integer\\n');
			$table->binary('challenge_response', 16)->default(0000000000000000)->comment('challenge reply, when modem connects we should check it against what it sends us');
			$table->integer('device_group_id')->default(0);
			$table->boolean('mode_id')->default(0)->comment('0-not configured, 1-slave, 2-master, 3- master with wifi connection(master+slave mode)');
			$table->integer('type_id')->default(0)->comment('0-not configured, 1-slave, 2-master, 3- master with wifi connection(master+slave mode)');
			$table->binary('aes_key', 16)->default(0000000000000000);
			$table->string('name', 64)->nullable()->comment('device name');
			$table->dateTime('last_seen')->nullable();
			$table->integer('modem_id')->nullable()->index('fk_devices_modems_idx');
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
		Schema::drop('devices');
	}

}
