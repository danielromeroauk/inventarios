<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRotationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rotations', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('branch_from')->unsigned();
			$table->integer('branch_to')->unsigned();
			$table->string('comments');
			$table->string('status');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('branch_from')
				  ->references('id')->on('branches')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('branch_to')
				  ->references('id')->on('branches')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rotations', function(Blueprint $table)
        {
            $table->dropForeign('rotations_user_id_foreign');
			$table->dropForeign('rotations_branch_from_foreign');
			$table->dropForeign('rotations_branch_to_foreign');
		});

		Schema::drop('rotations');
	}

}
