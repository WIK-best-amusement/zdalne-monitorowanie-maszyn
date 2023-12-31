<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUsersRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('users_roles', function(Blueprint $table)
		{
			$table->foreign('user_id', 'fk_users_roles_users1')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_roles', function(Blueprint $table)
		{
			$table->dropForeign('fk_users_roles_users1');
		});
	}

}
