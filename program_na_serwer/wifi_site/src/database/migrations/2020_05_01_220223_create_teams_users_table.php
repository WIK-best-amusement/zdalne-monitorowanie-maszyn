<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTeamsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('teams_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('team_id')->unsigned()->index();
			$table->integer('user_id')->unsigned()->index();
			$table->primary(['id','user_id','team_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('teams_users');
	}

}
