<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('order_id')
                ->comment('Заказ к котому относится');

            $table->char('sku', 100)
                ->comment("Артикул вариации");

            $table->float('price')
                ->comment("Цена вариации");

            $table->integer('quantity')
                ->default(1)
                ->comment("Количество");

            $table->float('total')
                ->default(0)
                ->comment("Итого");

            $table->string('description')
                ->nullable()
                ->comment("Описание вариации");

            $table->string('title')
                ->comment("Заголовок товара");

            $table->integer("product_id")
                ->comment("Ссылка на товар");

            $table->integer("variation_id")
                ->comment("Id вариации");

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
        Schema::dropIfExists('order_items');
    }
}
