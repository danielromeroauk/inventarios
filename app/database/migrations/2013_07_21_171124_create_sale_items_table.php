<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sale_items', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('sale_id')->unsigned();
			$table->integer('article_id')->unsigned();
			$table->decimal('amount', 16, 2)->unsigned();

			$table->timestamps();

			$table->foreign('sale_id')
				  ->references('id')->on('sales')
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
		Schema::table('sale_items', function(Blueprint $table)
        {
            $table->dropForeign('sale_items_sale_id_foreign');
			$table->dropForeign('sale_items_article_id_foreign');
		});

		Schema::drop('sale_items');
	}

}
