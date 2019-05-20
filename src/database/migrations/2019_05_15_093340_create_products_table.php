<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')
                ->comment('Заголовок товара');

            $table->string('slug')
                ->unique()
                ->comment("Slug");

            $table->integer('category_id')
                ->comment('Категория товара');

            $table->string('short')
                ->nullable()
                ->comment("Кроткое описание");

            $table->longText('description')
                ->comment("Описание");

            $table->integer('main_image')
                ->nullable()
                ->comment("Главное изображение товара");

            $table->boolean('published')
                ->default(1)
                ->comment('Статус публикации');

            $table->char('state', 20)
                ->nullable()
                ->comment("Метки товара");

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
        Schema::dropIfExists('products');
    }
}
