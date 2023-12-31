<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function(Blueprint $table)
		{
			$table->string('id')->unique();
			$table->integer('user_id')->unsigned()->nullable()->index('fk_sessions_users1_idx');
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent', 65535)->nullable();
			$table->text('payload', 65535);
			$table->integer('last_activity');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}
