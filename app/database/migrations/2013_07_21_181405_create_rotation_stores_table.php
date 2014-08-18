<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRotationStoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rotation_stores', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('rotation_id')->unsigned();
			$table->string('comments_from');
			$table->string('comments_to');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('rotation_id')
				  ->references('id')->on('rotations')
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
        Schema::table('rotation_stores', function(Blueprint $table)
        {
            $table->dropForeign('rotation_stores_user_id_foreign');
        	$table->dropForeign('rotation_stores_rotation_id_foreign');
        });

		Schema::drop('rotation_stores');
	}

}
