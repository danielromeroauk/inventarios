<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstantItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instant_items', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('instant_id')->unsigned();
			$table->integer('article_id')->unsigned();
			$table->decimal('amount', 16, 2)->unsigned();

			$table->timestamps();

			$table->foreign('instant_id')
				  ->references('id')->on('instants')
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
		Schema::table('instant_items', function(Blueprint $table)
        {
            $table->dropForeign('instant_items_instant_id_foreign');
			$table->dropForeign('instant_items_article_id_foreign');
		});

		Schema::drop('instant_items');
	}

}
