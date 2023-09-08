<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('options', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 90);
			$table->string('dev_rep', 10)->nullable();
			$table->string('field_length', 45)->default('2');
			$table->timestamps();
			$table->integer('group_id')->unsigned()->default(1)->index('fk_options_options_groups1_idx');
			$table->integer('order')->nullable()->default(0);
			$table->integer('type_id')->default(1);
			$table->integer('level')->nullable()->default(128);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('options');
	}

}
