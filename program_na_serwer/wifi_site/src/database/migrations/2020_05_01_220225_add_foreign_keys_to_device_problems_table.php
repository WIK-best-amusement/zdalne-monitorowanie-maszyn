<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeviceProblemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('device_problems', function(Blueprint $table)
		{
			$table->foreign('device_id', 'fk_device_problems_devices1')->references('id')->on('devices')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('device_problems', function(Blueprint $table)
		{
			$table->dropForeign('fk_device_problems_devices1');
		});
	}

}
