<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_images', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('image');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('id')
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
        Schema::table('article_images', function(Blueprint $table)
        {
            $table->dropForeign('article_images_user_id_foreign');
        	$table->dropForeign('article_images_id_foreign');
        });

		Schema::drop('article_images');
	}

}
