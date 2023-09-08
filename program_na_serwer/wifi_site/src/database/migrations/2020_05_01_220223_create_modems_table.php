<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modems', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('serial_number', 20)->unique('serial_UNIQUE');
			$table->integer('firmware_version')->nullable();
			$table->decimal('rssi', 4, 1)->nullable();
			$table->integer('device_group_id');
			$table->string('net_name', 45)->nullable()->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modems');
	}

}
