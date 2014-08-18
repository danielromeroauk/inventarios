<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRotationItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rotation_items', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('rotation_id')->unsigned();
			$table->integer('article_id')->unsigned();
			$table->decimal('amount', 16, 2)->unsigned();

			$table->timestamps();

			$table->foreign('rotation_id')
				  ->references('id')->on('rotations')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('article_id')
				  ->references('id')->on('articles')
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
		Schema::table('rotation_items', function(Blueprint $table)
        {
            $table->dropForeign('rotation_items_rotation_id_foreign');
			$table->dropForeign('rotation_items_article_id_foreign');
		});

		Schema::drop('rotation_items');
	}

}
