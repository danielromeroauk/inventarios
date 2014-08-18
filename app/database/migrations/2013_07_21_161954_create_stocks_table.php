<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stocks', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('branch_id')->unsigned();
			$table->integer('article_id')->unsigned();
			$table->decimal('stock', 16, 2)->unsigned();
			$table->decimal('minstock', 16, 2)->unsigned();

			$table->timestamps();

			$table->foreign('branch_id')
				  ->references('id')->on('branches')
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
		Schema::table('stocks', function(Blueprint $table)
        {
            $table->dropForeign('stocks_branch_id_foreign');
			$table->dropForeign('stocks_article_id_foreign');
		});

		Schema::drop('stocks');
	}

}
