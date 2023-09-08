<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionsGroupsRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('options_groups_roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('option_id')->index('fk_options_groups_roles_options1_idx');
			$table->integer('role_id')->unsigned()->index('fk_options_groups_roles_groups_roles1_idx');
			$table->boolean('can_see')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('options_groups_roles');
	}

}
