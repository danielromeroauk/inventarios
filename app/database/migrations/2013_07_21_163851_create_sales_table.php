<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales', function(Blueprint $table)
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
				  ->onUpdate('CASCADE')
				  ->onDelete('NO ACTION');

			$table->foreign('branch_id')
				  ->references('id')->on('branches')
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
		Schema::table('sales', function(Blueprint $table)
        {
            $table->dropForeign('sales_user_id_foreign');
			$table->dropForeign('sales_branch_id_foreign');
		});

		Schema::drop('sales');
	}

}
