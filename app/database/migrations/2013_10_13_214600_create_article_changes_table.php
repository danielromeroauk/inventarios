<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleChangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_changes', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('article_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('log', 2000);

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onDelete('NO ACTION')
				  ->onUpdate('cascade');

			$table->foreign('article_id')
				  ->references('id')->on('articles')
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
        Schema::table('article_changes', function(Blueprint $table)
        {
            $table->dropForeign('article_changes_user_id_foreign');
        	$table->dropForeign('article_changes_id_foreign');
        });

		Schema::drop('article_changes');
	}

}
