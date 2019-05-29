<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('user_id')
                ->nullable()
                ->comment("Владелец корзины");

            $table->boolean('notify')
                ->default(0)
                ->comment('Отправка уведомления об устаревшей корзине');

            $table->json('items')
                ->nullable()
                ->comment("Содержимое корзины");

            $table->uuid('uuid')
                ->comment("Уникальный идентификатор корзины");

            $table->float('total')
                ->default(0)
                ->comment("Итого");

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
        Schema::dropIfExists('carts');
    }
}
