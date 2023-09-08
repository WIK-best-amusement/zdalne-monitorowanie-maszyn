<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('groups_users', function(Blueprint $table)
		{
			$table->foreign('group_id', 'fk_groups_users_groups1')->references('id')->on('groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('role', 'fk_groups_users_groups_roles1')->references('id')->on('groups_roles')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups_users', function(Blueprint $table)
		{
			$table->dropForeign('fk_groups_users_groups1');
			$table->dropForeign('fk_groups_users_groups_roles1');
		});
	}

}
