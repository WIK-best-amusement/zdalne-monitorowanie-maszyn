<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTeamsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('teams_users', function(Blueprint $table)
		{
			$table->foreign('team_id', 'fk_teams_users_teams1')->references('id')->on('teams')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('user_id', 'fk_teams_users_users1')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('teams_users', function(Blueprint $table)
		{
			$table->dropForeign('fk_teams_users_teams1');
			$table->dropForeign('fk_teams_users_users1');
		});
	}

}
