<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceProblemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device_problems', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('device_id')->unsigned()->index('fk_device_problems_devices1_idx');
			$table->string('title', 80);
			$table->string('description', 300)->nullable();
			$table->dateTime('date');
			$table->boolean('displayed')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device_problems');
	}

}
