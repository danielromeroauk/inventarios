<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseStoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_stores', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('purchase_id')->unsigned();
			$table->string('comments');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onDelete('NO ACTION')
				  ->onUpdate('cascade');

			$table->foreign('purchase_id')
				  ->references('id')->on('purchases')
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
        Schema::table('purchase_stores', function(Blueprint $table)
        {
            $table->dropForeign('purchase_stores_user_id_foreign');
        	$table->dropForeign('purchase_stores_purchase_id_foreign');
        });

		Schema::drop('purchase_stores');
	}

}
