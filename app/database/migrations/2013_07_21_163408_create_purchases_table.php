<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchases', function(Blueprint $table)
		{
			$table->engine = 'innoDB';

			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('branch_id')->unsigned();
			$table->string('comments');
			$table->string('status');

			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')->on('users')
				  ->onDelete('NO ACTION')
				  ->onUpdate('cascade');

			$table->foreign('branch_id')
				  ->references('id')->on('branches')
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
		Schema::table('purchases', function(Blueprint $table)
        {
            $table->dropForeign('purchases_user_id_foreign');
			$table->dropForeign('purchases_branch_id_foreign');
		});

		Schema::drop('purchases');
	}

}
