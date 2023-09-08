<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionsTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('options_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->default('string');
			$table->enum('type', array('string','integer','hide|show','on|off','date','pin','lang','readonly','select','counter'));
			$table->integer('min');
			$table->integer('max');
			$table->string('values')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('options_types');
	}

}
