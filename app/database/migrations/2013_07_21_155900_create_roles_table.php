<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function(Blueprint $table)
        {
            $table->engine = 'innoDB';

            $table->increments('id');
            $table->string('name');
            $table->integer('user_id')->unsigned();
            $table->integer('branch_id')->unsigned();

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
        Schema::table('roles', function(Blueprint $table)
        {
            $table->dropForeign('roles_user_id_foreign');
            $table->dropForeign('roles_branch_id_foreign');
        });

        Schema::drop('roles');
    }

}
