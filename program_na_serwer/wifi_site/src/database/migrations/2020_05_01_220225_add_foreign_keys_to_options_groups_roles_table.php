<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOptionsGroupsRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::disableForeignKeyConstraints();
		Schema::table('options_groups_roles', function(Blueprint $table)
		{
			$table->foreign('role_id', 'fk_options_groups_roles_groups_roles1')->references('id')->on('groups_roles')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('option_id', 'fk_options_groups_roles_options1')->references('id')->on('options')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('options_groups_roles', function(Blueprint $table)
		{
			$table->dropForeign('fk_options_groups_roles_groups_roles1');
			$table->dropForeign('fk_options_groups_roles_options1');
		});
	}

}
