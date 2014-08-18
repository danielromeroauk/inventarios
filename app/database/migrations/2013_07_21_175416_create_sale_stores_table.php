<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleStoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sale_stores', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('sale_id')->unsigned();
			$table->string('comments');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('sale_id')
				  ->references('id')->on('sales')
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
        Schema::table('sale_stores', function(Blueprint $table)
        {
            $table->dropForeign('sale_stores_user_id_foreign');
        	$table->dropForeign('sale_stores_sale_id_foreign');
        });

		Schema::drop('sale_stores');
	}

}
