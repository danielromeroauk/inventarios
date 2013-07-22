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
			$table->string('comments');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onDelete('NO ACTION')
				  ->onUpdate('cascade');

			$table->foreign('id')
				  ->references('id')->on('rotations')
				  ->onDelete('NO ACTION')
				  ->onUpdate('cascade');
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
        	$table->dropForeign('rotation_stores_id_foreign');
        });

		Schema::drop('rotation_stores');
	}

}
