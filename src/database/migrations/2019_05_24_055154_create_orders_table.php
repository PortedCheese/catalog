<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')
                ->nullable()
                ->comment("Пользователь оформивший заказ");

            $table->jsonb('user_data')
                ->nullable()
                ->comment("Информация о пользователе");

            $table->integer('state_id')
                ->nullable()
                ->comment('Статус заказа');

            $table->float('total')
                ->default(0)
                ->comment('Сумма заказа');

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
        Schema::dropIfExists('orders');
    }
}
