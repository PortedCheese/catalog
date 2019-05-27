<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')
                ->comment('Заголовок категории');

            $table->string('description')
                ->nullable()
                ->comment('Описание категории');

            $table->string('slug')
                ->unique()
                ->comment('Машинное имя');

            $table->integer('parent_id')
                ->nullable()
                ->comment('Родительская категория');

            $table->integer('main_image')
                ->nullable()
                ->comment('Изображение категрии');

            $table->integer('weight')
                ->default(1)
                ->comment('Вес категории');

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
        Schema::dropIfExists('categories');
    }
}
