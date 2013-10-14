<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticlesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function(Blueprint $table) {
            $table->engine = 'innoDB';

            $table->increments('id');
            $table->string('name')->unique();
            $table->string('unit');
            $table->decimal('cost', 16, 2)->unsigned();
            $table->decimal('price', 16, 2)->unsigned();
            $table->decimal('iva', 4, 2)->unsigned();
            $table->string('comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('articles');
    }

}
