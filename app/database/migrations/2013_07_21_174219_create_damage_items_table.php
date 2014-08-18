<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('damage_items', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('damage_id')->unsigned();
			$table->integer('article_id')->unsigned();
			$table->decimal('amount', 16, 2)->unsigned();

			$table->timestamps();

			$table->foreign('damage_id')
				  ->references('id')->on('damages')
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
		Schema::table('damage_items', function(Blueprint $table)
        {
            $table->dropForeign('damage_items_damage_id_foreign');
			$table->dropForeign('damage_items_article_id_foreign');
		});

		Schema::drop('damage_items');
	}

}
